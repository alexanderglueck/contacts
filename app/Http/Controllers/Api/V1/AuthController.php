<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\TwoFactorChallengeRequest;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use Laravel\Fortify\Fortify;

/**
 * Token-based auth for native clients (Android, iOS, anything URL-only).
 *
 * Heavy logic stays in Fortify's action classes (CreateNewUser etc.) so the
 * web (Inertia + session cookie) and mobile (Sanctum bearer token) paths
 * stay behaviorally identical without duplication. The endpoints here are
 * thin orchestration.
 */
class AuthController extends Controller
{
    /**
     * Cache TTL for the short-lived challenge token issued after a valid
     * password submission when 2FA is required. Mirrors how long Fortify's
     * web flow keeps `login.id` in the session — long enough to fish a code
     * out of an authenticator app, short enough that an intercepted token
     * is near-worthless.
     */
    private const CHALLENGE_TTL_SECONDS = 300;

    public function register(RegisterRequest $request, CreatesNewUsers $creator): JsonResponse
    {
        // Delegates to App\Actions\Fortify\CreateNewUser — same code path the
        // web POST /register hits, so team creation + TenantWasCreated event
        // fire identically here. The RegisterRequest typehint exists so
        // Scramble can document the body schema; the action validates again
        // internally to protect the web path that doesn't go through it.
        //
        // Pass `all()` rather than `validated()` — `password_confirmation`
        // isn't a top-level rule key (Laravel's `confirmed` rule resolves
        // it by convention) so `validated()` strips it, leaving the
        // action's internal Validator with nothing to compare against.
        $user = $creator->create($request->all());

        return response()->json([
            'user' => $this->serialize($user),
            'token' => $user->createToken($this->deviceName($request))->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Match Fortify's auth.failed message so any locale strings the
            // app already ships with line up.
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 2FA-enrolled users get a challenge token instead of a Sanctum token.
        // The client then POSTs the TOTP (or recovery code) plus this token to
        // /auth/two-factor/challenge to exchange it for the real token. We
        // deliberately do NOT fire the Login event here — only after the
        // challenge succeeds, matching Fortify's web flow where the guard
        // login (and its Login event) fires inside the challenge controller.
        if ($user->two_factor_confirmed_at !== null) {
            $challengeToken = Str::random(64);

            Cache::put(
                $this->challengeCacheKey($challengeToken),
                [
                    'user_id' => $user->id,
                    'device_name' => $this->deviceName($request),
                ],
                self::CHALLENGE_TTL_SECONDS,
            );

            return response()->json([
                'two_factor_required' => true,
                'challenge_token' => $challengeToken,
                'expires_in' => self::CHALLENGE_TTL_SECONDS,
            ]);
        }

        // Fire the standard Login event so existing listeners (PrimeTenantSession,
        // LogSuccessfulLogin) run consistently for mobile auth too.
        event(new Login('sanctum', $user, false));

        return response()->json([
            'user' => $this->serialize($user),
            'token' => $user->createToken($this->deviceName($request))->plainTextToken,
        ]);
    }

    public function twoFactorChallenge(
        TwoFactorChallengeRequest $request,
        TwoFactorAuthenticationProvider $provider,
    ): JsonResponse {
        $cacheKey = $this->challengeCacheKey($request->input('challenge_token'));
        $entry = Cache::get($cacheKey);

        // Treat missing/expired challenge tokens as a generic validation
        // failure on the token field rather than leaking which half went
        // wrong (token vs code).
        $user = $entry ? User::find($entry['user_id']) : null;

        if (! $user || $user->two_factor_confirmed_at === null) {
            throw ValidationException::withMessages([
                'challenge_token' => __('The challenge token is invalid or has expired. Please log in again.'),
            ]);
        }

        if ($request->filled('recovery_code')) {
            $matched = collect($user->recoveryCodes())->first(
                fn (string $candidate) => hash_equals($candidate, (string) $request->input('recovery_code')),
            );

            if (! $matched) {
                event(new TwoFactorAuthenticationFailed($user));

                throw ValidationException::withMessages([
                    'recovery_code' => __('The recovery code is invalid.'),
                ]);
            }

            // Recovery codes are single-use. `replaceRecoveryCode` rotates
            // just the consumed one and re-encrypts the rest, mirroring web.
            $user->replaceRecoveryCode($matched);
        } else {
            $valid = $provider->verify(
                Fortify::currentEncrypter()->decrypt($user->two_factor_secret),
                (string) $request->input('code'),
            );

            if (! $valid) {
                event(new TwoFactorAuthenticationFailed($user));

                throw ValidationException::withMessages([
                    'code' => __('The one-time code is invalid.'),
                ]);
            }
        }

        // Burn the challenge token on success so a stolen one can't be
        // replayed against a different code in the (rare) race window
        // before its TTL expires.
        Cache::forget($cacheKey);

        event(new ValidTwoFactorAuthenticationCodeProvided($user));
        event(new Login('sanctum', $user, false));

        // Prefer the device name the client sent on /challenge (mobile may
        // not have known its final identity at /login time), falling back
        // to whatever was captured during password submission.
        $deviceName = $request->filled('device_name')
            ? $this->deviceName($request)
            : ($entry['device_name'] ?? 'mobile');

        return response()->json([
            'user' => $this->serialize($user),
            'token' => $user->createToken($deviceName)->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->serialize($request->user()),
        ]);
    }

    private function challengeCacheKey(string $token): string
    {
        return "auth:2fa-challenge:{$token}";
    }

    private function deviceName(Request $request): string
    {
        // Clients should pass `device_name: "Alex's Pixel 8"` so the
        // resulting Sanctum token row is identifiable in the API tokens UI
        // for revocation. Falls back to a generic label if omitted.
        return $request->input('device_name', 'mobile');
    }

    private function serialize(User $user): array
    {
        $user->loadMissing('currentTeam');

        return [
            'ulid' => $user->ulid,
            'name' => $user->name,
            'email' => $user->email,
            'current_team' => $user->currentTeam ? [
                'uuid' => $user->currentTeam->uuid,
                'name' => $user->currentTeam->name,
            ] : null,
        ];
    }
}

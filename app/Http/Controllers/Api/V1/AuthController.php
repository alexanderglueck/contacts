<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

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

        // 2FA mobile flow isn't built yet — block token issuance for users
        // with confirmed 2FA so the client can't bypass it. We'll add a
        // /api/auth/two-factor/challenge endpoint when needed.
        if ($user->two_factor_confirmed_at !== null) {
            throw ValidationException::withMessages([
                'email' => __('Two-factor authentication is required on this account. Mobile 2FA login is not yet supported.'),
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

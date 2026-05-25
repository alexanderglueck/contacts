<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use Laravel\Fortify\Fortify;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * /api/v1/auth/* — register, login, me, logout. Token-based flow
 * shared with the Android client.
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function register_returns_a_sanctum_token_and_a_user_payload()
    {
        $response = $this->postJson(route('api.v1.auth.register'), [
            'name' => 'Jane Bond',
            'email' => 'jane@example.test',
            'password' => 'Sup3rSecret!',
            'password_confirmation' => 'Sup3rSecret!',
            'terms' => true,
            'device_name' => 'Pixel 8',
        ]);

        $response->assertCreated();
        $this->assertNotEmpty($response->json('token'));
        $this->assertSame('jane@example.test', $response->json('user.email'));
        $this->assertDatabaseHas('users', ['email' => 'jane@example.test']);
    }

    #[Test]
    public function login_with_correct_credentials_returns_a_token()
    {
        $user = create(User::class, ['email' => 'jane@example.test']);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'jane@example.test',
            'password' => 'password', // matches the User factory hash
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('token'));
        // serialize() returns ulid/name/email/current_team — no `id`.
        $this->assertSame($user->ulid, $response->json('user.ulid'));
        $this->assertSame('jane@example.test', $response->json('user.email'));
    }

    #[Test]
    public function login_with_wrong_password_returns_a_422_with_an_email_error()
    {
        create(User::class, ['email' => 'jane@example.test']);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'jane@example.test',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('errors'));
    }

    #[Test]
    public function login_returns_a_challenge_token_when_2fa_is_enrolled()
    {
        [$user] = $this->enroll2fa();

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk();
        $response->assertJsonMissing(['token']);
        $this->assertTrue($response->json('two_factor_required'));
        $this->assertIsString($response->json('challenge_token'));
        $this->assertSame(300, $response->json('expires_in'));
    }

    #[Test]
    public function challenge_with_a_valid_totp_returns_a_sanctum_token()
    {
        [$user, $secret] = $this->enroll2fa();
        $challenge = $this->loginAndGetChallenge($user);

        $code = app(Google2FA::class)->getCurrentOtp($secret);

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'code' => $code,
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('token'));
        $this->assertSame($user->ulid, $response->json('user.ulid'));
        // Token must actually work against an auth-guarded endpoint.
        $this->withHeader('Authorization', 'Bearer '.$response->json('token'))
            ->getJson(route('api.v1.auth.me'))
            ->assertOk();
    }

    #[Test]
    public function challenge_with_a_valid_recovery_code_burns_it()
    {
        [$user] = $this->enroll2fa();
        $recoveryCodes = $user->recoveryCodes();
        $challenge = $this->loginAndGetChallenge($user);

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'recovery_code' => $recoveryCodes[0],
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('token'));

        $remaining = $user->fresh()->recoveryCodes();
        $this->assertNotContains($recoveryCodes[0], $remaining);
        $this->assertCount(count($recoveryCodes), $remaining);
    }

    #[Test]
    public function challenge_with_an_invalid_code_returns_422_and_fires_failed_event()
    {
        Event::fake([TwoFactorAuthenticationFailed::class, ValidTwoFactorAuthenticationCodeProvided::class]);

        [$user] = $this->enroll2fa();
        $challenge = $this->loginAndGetChallenge($user);

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'code' => '000000',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('code', $response->json('errors'));
        Event::assertDispatched(TwoFactorAuthenticationFailed::class);
        Event::assertNotDispatched(ValidTwoFactorAuthenticationCodeProvided::class);
    }

    #[Test]
    public function challenge_with_an_invalid_recovery_code_returns_422()
    {
        [$user] = $this->enroll2fa();
        $challenge = $this->loginAndGetChallenge($user);

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'recovery_code' => 'definitely-not-a-real-code',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('recovery_code', $response->json('errors'));
    }

    #[Test]
    public function challenge_with_a_missing_or_expired_token_returns_422()
    {
        $this->enroll2fa();

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => 'not-a-real-token',
            'code' => '123456',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('challenge_token', $response->json('errors'));
    }

    #[Test]
    public function a_successful_challenge_burns_the_token_so_it_cannot_be_replayed()
    {
        [$user, $secret] = $this->enroll2fa();
        $challenge = $this->loginAndGetChallenge($user);

        $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'code' => app(Google2FA::class)->getCurrentOtp($secret),
        ])->assertOk();

        // Second attempt with the same challenge token must be rejected
        // as if the token never existed.
        $replay = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
            'code' => app(Google2FA::class)->getCurrentOtp($secret),
        ]);

        $replay->assertStatus(422);
        $this->assertArrayHasKey('challenge_token', $replay->json('errors'));
    }

    #[Test]
    public function challenge_requires_either_code_or_recovery_code()
    {
        [$user] = $this->enroll2fa();
        $challenge = $this->loginAndGetChallenge($user);

        $response = $this->postJson(route('api.v1.auth.two_factor.challenge'), [
            'challenge_token' => $challenge,
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('code', $response->json('errors'));
    }

    /**
     * Build a user with a confirmed 2FA secret + recovery codes. Returns
     * [$user, $plainSecret] so tests can mint TOTPs against the same secret
     * the controller will decrypt.
     */
    private function enroll2fa(): array
    {
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey();
        $recoveryCodes = collect(range(1, 8))
            ->map(fn () => \Illuminate\Support\Str::random(10).'-'.\Illuminate\Support\Str::random(10))
            ->all();

        $user = create(User::class, [
            'email' => 'jane@example.test',
            'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
            'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ]);

        return [$user, $secret];
    }

    private function loginAndGetChallenge(User $user): string
    {
        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk();
        $token = $response->json('challenge_token');
        $this->assertIsString($token);

        // Sanity: the cache entry actually got written under our key shape.
        $this->assertNotNull(Cache::get("auth:2fa-challenge:{$token}"));

        return $token;
    }

    #[Test]
    public function me_returns_the_authenticated_users_payload()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.auth.me'));

        $response->assertOk();
        $this->assertSame($user->ulid, $response->json('user.ulid'));
        $this->assertSame($user->email, $response->json('user.email'));
    }

    #[Test]
    public function me_returns_401_for_an_unauthenticated_caller()
    {
        $this->getJson(route('api.v1.auth.me'))->assertStatus(401);
    }

    #[Test]
    public function logout_deletes_the_current_access_token_row()
    {
        $user = create(User::class);
        $newToken = $user->createToken('test-device');
        $tokenId = $newToken->accessToken->id;

        $this->withHeader('Authorization', "Bearer {$newToken->plainTextToken}")
            ->postJson(route('api.v1.auth.logout'))
            ->assertOk();

        // The contract is "the token's database row is gone after logout".
        // We assert that directly rather than replaying the bearer in the
        // same test process — Sanctum's per-request resolution caches
        // within Laravel's TestCase boundary in ways that mask deletion.
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }
}

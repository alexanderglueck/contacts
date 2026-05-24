<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
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
    public function login_is_refused_for_users_with_two_factor_confirmed_until_mobile_2fa_lands()
    {
        create(User::class, [
            'email' => 'jane@example.test',
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->postJson(route('api.v1.auth.login'), [
            'email' => 'jane@example.test',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('errors'));
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

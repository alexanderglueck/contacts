<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasskeyManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registering_a_passkey_requires_a_confirmed_password()
    {
        $user = create(User::class);

        $response = $this->actingAs($user)
            ->getJson(route('passkey.registration-options'));

        $response->assertStatus(423);
    }

    #[Test]
    public function registration_options_are_served_once_the_password_is_confirmed()
    {
        $user = create(User::class);

        $this->actingAs($user)
            ->postJson(route('password.confirm.store'), ['password' => 'password'])
            ->assertStatus(201);

        $response = $this->actingAs($user)
            ->getJson(route('passkey.registration-options'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['options']);
    }

    #[Test]
    public function confirming_with_the_wrong_password_leaves_passkey_management_locked()
    {
        $user = create(User::class);

        $this->actingAs($user)
            ->postJson(route('password.confirm.store'), ['password' => 'not-the-password'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->actingAs($user)
            ->getJson(route('passkey.registration-options'))
            ->assertStatus(423);
    }

    #[Test]
    public function the_password_confirmation_status_endpoint_reports_the_current_state()
    {
        $user = create(User::class);

        $this->actingAs($user)
            ->getJson(route('password.confirmation'))
            ->assertStatus(200)
            ->assertJson(['confirmed' => false]);

        $this->actingAs($user)
            ->postJson(route('password.confirm.store'), ['password' => 'password'])
            ->assertStatus(201);

        $this->actingAs($user)
            ->getJson(route('password.confirmation'))
            ->assertStatus(200)
            ->assertJson(['confirmed' => true]);
    }
}

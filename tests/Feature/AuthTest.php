<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_works()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    #[Test]
    public function a_guest_can_login_with_correct_credentials()
    {
        $user = create(User::class);

        $response = $this->post(route('login.check'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertSessionMissing('errors');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_guest_cannot_login_with_incorrect_credentials()
    {
        $user = create(User::class);

        $response = $this->post(route('login.check'), [
            'email' => $user->email,
            'password' => 'invalid'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function a_user_can_logout()
    {
        $user = create(User::class);

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertStatus(302);
        $this->assertGuest();
    }
}

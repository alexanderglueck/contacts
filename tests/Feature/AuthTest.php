<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function login_page_works()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /** @test */
    public function a_guest_can_login_with_correct_credentials()
    {
        $user = create(User::class);

        $response = $this->post(route('login.check'), [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertSessionMissing('errors');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
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

    /** @test */
    public function a_user_can_logout()
    {
        $user = create(User::class);

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertStatus(302);
        $this->assertGuest();
    }
}

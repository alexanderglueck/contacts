<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetOptOutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function password_reset_email_is_sent_when_no_opt_out_is_set()
    {
        Notification::fake();

        $user = create(User::class, ['password_reset_disabled' => false]);

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    #[Test]
    public function password_reset_email_is_suppressed_when_user_flag_is_set()
    {
        Notification::fake();

        $user = create(User::class, ['password_reset_disabled' => true]);

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertNothingSentTo($user);
    }

    #[Test]
    public function password_reset_email_is_suppressed_when_a_team_flag_is_set()
    {
        Notification::fake();

        $user = create(User::class, ['password_reset_disabled' => false]);
        $team = create(Team::class, [
            'owner_id' => $user->id,
            'password_reset_disabled' => true,
        ]);
        $user->attachTeam($team->id);

        $this->post(route('password.email'), ['email' => $user->email]);

        // Strictest setting wins — team override blocks the notification
        // even though the user's own flag is false.
        Notification::assertNothingSentTo($user);
    }

    #[Test]
    public function the_broker_still_returns_a_normal_response_so_emails_arent_enumerable()
    {
        Notification::fake();

        $user = create(User::class, ['password_reset_disabled' => true]);

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertSessionHasNoErrors();
        Notification::assertNothingSentTo($user);
    }
}

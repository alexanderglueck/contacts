<?php

namespace App\Console\Commands\Notification;

use App\Models\User;
use App\Notifications\WeeksDates;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SendWeeklyEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email with all upcoming contact dates to all users';

    protected WeeksDates $upcomingDates;

    /**
     * Create a new command instance.
     */
    public function __construct(WeeksDates $weeksDates)
    {
        parent::__construct();
        $this->upcomingDates = $weeksDates;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (User::all() as $user) {
            $settings = $user->notificationSettings();

            if ( ! $settings->send_weekly && ! $settings->send_weekly_push) {
                continue;
            }

            // Scope the tenant global scope to this user's team.
            Auth::setUser($user);

            $user->notify($this->upcomingDates);
        }

        Auth::forgetUser();

        return 0;
    }
}

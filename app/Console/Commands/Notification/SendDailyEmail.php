<?php

namespace App\Console\Commands\Notification;

use App\Models\User;
use App\Notifications\TodaysDates;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SendDailyEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends an email with today\'s contact dates to all users';

    protected TodaysDates $todaysDates;

    /**
     * Create a new command instance.
     */
    public function __construct(TodaysDates $todaysDates)
    {
        parent::__construct();
        $this->todaysDates = $todaysDates;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (User::all() as $user) {
            $settings = $user->notificationSettings();

            // Mail and push are independent — notify if either is enabled and
            // let the notification's via() pick the channels.
            if ( ! $settings->send_daily && ! $settings->send_daily_push) {
                continue;
            }

            // Set the auth context so the tenant global scope (and the events
            // helper) only sees this user's team — otherwise every user would
            // be notified about every team's events.
            Auth::setUser($user);

            $user->notify($this->todaysDates);
        }

        Auth::forgetUser();

        return 0;
    }
}

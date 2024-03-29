<?php

namespace App\Console\Commands\Notification;

use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\WeeksDates;

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
            if ( ! $user->notificationSettings()->send_weekly) {
                continue;
            }

            $user->notify($this->upcomingDates);
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands\Notification;

use App\Models\User;
use Illuminate\Console\Command;
use App\Notifications\TodaysDates;

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
            if ( ! $user->notificationSettings()->send_daily) {
                continue;
            }

            $user->notify($this->todaysDates);
        }

        return 0;
    }
}

<?php

namespace App\Console;

use DateTime;
use DateInterval;
use App\Models\ContactDate;
use App\Console\Commands\SendDailyEmail;
use App\Console\Commands\SendWeeklyEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendWeeklyEmail::class,
        SendDailyEmail::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(SendDailyEmail::class)
            ->dailyAt('06:00')
            ->timezone('Europe/Vienna')
            ->when(function () {
                // someone has birthday on this day
                $events = ContactDate::datesOnDate(new \DateTime());

                return count($events) > 0;
            });

        $schedule->command(SendWeeklyEmail::class)
            ->mondays()
            ->at('06:00')
            ->timezone('Europe/Vienna')
            ->when(function () {

                // Start and end day before the interval is added
                $startDate = DateTime::createFromFormat('Ymd', date('Ymd'));
                $endDate = DateTime::createFromFormat('Ymd', date('Ymd'));

                // Date Intervals
                $oneWeek = DateInterval::createFromDateString('1 week');
                $twoWeeks = DateInterval::createFromDateString('2 weeks');
                $oneDay = DateInterval::createFromDateString('1 day');

                // Add one week to get the events for this week
                $endDate->add($oneWeek)->sub($oneDay);

                $contactDatesThisWeek = ContactDate::datesInRange($startDate, $endDate);

                // Reset $endDate because one week was added
                $endDate = DateTime::createFromFormat('Ymd', date('Ymd'));

                // Add the intervals to the dates
                $startDate->add($oneWeek);
                $endDate->add($twoWeeks)->sub($oneDay);

                $contactDatesNextWeek = ContactDate::datesInRange($startDate, $endDate);

                return count($contactDatesNextWeek) > 0 || count($contactDatesThisWeek) > 0;
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

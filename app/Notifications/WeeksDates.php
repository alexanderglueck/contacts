<?php

namespace App\Notifications;

use App\Models\ContactDate;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WeeksDates extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Start and end day before the interval is added
        $startDate = DateTime::createFromFormat('Ymd', date("Ymd"));
        $endDate = DateTime::createFromFormat('Ymd', date("Ymd"));

        // Date Intervals
        $oneWeek = DateInterval::createFromDateString('1 week');
        $twoWeeks = DateInterval::createFromDateString('2 weeks');
        $oneDay = DateInterval::createFromDateString('1 day');

        $mailMessage = (new MailMessage)
            ->from('service@gdev.at', env('app.name'))
            ->subject('Bevorstehende Ereignisse');

        // events this week
        $endDate->add($oneWeek)->sub($oneDay);

        $events = ContactDate::datesInRange($startDate, $endDate);

        $this->buildMail(
            $mailMessage,
            $events,
            $startDate->format('Y'),
            $endDate->format('Y'),
            $startDate,
            $endDate,
            true
        );

        // Reset end date (we added one week above)
        $endDate = DateTime::createFromFormat('Ymd', date("Ymd"));

        // Add the intervals to the dates
        $startDate->add($oneWeek);
        $endDate->add($twoWeeks)->sub($oneDay);

        $fromYear = $startDate->format('Y');
        $toYear = $endDate->format('Y');

        $events = ContactDate::datesInRange($startDate, $endDate);

        $this->buildMail(
            $mailMessage,
            $events,
            $fromYear,
            $toYear,
            $startDate,
            $endDate,
            false
        );


        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    private function buildMail($mailMessage, $events, $fromYear, $toYear, $startDate, $endDate, $thisWeek = true)
    {
        if (count($events) > 0) {
            if (count($events) > 1) {
                if ($thisWeek) {
                    $mailMessage->line('Diese Woche stehen diese Ereignisse bevor: ');
                }else{
                    $mailMessage->line('Nächste Woche stehen diese Ereignisse bevor: ');
                }
            } else {
                if($thisWeek) {
                    $mailMessage->line('Diese Woche steht dieses Ereignis bevor: ');
                } else {
                    $mailMessage->line('Nächste Woche steht dieses Ereignis bevor: ');
                }
            }

            foreach ($events as $event) {

                if ($fromYear < $toYear) {

                    /**
                     * If the concatenated event date with the from year is greater than the raw from date, then the event is in the old year.
                     * Example:
                     *   2016 12 27 > 2016 12 26 && 2016 12 27 < 2017 02 06
                     *      from case true
                     *      event from the old year
                     *   2016 01 01 > 2016 12 26 && 2016 01 01 < 2017 02 06
                     *      from case false
                     *      event from the new year
                     */
                    if (intval($fromYear . $event->format('md')) > intval($startDate->format('Ymd')) && intval($fromYear . $event->format('md')) < intval($endDate->format('Ymd'))) {
                        $fromCase = true;
                    } else {
                        $fromCase = false;
                    }
                } else {
                    $fromCase = true;
                }

                if ($fromCase) {
                    $mailMessage->line($event->contact->fullname . ' - ' . $event->getCalculatedName($fromYear) . ' - ' . $event->formattedDate);
                } else {
                    $mailMessage->line($event->contact->fullname . ' - ' . $event->getCalculatedName($toYear) . ' - ' . $event->formattedDate);
                }

            }
        } else {
            if($thisWeek) {
                $mailMessage->line('Diese Woche steht kein Ereignis bevor.');
            }else {
                $mailMessage->line('Nächste Woche steht kein Ereignis bevor.');
            }
        }
    }
}

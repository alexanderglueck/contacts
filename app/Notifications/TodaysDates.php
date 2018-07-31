<?php

namespace App\Notifications;

use App\Models\ContactDate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TodaysDates extends Notification
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
        $events = ContactDate::datesOnDate(new \DateTime());

        $mailMessage = (new MailMessage)
            ->from('service@gdev.at', config('app.name'))
            ->subject('Heutige Ereignisse');

        if (count($events) > 0) {
            if (count($events) > 1) {
                $mailMessage->line('Heute sind diese besonderen Ereignisse: ');
            } else {
                $mailMessage->line('Heute ist dieses besondere Ereignis: ');
            }

            foreach ($events as $event) {
                $mailMessage->line($event->contact->fullname . ' - ' . $event->getCalculatedName(date('Y')) . ' - ' . $event->formattedDate);
            }
        } else {
            $mailMessage->line('Heute ist kein besonderes Ereignis.');
        }

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
}

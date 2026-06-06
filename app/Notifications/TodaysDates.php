<?php

namespace App\Notifications;

use App\Notifications\Channels\FcmChannel;
use App\Services\UpcomingEvents;
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
        $settings = $notifiable->notificationSettings();

        $channels = [];

        if ($settings->send_daily) {
            $channels[] = 'mail';
        }

        if ($settings->send_daily_push) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * Low-priority push summary of today's events.
     *
     * @return array<string, mixed>
     */
    public function toFcm($notifiable): array
    {
        $events = UpcomingEvents::eventsOnDate(new \DateTime());
        $count = $events->count();

        if ($count === 0) {
            $body = 'Heute ist kein besonderes Ereignis.';
        } else {
            $names = $events->map(fn ($e) => $e->fullname())->implode(', ');
            $body = $count === 1 ? $names : $count.' Ereignisse: '.$names;
        }

        return [
            'title' => 'Heutige Ereignisse',
            'body' => $body,
            'data' => ['type' => 'daily'],
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $year = (int) date('Y');
        $events = UpcomingEvents::eventsOnDate(new \DateTime());

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
                $mailMessage->line($event->fullname() . ' - ' . $event->label($year) . ' - ' . $event->formatted());
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

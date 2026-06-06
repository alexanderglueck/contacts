<?php

namespace App\Notifications;

use App\Models\Contact;
use App\Models\ContactDate;
use App\Notifications\Channels\FcmChannel;
use App\Services\UpcomingEvents;
use DateInterval;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeksDates extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        $settings = $notifiable->notificationSettings();

        $channels = [];

        if ($settings->send_weekly) {
            $channels[] = 'mail';
        }

        if ($settings->send_weekly_push) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * Low-priority push summary of the next two weeks' events.
     *
     * @return array<string, mixed>
     */
    public function toFcm($notifiable): array
    {
        $start = DateTime::createFromFormat('Ymd', date('Ymd'));
        $end = DateTime::createFromFormat('Ymd', date('Ymd'))
            ->add(DateInterval::createFromDateString('2 weeks'))
            ->sub(DateInterval::createFromDateString('1 day'));

        $count = UpcomingEvents::eventsInRange($start, $end)->count();

        $body = $count === 1
            ? 'In den nächsten zwei Wochen steht 1 Ereignis bevor.'
            : 'In den nächsten zwei Wochen stehen '.$count.' Ereignisse bevor.';

        return [
            'title' => 'Bevorstehende Ereignisse',
            'body' => $body,
            'data' => ['type' => 'weekly'],
        ];
    }

    public function toMail($notifiable)
    {
        $oneWeek = DateInterval::createFromDateString('1 week');
        $twoWeeks = DateInterval::createFromDateString('2 weeks');
        $oneDay = DateInterval::createFromDateString('1 day');

        $mailMessage = (new MailMessage)
            ->from('service@gdev.at', config('app.name'))
            ->subject('Bevorstehende Ereignisse');

        // ───── This week: today … today + 6 days ─────
        $thisWeekStart = DateTime::createFromFormat('Ymd', date('Ymd'));
        $thisWeekEnd = DateTime::createFromFormat('Ymd', date('Ymd'))
            ->add($oneWeek)
            ->sub($oneDay);

        $this->buildSection(
            $mailMessage,
            ContactDate::datesInRange($thisWeekStart, $thisWeekEnd),
            $this->loadBirthdays($thisWeekStart, $thisWeekEnd),
            Contact::diedDatesInRange($thisWeekStart, $thisWeekEnd),
            $thisWeekStart,
            $thisWeekEnd,
            isThisWeek: true,
            includeGiftIdeas: false,
        );

        // ───── Next week: today + 7 … today + 13 days ─────
        // (the "1 week before" gift-idea reminder window)
        $nextWeekStart = DateTime::createFromFormat('Ymd', date('Ymd'))->add($oneWeek);
        $nextWeekEnd = DateTime::createFromFormat('Ymd', date('Ymd'))->add($twoWeeks)->sub($oneDay);

        $this->buildSection(
            $mailMessage,
            ContactDate::datesInRange($nextWeekStart, $nextWeekEnd),
            $this->loadBirthdays($nextWeekStart, $nextWeekEnd),
            Contact::diedDatesInRange($nextWeekStart, $nextWeekEnd),
            $nextWeekStart,
            $nextWeekEnd,
            isThisWeek: false,
            includeGiftIdeas: true,
        );

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return [];
    }

    /**
     * Birthdays whose month-day falls in the given range, eager-loading
     * giftIdeas so the next-week section can list them without N+1.
     */
    private function loadBirthdays(DateTime $start, DateTime $end)
    {
        return Contact::datesInRange($start, $end)->load('giftIdeas');
    }

    private function buildSection(
        MailMessage $mailMessage,
        $contactDates,
        $birthdays,
        $memorials,
        DateTime $startDate,
        DateTime $endDate,
        bool $isThisWeek,
        bool $includeGiftIdeas,
    ): void {
        $totalEvents = count($contactDates) + count($birthdays) + count($memorials);

        if ($totalEvents === 0) {
            $mailMessage->line($isThisWeek
                ? 'Diese Woche steht kein Ereignis bevor.'
                : 'Nächste Woche steht kein Ereignis bevor.');

            return;
        }

        if ($totalEvents > 1) {
            $mailMessage->line($isThisWeek
                ? 'Diese Woche stehen diese Ereignisse bevor:'
                : 'Nächste Woche stehen diese Ereignisse bevor:');
        } else {
            $mailMessage->line($isThisWeek
                ? 'Diese Woche steht dieses Ereignis bevor:'
                : 'Nächste Woche steht dieses Ereignis bevor:');
        }

        $fromYear = (int) $startDate->format('Y');
        $toYear = (int) $endDate->format('Y');

        foreach ($contactDates as $event) {
            $year = $this->resolveEventYear($event->date, $startDate, $endDate, $fromYear, $toYear);
            $mailMessage->line(
                $event->contact->fullname.' - '.$event->getCalculatedName($year).' - '.$event->formattedDate,
            );
        }

        foreach ($birthdays as $contact) {
            $year = $this->resolveEventYear($contact->date_of_birth, $startDate, $endDate, $fromYear, $toYear);
            // getCalculatedName returns e.g. "31. Geburtstag\nFullname";
            // peel the name off so we can build a single line ourselves.
            $title = trim(str_replace($contact->fullname, '', $contact->getCalculatedName($year)));
            $mailMessage->line(
                $contact->fullname.' - '.$title.' - '.$contact->formatted_date_of_birth,
            );

            if ($includeGiftIdeas && $contact->giftIdeas->isNotEmpty()) {
                $mailMessage->line('Geschenkideen:');
                foreach ($contact->giftIdeas as $idea) {
                    $line = '• '.$idea->name;
                    if ($idea->description) {
                        $line .= ' — '.$idea->description;
                    }
                    if ($idea->url) {
                        $line .= ' ('.$idea->url.')';
                    }
                    $mailMessage->line($line);
                }
            }
        }

        foreach ($memorials as $contact) {
            $year = $this->resolveEventYear($contact->died_at, $startDate, $endDate, $fromYear, $toYear);
            $title = trim(str_replace($contact->fullname, '', $contact->getDeathCalculatedName($year)));
            $mailMessage->line(
                $contact->fullname.' - '.$title.' - '.$contact->formatted_died_at,
            );
        }
    }

    /**
     * Pick which calendar year to display the recurring event in. When the
     * range spans a year boundary (Dec → Jan), an event with an early-year
     * MMDD belongs in the to-year; otherwise it sits in the from-year.
     */
    private function resolveEventYear(
        string $rawDate, DateTime $startDate, DateTime $endDate, int $fromYear, int $toYear,
    ): int {
        if ($fromYear === $toYear) {
            return $fromYear;
        }

        $eventDate = date_create_from_format('Y-m-d H:i:s', $rawDate)
            ?: date_create_from_format('Y-m-d', $rawDate);

        $mmdd = $eventDate ? $eventDate->format('md') : '';
        $stamp = intval($fromYear.$mmdd);

        return $stamp >= intval($startDate->format('Ymd'))
            && $stamp <= intval($endDate->format('Ymd'))
            ? $fromYear
            : $toYear;
    }
}

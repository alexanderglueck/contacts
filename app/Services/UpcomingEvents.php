<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactDate;
use DateTime;
use DateTimeInterface;
use Illuminate\Support\Collection;

/**
 * Single source of truth for "what recurring events fall on a day / in a
 * range". Merges contact birthdays (Contact::$date_of_birth) with important
 * dates (ContactDate rows) so every surface — dashboard, daily/weekly mail,
 * push notifications and the iCal feed — shows a consistent set.
 *
 * Both underlying queries route through the tenant-scoped Contact model, so
 * results are automatically limited to the authenticated user's team.
 */
class UpcomingEvents
{
    /**
     * Birthdays and important dates whose month-day matches $date.
     *
     * @return Collection<int, UpcomingEvent>
     */
    public static function eventsOnDate(DateTimeInterface $date): Collection
    {
        $on = $date instanceof DateTime ? $date : DateTime::createFromInterface($date);

        $dates = ContactDate::datesOnDate($on)
            ->map(fn (ContactDate $cd) => UpcomingEvent::fromContactDate($cd));

        $birthdays = Contact::datesInRange($date, $date)
            ->map(fn (Contact $c) => UpcomingEvent::fromContact($c));

        return $dates->concat($birthdays)->values();
    }

    /**
     * Birthdays and important dates whose month-day falls within
     * [$start, $end] (handles ranges that cross a year boundary).
     *
     * @return Collection<int, UpcomingEvent>
     */
    public static function eventsInRange(DateTimeInterface $start, DateTimeInterface $end): Collection
    {
        $dates = ContactDate::datesInRange($start, $end)
            ->map(fn (ContactDate $cd) => UpcomingEvent::fromContactDate($cd));

        $birthdays = Contact::datesInRange($start, $end)
            ->map(fn (Contact $c) => UpcomingEvent::fromContact($c));

        return $dates->concat($birthdays)->values();
    }

    /**
     * A single contact's events (birthday + important dates) whose month-day
     * matches $date. Used by the contact page's "today" reminder banner.
     *
     * @return Collection<int, UpcomingEvent>
     */
    public static function eventsForContactOnDate(Contact $contact, DateTimeInterface $date): Collection
    {
        $md = $date->format('md');
        $events = collect();

        if ($contact->date_of_birth) {
            $dob = date_create_from_format('Y-m-d', $contact->date_of_birth);

            if ($dob && $dob->format('md') === $md) {
                $events->push(UpcomingEvent::fromContact($contact));
            }
        }

        foreach ($contact->dates as $contactDate) {
            $cdDate = date_create_from_format('Y-m-d H:i:s', $contactDate->date)
                ?: date_create_from_format('Y-m-d', $contactDate->date);

            if ($cdDate && $cdDate->format('md') === $md) {
                // Avoid a re-query for the (already known) contact.
                $contactDate->setRelation('contact', $contact);
                $events->push(UpcomingEvent::fromContactDate($contactDate));
            }
        }

        return $events->values();
    }
}

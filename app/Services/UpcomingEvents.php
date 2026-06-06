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
}

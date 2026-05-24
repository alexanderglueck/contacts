<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use DateTime;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const UPCOMING_DAYS = 7;
    private const UPCOMING_LIMIT = 3;

    public function index(): Response
    {
        return Inertia::render('Home', [
            'todaysBirthdays' => $this->birthdaysToday(),
            'upcomingBirthdays' => $this->upcomingBirthdays(),
        ]);
    }

    /**
     * Contacts whose birthday is today. Always returned in full (no limit) —
     * there are unlikely to be more than a handful per day in a personal
     * contact book, and the dashboard highlights them in a prominent banner.
     *
     * @return array<int, array<string, mixed>>
     */
    private function birthdaysToday(): array
    {
        $today = new DateTime('today');

        return Contact::datesInRange($today, $today)
            ->map(fn (Contact $c) => $this->serialize($c, $today, 0))
            ->values()
            ->toArray();
    }

    /**
     * Birthdays in the next UPCOMING_DAYS, sorted by days_until ascending,
     * capped at UPCOMING_LIMIT. Excludes today (covered separately above).
     *
     * @return array<int, array<string, mixed>>
     */
    private function upcomingBirthdays(): array
    {
        $today = new DateTime('today');
        $tomorrow = (clone $today)->modify('+1 day');
        $end = (clone $today)->modify('+'.self::UPCOMING_DAYS.' days');

        return Contact::datesInRange($tomorrow, $end)
            ->map(function (Contact $c) use ($today) {
                $occurrence = $this->resolveOccurrence($c, $today);
                $daysUntil = (int) $today->diff($occurrence)->format('%r%a');

                return $this->serialize($c, $today, $daysUntil, $occurrence);
            })
            ->filter(fn ($item) => $item['days_until'] >= 1
                && $item['days_until'] <= self::UPCOMING_DAYS)
            ->sortBy('days_until')
            ->take(self::UPCOMING_LIMIT)
            ->values()
            ->toArray();
    }

    /**
     * Resolve a contact's birthday to the next concrete occurrence on or
     * after $today, accounting for leap-day fallback (Feb 29 → Feb 28 in
     * non-leap years).
     */
    private function resolveOccurrence(Contact $contact, DateTime $today): DateTime
    {
        $dob = date_create_from_format('Y-m-d', $contact->date_of_birth);
        $year = (int) $today->format('Y');

        $occurrence = DateTime::createFromFormat('Y-m-d', $year.'-'.$dob->format('m-d'))
            ?: DateTime::createFromFormat('Y-m-d', $year.'-02-28');

        if ($occurrence < $today) {
            $year++;
            $occurrence = DateTime::createFromFormat('Y-m-d', $year.'-'.$dob->format('m-d'))
                ?: DateTime::createFromFormat('Y-m-d', $year.'-02-28');
        }

        return $occurrence;
    }

    private function serialize(Contact $contact, DateTime $today, int $daysUntil, ?DateTime $occurrence = null): array
    {
        $occurrence ??= $this->resolveOccurrence($contact, $today);
        $dob = date_create_from_format('Y-m-d', $contact->date_of_birth);

        return [
            'ulid' => $contact->ulid,
            'fullname' => $contact->fullname,
            'date' => $occurrence->format('Y-m-d'),
            'days_until' => $daysUntil,
            'is_today' => $daysUntil === 0,
            'turning' => (int) $occurrence->format('Y') - (int) $dob->format('Y'),
        ];
    }
}

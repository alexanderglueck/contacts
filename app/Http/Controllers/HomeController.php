<?php

namespace App\Http\Controllers;

use App\Services\UpcomingEvent;
use App\Services\UpcomingEvents;
use DateTime;
use DateTimeInterface;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const UPCOMING_DAYS = 7;
    private const UPCOMING_LIMIT = 5;

    public function index(): Response
    {
        return Inertia::render('Home', [
            'todaysEvents' => $this->eventsToday(),
            'upcomingEvents' => $this->upcomingEvents(),
        ]);
    }

    /**
     * Birthdays and important dates happening today. Always returned in full
     * (no limit) — the dashboard highlights them in a prominent banner.
     *
     * @return array<int, array<string, mixed>>
     */
    private function eventsToday(): array
    {
        $today = new DateTime('today');

        return UpcomingEvents::eventsOnDate($today)
            ->map(fn (UpcomingEvent $e) => $this->serialize($e, $today, 0))
            ->values()
            ->toArray();
    }

    /**
     * Events in the next UPCOMING_DAYS, sorted by days_until ascending, capped
     * at UPCOMING_LIMIT. Excludes today (covered separately above).
     *
     * @return array<int, array<string, mixed>>
     */
    private function upcomingEvents(): array
    {
        $today = new DateTime('today');
        $tomorrow = (clone $today)->modify('+1 day');
        $end = (clone $today)->modify('+'.self::UPCOMING_DAYS.' days');

        return UpcomingEvents::eventsInRange($tomorrow, $end)
            ->map(function (UpcomingEvent $e) use ($today) {
                $occurrence = $this->resolveOccurrence($e, $today);
                $daysUntil = (int) $today->diff($occurrence)->format('%r%a');

                return $this->serialize($e, $today, $daysUntil, $occurrence);
            })
            ->filter(fn ($item) => $item['days_until'] >= 1
                && $item['days_until'] <= self::UPCOMING_DAYS)
            ->sortBy('days_until')
            ->take(self::UPCOMING_LIMIT)
            ->values()
            ->toArray();
    }

    /**
     * Resolve an event to its next concrete occurrence on or after $today,
     * accounting for leap-day fallback (Feb 29 → Feb 28 in non-leap years).
     */
    private function resolveOccurrence(UpcomingEvent $event, DateTime $today): DateTime
    {
        $source = $event->date;
        $year = (int) $today->format('Y');

        $occurrence = $this->buildOccurrence($year, $source);

        if ($occurrence < $today) {
            $occurrence = $this->buildOccurrence($year + 1, $source);
        }

        return $occurrence;
    }

    private function buildOccurrence(int $year, DateTimeInterface $source): DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $year.'-'.$source->format('m-d'))
            ?: DateTime::createFromFormat('Y-m-d', $year.'-02-28');
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(UpcomingEvent $event, DateTime $today, int $daysUntil, ?DateTime $occurrence = null): array
    {
        $occurrence ??= $this->resolveOccurrence($event, $today);
        $occurrenceYear = (int) $occurrence->format('Y');

        return [
            'type' => $event->type,
            'ulid' => $event->contact->ulid,
            'fullname' => $event->fullname(),
            'label' => $event->label($occurrenceYear),
            'date' => $occurrence->format('Y-m-d'),
            'days_until' => $daysUntil,
            'is_today' => $daysUntil === 0,
            'turning' => $event->isBirthday()
                ? $occurrenceYear - (int) $event->date->format('Y')
                : null,
        ];
    }
}

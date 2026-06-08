<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\CalendarController as WebCalendarController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CalendarEventsRequest;
use App\Models\Contact;
use App\Models\ContactDate;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected ?string $accessEntity = 'calendar';

    private const UPCOMING_DAYS = 7;

    /**
     * Return all calendar events (contact birthdays + ContactDate rows) that
     * fall in the requested window. Mirrors the web CalendarController::events
     * endpoint that FullCalendar consumes, but with an API-friendly shape:
     * a unified event object with explicit `type`, `contact`, and `years`
     * fields so the mobile client doesn't have to parse titles.
     *
     * The window is recurring-event aware — a person born in 1980 with a
     * birthday on 03-15 will appear in every year's window that covers
     * March 15, with `years` reflecting the age in the target year.
     */
    public function events(CalendarEventsRequest $request): JsonResponse
    {
        $this->can('view');

        $from = DateTime::createFromFormat('Y-m-d', $request->input('from'));
        $to = DateTime::createFromFormat('Y-m-d', $request->input('to'));
        $fromYear = (int) $from->format('Y');
        $toYear = (int) $to->format('Y');

        $events = [];

        foreach (ContactDate::datesInRange($from, $to) as $date) {
            $eventDate = date_create_from_format('Y-m-d H:i:s', $date->date);
            $this->addEvent($events, 'date', $date, $eventDate, $from, $to, $fromYear, $toYear);
        }

        foreach (Contact::datesInRange($from, $to) as $contact) {
            $eventDate = date_create_from_format('Y-m-d', $contact->date_of_birth);
            $this->addEvent($events, 'birthday', $contact, $eventDate, $from, $to, $fromYear, $toYear);
        }

        // Sort chronologically so the client doesn't need to re-sort.
        usort($events, fn ($a, $b) => strcmp($a['date'], $b['date']));

        return response()->json(['data' => $events]);
    }

    /**
     * Unified "what's coming up" feed: today's events plus the next
     * UPCOMING_DAYS days, sorted by date. Designed for an Android home-screen
     * widget — a single round trip gives everything needed to render
     * "Today: Alice's 30th birthday / Tomorrow: Bob & Carol's anniversary".
     */
    public function upcoming(Request $request): JsonResponse
    {
        $this->can('view');

        $today = new DateTime('today');
        $end = (clone $today)->modify('+'.self::UPCOMING_DAYS.' days');
        $fromYear = (int) $today->format('Y');
        $toYear = (int) $end->format('Y');

        $events = [];

        foreach (ContactDate::datesInRange($today, $end) as $date) {
            $eventDate = date_create_from_format('Y-m-d H:i:s', $date->date);
            $this->addEvent($events, 'date', $date, $eventDate, $today, $end, $fromYear, $toYear);
        }

        foreach (Contact::datesInRange($today, $end) as $contact) {
            $eventDate = date_create_from_format('Y-m-d', $contact->date_of_birth);
            $this->addEvent($events, 'birthday', $contact, $eventDate, $today, $end, $fromYear, $toYear);
        }

        // Annotate with days_until / is_today so the widget can group without
        // recomputing dates client-side.
        $todayStr = $today->format('Y-m-d');
        $events = array_map(function (array $e) use ($today, $todayStr) {
            $occurrence = DateTime::createFromFormat('Y-m-d', $e['date']);
            $e['days_until'] = (int) $today->diff($occurrence)->format('%r%a');
            $e['is_today'] = $e['date'] === $todayStr;

            return $e;
        }, $events);

        usort($events, fn ($a, $b) => $a['days_until'] <=> $b['days_until']);

        return response()->json(['data' => $events]);
    }

    /**
     * Report whether the user has an iCal subscription token issued, and
     * the URL if they do. The plaintext token is only available at the
     * moment of creation (Sanctum stores its hash), so a user who lost
     * their URL needs to rotate via POST to recover.
     */
    public function syncUrl(Request $request): JsonResponse
    {
        $this->can('view');

        $user = $request->user();
        $hasToken = $user->tokens()
            ->where('name', WebCalendarController::SYNC_TOKEN_NAME)
            ->exists();

        return response()->json([
            'data' => [
                'configured' => $hasToken,
                // We don't have the plaintext anymore — only POST returns it.
                'url' => null,
            ],
        ]);
    }

    /**
     * Issue (or rotate) the iCal subscription token. Any previous token of
     * the same name is revoked. The plaintext-bearing URL in this response
     * is unrecoverable afterwards — the client should treat it as a
     * one-shot value and persist it locally if it wants to surface it again.
     */
    public function rotateSyncUrl(Request $request): JsonResponse
    {
        $this->can('view');

        $user = $request->user();
        $user->tokens()->where('name', WebCalendarController::SYNC_TOKEN_NAME)->delete();
        $token = $user->createToken(WebCalendarController::SYNC_TOKEN_NAME);

        return response()->json([
            'data' => [
                'configured' => true,
                'url' => route('ical', [
                    'api_token' => $token->plainTextToken,
                    'tenant' => $user->currentTeam?->uuid,
                ]),
            ],
        ]);
    }

    /**
     * Revoke the iCal subscription token. Any device still polling the
     * old URL will start getting 401s.
     */
    public function revokeSyncUrl(Request $request): JsonResponse
    {
        $this->can('view');

        $request->user()
            ->tokens()
            ->where('name', WebCalendarController::SYNC_TOKEN_NAME)
            ->delete();

        return response()->json([
            'data' => ['configured' => false, 'url' => null],
        ]);
    }

    /**
     * Push a single event onto $events, applying the recurring-event year
     * mapping (which year's occurrence falls inside the window) and the
     * Feb-29-in-non-leap-year fallback. Lifted from the web controller's
     * processEvent + handleLeapYear and reshaped into the API event format.
     */
    private function addEvent(
        array &$events,
        string $type,
        Contact|ContactDate $source,
        \DateTime $eventDate,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $fromYear,
        int $toYear,
    ): void {
        if ($fromYear < $toYear) {
            // Window straddles a year boundary — pick whichever year's
            // recurrence actually falls between from and to.
            $fromCase = (int) ($fromYear.$eventDate->format('md')) > (int) $from->format('Ymd')
                && (int) ($fromYear.$eventDate->format('md')) < (int) $to->format('Ymd');
        } else {
            $fromCase = true;
        }

        $targetYear = $fromCase ? $fromYear : $toYear;
        $yearDifference = $targetYear - (int) $eventDate->format('Y');

        if ($yearDifference < 0) {
            // The originating record was created after the requested window —
            // don't surface a future-dated person retroactively.
            return;
        }

        $dateStr = $this->resolveOccurrenceDate($targetYear, $eventDate);
        $contact = $source instanceof Contact ? $source : $source->contact;

        // Important dates carry an explicit skip_year flag; birthdays use the
        // 1900 sentinel to mean "year unknown". Either way, suppress the age.
        $skipYear = $source instanceof ContactDate
            ? (bool) $source->skip_year
            : ! $source->hasKnownBirthYear();
        $years = $skipYear ? null : $yearDifference;

        $events[] = [
            'id' => $type.':'.$source->ulid.':'.$dateStr,
            'type' => $type,
            'title' => $type === 'birthday'
                ? $contact->fullname
                : $source->name,
            'date' => $dateStr,
            'all_day' => true,
            'skip_year' => $skipYear,
            'years' => $years,
            'contact' => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ],
        ];
    }

    /**
     * Format the occurrence date for the target year, sliding Feb 29 back
     * to Feb 28 when the target year isn't a leap year.
     */
    private function resolveOccurrenceDate(int $year, \DateTime $eventDate): string
    {
        $candidate = $year.'-'.$eventDate->format('m-d');
        $parsed = DateTime::createFromFormat('Y-m-d', $candidate);

        if ($parsed && $parsed->format('Y-m-d') === $candidate) {
            return $candidate;
        }

        // Feb 29 in a non-leap year — fall back one day.
        $fallback = DateTime::createFromFormat('Y-m-d', $year.'-'.$eventDate->format('m-d'));
        if ($fallback) {
            $fallback->sub(new DateInterval('P1D'));

            return $fallback->format('Y-m-d');
        }

        return $candidate;
    }
}

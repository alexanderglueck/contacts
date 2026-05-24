<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactDate;
use DateInterval;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    protected ?string $accessEntity = 'calendar';

    public const SYNC_TOKEN_NAME = 'Calendar sync';

    public function index(Request $request): Response
    {
        $this->can('view');

        return Inertia::render('Calendar/Index', [
            'hasCalendarSyncToken' => $request->user()
                ->tokens()
                ->where('name', self::SYNC_TOKEN_NAME)
                ->exists(),
        ]);
    }

    /**
     * Issue (or rotate) a Sanctum token dedicated to the iCal subscription
     * feed and return the full subscription URL. The plaintext token is
     * only available in this response — Sanctum stores its hash, so we
     * can't recover the URL later. Calling this again revokes the old
     * token and produces a fresh URL, which is the only way to recover
     * if the user lost it.
     */
    public function rotateSyncToken(Request $request): JsonResponse
    {
        $this->can('view');

        $user = $request->user();

        $user->tokens()->where('name', self::SYNC_TOKEN_NAME)->delete();
        $token = $user->createToken(self::SYNC_TOKEN_NAME);

        return response()->json([
            'url' => route('ical', [
                'api_token' => $token->plainTextToken,
                'tenant' => $user->currentTeam?->uuid,
            ]),
        ]);
    }

    public function events(Request $request): JsonResponse
    {
        $this->can('view');

        $fromRaw = date_create_from_format('Y-m-d', $request->input('start'));
        $toRaw = date_create_from_format('Y-m-d', $request->input('end'));
        $fromYear = $fromRaw->format('Y');
        $toYear = $toRaw->format('Y');

        $contactDates = ContactDate::datesInRange($fromRaw, $toRaw);

        $events = [];

        foreach ($contactDates as $event) {
            $eventDate = date_create_from_format('Y-m-d H:i:s', $event->date);
            $this->processEvent($events, $event, $eventDate, $fromRaw, $toRaw, $fromYear, $toYear);
        }

        $birthdays = Contact::datesInRange($fromRaw, $toRaw);

        foreach ($birthdays as $event) {
            $eventDate = date_create_from_format('Y-m-d', $event->date_of_birth);
            $this->processEvent($events, $event, $eventDate, $fromRaw, $toRaw, $fromYear, $toYear);
        }

        return response()->json($events);
    }

    private function processEvent(&$events, $event, $eventDate, $fromRaw, $toRaw, $fromYear, $toYear): void
    {
        if ($fromYear < $toYear) {
            $fromCase = intval($fromYear . $eventDate->format('md')) > intval($fromRaw->format('Ymd'))
                && intval($fromYear . $eventDate->format('md')) < intval($toRaw->format('Ymd'));
        } else {
            $fromCase = true;
        }

        if ($fromCase) {
            $yearDifference = $fromYear - $eventDate->format('Y');
            $title = $event->getCalculatedName($fromYear);
            $start = $fromYear . '-' . $eventDate->format('m-d');
        } else {
            $yearDifference = $toYear - $eventDate->format('Y');
            $title = $event->getCalculatedName($toYear);
            $start = $toYear . '-' . $eventDate->format('m-d');
        }

        $tempEvent = [
            'id' => $event->id,
            'title' => $title,
            'allDay' => true,
            'start' => $start,
            'url' => $event->getCalendarEventUrl($event),
        ];

        $tempEvent = $this->handleLeapYear($tempEvent);

        if ($yearDifference >= 0) {
            $events[] = $tempEvent;
        }
    }

    private function handleLeapYear(array $tempEvent): array
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $tempEvent['start']);

        if ($dateTime->format('md') == '0301' && $dateTime->format('Y-m-d') != $tempEvent['start']) {
            if ( ! $this->validateDate($tempEvent['start'], 'Y-m-d')) {
                $dateTime->sub(new DateInterval('P1D'));
                $tempEvent['start'] = $dateTime->format('Y-m-d');
            }
        }

        return $tempEvent;
    }

    private function validateDate(string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}

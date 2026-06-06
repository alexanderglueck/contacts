<?php

namespace App\Http\Controllers;

use App\Services\UpcomingEvents;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Illuminate\Support\Facades\Log;

class ICalController extends Controller
{
    public function index()
    {
        $fromRaw = DateTime::createFromFormat('Y-m-d', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toRaw = DateTime::createFromFormat('Y-m-d', Carbon::now()->addMonths(9)->format('Y-m-d'));

        $fromYear = $fromRaw->format('Y');
        $toYear = $toRaw->format('Y');

        // Both birthdays (date_of_birth) and important dates (ContactDate),
        // tenant-scoped via the Contact global scope. Without this the feed
        // would drop birthdays once they are migrated out of contact_dates.
        $upcomingEvents = UpcomingEvents::eventsInRange($fromRaw, $toRaw);

        $vCalendar = new Calendar();

        $timeZone = new DateTimeZone('Europe/Vienna');

        $events = [];

        foreach ($upcomingEvents as $event) {

            $eventDate = $event->date;

            /**
             * Check to see if the given date is in the from year or in the to year.
             */
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
                if (intval($fromYear . $eventDate->format('md')) > intval($fromRaw->format('Ymd')) && intval($fromYear . $eventDate->format('md')) < intval($toRaw->format('Ymd'))) {
                    $fromCase = true;
                } else {
                    $fromCase = false;
                }
            } else {
                $fromCase = true;
            }

            $displayYear = $fromCase ? (int) $fromYear : (int) $toYear;
            $yearDifference = $displayYear - (int) $eventDate->format('Y');
            $title = $event->calculatedName($displayYear);
            $start = $displayYear . '-' . $eventDate->format('m-d');

            $tempEvent = [
                'uid' => $event->uid,
                'title' => $title,
                'allDay' => true,
                'start' => $start,
                'url' => $event->url(),
            ];

            /**
             * Only display the event if the requested date range is after the event creation.
             */
            if ($yearDifference >= 0) {
                $events[] = $tempEvent;
            }
        }

        foreach ($events as $event) {
            try {
                $vEvent = new \Eluceo\iCal\Domain\Entity\Event(
                    new UniqueIdentifier($event['uid'])
                );
                $vEvent->setOccurrence(
                    new SingleDay(
                        new Date(\DateTimeImmutable::createFromFormat('Y-m-d', $event['start'], $timeZone))
                    )
                )->setSummary($event['title']);

                $vCalendar->addEvent($vEvent);
            } catch (\Exception $e) {
                Log::error('Malformed DateTime', [
                    'message' => $e->getMessage()
                ]);
            }
        }

        $vCalendar->setProductIdentifier(config('app.url'));

        $response = response((new CalendarFactory())->createCalendar($vCalendar));
        $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="cal.ics"');;

        return $response;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ContactDate;
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

        $contactDates = ContactDate::datesInRange($fromRaw, $toRaw);

        $vCalendar = new Calendar();

        $timeZone = new DateTimeZone('Europe/Vienna');

        $events = [];

        foreach ($contactDates as $event) {

            $eventDate = date_create_from_format('Y-m-d H:i:s', $event->date);

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

            if ($fromCase) {
                $yearDifference = $fromYear - $eventDate->format('Y');
                $title = $event->getCalculatedName($fromYear) . PHP_EOL . $event->contact->fullname;

                $start = $fromYear . '-' . $eventDate->format('m-d');

            } else {
                $yearDifference = $toYear - $eventDate->format('Y');
                $title = $event->getCalculatedName($toYear) . PHP_EOL . $event->contact->fullname;

                $start = $toYear . '-' . $eventDate->format('m-d');
            }

            $tempEvent = [
                'id' => $event->id,
                'title' => $title,
                'allDay' => true,
                'start' => $start,
                'url' => route('contact_dates.show', [$event->contact->slug, $event->slug])
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
                    new UniqueIdentifier($event['id'])
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

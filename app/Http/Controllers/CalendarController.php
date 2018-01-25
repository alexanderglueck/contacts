<?php

namespace App\Http\Controllers;

use App\Models\ContactDate;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the contact dates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('calendar.index', []);
    }

    /**
     * Returns a JSON array of contact dates in the given
     * start and end date range.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function events(Request $request)
    {
        $fromRaw = date_create_from_format('Y-m-d', $request->input('start'));
        $toRaw = date_create_from_format('Y-m-d', $request->input('end'));
        $fromYear = $fromRaw->format('Y');
        $toYear = $toRaw->format('Y');

        /**
         * Fetches the contact dates between the given start and end date.
         * If the start date is greater than the end date the query
         * fetches dates from two years and changes the query accordingly.
         */
        $contactDates = ContactDate::datesInRange($fromRaw, $toRaw);

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

        return response()->json($events);
    }
}

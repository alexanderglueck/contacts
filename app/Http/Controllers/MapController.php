<?php

namespace App\Http\Controllers;

use App\Models\ContactAddress;
use App\Http\Requests\Map\MapContactsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    protected ?string $accessEntity = 'map';

    /**
     * Display all the geocoded contacts on a map.
     */
    public function index(): View
    {
        $this->can('view');

        return view('map.index', []);
    }

    public function contacts(MapContactsRequest $request): JsonResponse
    {
        $rawBounds = explode(',', $request->bounds);

        $bounds = [
            'sw_lat' => $rawBounds[0],
            'sw_lng' => $rawBounds[1],
            'ne_lat' => $rawBounds[2],
            'ne_lng' => $rawBounds[3],
        ];

        $contactAddresses = ContactAddress::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1)
            ->whereRaw('(CASE WHEN ' . $bounds['ne_lat'] . ' < ' . $bounds['sw_lat'] . '
                    THEN latitude BETWEEN ' . $bounds['ne_lat'] . ' AND ' . $bounds['sw_lat'] . '
                    ELSE latitude BETWEEN ' . $bounds['sw_lat'] . ' AND ' . $bounds['ne_lat'] . '
            END)
            AND
            (CASE WHEN ' . $bounds['ne_lng'] . ' < ' . $bounds['sw_lng'] . '
                    THEN longitude BETWEEN ' . $bounds['ne_lng'] . ' AND ' . $bounds['sw_lng'] . '
                    ELSE longitude BETWEEN ' . $bounds['sw_lng'] . ' AND ' . $bounds['ne_lng'] . '
            END)')
            ->get();

        $markers = [];

        foreach ($contactAddresses as $contactAddress) {
            $tempArray = [
                'title' => $contactAddress->contact->fullname,
                'name' => $contactAddress->name,
                'latitude' => $contactAddress->latitude,
                'longitude' => $contactAddress->longitude,
                'address' => '<p><b>' . $contactAddress->contact->fullname . '</b><br />' . PHP_EOL .
                    '<b>' . $contactAddress->name . '</b><br />' . PHP_EOL .
                    $contactAddress->street . '<br />' . PHP_EOL .
                    $contactAddress->zip . ', ' . $contactAddress->city . '<br />' . PHP_EOL .
                    $contactAddress->state . '<br />' . PHP_EOL .
                    $contactAddress->country->country . '<br />' . PHP_EOL .
                    '<br />' . PHP_EOL .
                    "<a href='" . route('contacts.show', $contactAddress->contact->slug) . "'>" .
                    trans('ui.view_contact') .
                    '</a></p>'
            ];

            $markers[] = $tempArray;
        }

        return response()->json($markers);
    }
}

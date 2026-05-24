<?php

namespace App\Http\Controllers;

use App\Models\ContactAddress;
use App\Http\Requests\Map\MapContactsRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    protected ?string $accessEntity = 'map';

    public function index(): Response
    {
        $this->can('view');

        return Inertia::render('Map/Index', [
            'googleMapsKey' => config('contacts.googleMapsKey'),
        ]);
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
            $markers[] = [
                'title' => $contactAddress->contact->fullname,
                'name' => $contactAddress->name,
                'latitude' => (float) $contactAddress->latitude,
                'longitude' => (float) $contactAddress->longitude,
                'contact_slug' => $contactAddress->contact->slug,
                'street' => $contactAddress->street,
                'zip' => $contactAddress->zip,
                'city' => $contactAddress->city,
                'state' => $contactAddress->state,
                'country' => $contactAddress->country?->country,
            ];
        }

        return response()->json($markers);
    }
}

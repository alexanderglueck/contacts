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

        return Inertia::render('Map/Index');
    }

    public function contacts(MapContactsRequest $request): JsonResponse
    {
        $rawBounds = array_map('floatval', explode(',', (string) $request->bounds));
        [$swLat, $swLng, $neLat, $neLng] = array_pad($rawBounds, 4, 0.0);

        // whereHas('contact') runs through the Contact model, so its
        // BelongsToTenantScope applies — only addresses whose contact is visible
        // to the current tenant come back, and the eager-loaded relation is
        // therefore never null below. (A raw join here would bypass that scope
        // and hand us cross-tenant rows whose ->contact resolves to null.)
        // Bounds are bound as parameters, not interpolated, to avoid injection.
        $contactAddresses = ContactAddress::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('contact', fn ($q) => $q->where('active', 1))
            ->whereRaw(
                '(CASE WHEN ? < ? THEN latitude BETWEEN ? AND ? ELSE latitude BETWEEN ? AND ? END)
                AND
                (CASE WHEN ? < ? THEN longitude BETWEEN ? AND ? ELSE longitude BETWEEN ? AND ? END)',
                [
                    $neLat, $swLat, $neLat, $swLat, $swLat, $neLat,
                    $neLng, $swLng, $neLng, $swLng, $swLng, $neLng,
                ]
            )
            ->with(['contact', 'country'])
            ->get();

        $markers = [];

        foreach ($contactAddresses as $contactAddress) {
            $markers[] = [
                'title' => $contactAddress->contact->fullname,
                'name' => $contactAddress->name,
                'latitude' => (float) $contactAddress->latitude,
                'longitude' => (float) $contactAddress->longitude,
                'contact_ulid' => $contactAddress->contact->ulid,
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

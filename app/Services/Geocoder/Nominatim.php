<?php

namespace App\Services\Geocoder;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Nominatim
{
    /**
     * Resolve a postal address to coordinates via Nominatim.
     *
     * Returns ['latitude' => float, 'longitude' => float] on success, null
     * when the address is unparseable, the API returns no match, or the
     * call fails. Failures are logged but never thrown — callers can treat
     * a null return as "leave lat/lng untouched".
     */
    public function geocodeAddress(
        ?string $street,
        ?string $zip,
        ?string $city,
        ?string $state,
        ?string $country,
    ): ?array {
        $query = $this->buildQuery($street, $zip, $city, $state, $country);

        if ($query === '') {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => config('services.nominatim.user_agent'),
                'Accept' => 'application/json',
            ])
                ->timeout(10)
                ->retry(2, 250, throw: false)
                ->get(config('services.nominatim.endpoint'), [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'addressdetails' => 0,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Nominatim geocoding failed', ['query' => $query, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Nominatim geocoding non-2xx', ['query' => $query, 'status' => $response->status()]);

            return null;
        }

        $first = $response->json()[0] ?? null;

        if (! $first || ! isset($first['lat'], $first['lon'])) {
            return null;
        }

        return [
            'latitude' => (float) $first['lat'],
            'longitude' => (float) $first['lon'],
        ];
    }

    private function buildQuery(?string $street, ?string $zip, ?string $city, ?string $state, ?string $country): string
    {
        return collect([
            trim((string) $street),
            trim(($zip ? $zip.' ' : '').((string) $city)),
            trim((string) $state),
            trim((string) $country),
        ])->filter(fn ($p) => $p !== '')->implode(', ');
    }
}

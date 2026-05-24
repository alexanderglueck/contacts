<?php

namespace App\Jobs;

use App\Models\ContactAddress;
use App\Services\Geocoder\Nominatim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class GeocodeContactAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ContactAddress $address)
    {
    }

    public function handle(Nominatim $geocoder): void
    {
        $this->address->refresh()->loadMissing('country');

        $result = $geocoder->geocodeAddress(
            $this->address->street,
            $this->address->zip,
            $this->address->city,
            $this->address->state,
            $this->address->country?->country,
        );

        if ($result === null) {
            return;
        }

        $this->address->forceFill($result)->saveQuietly();
    }

    public function middleware(): array
    {
        // Nominatim's public endpoint allows max 1 req/sec across the whole app.
        // The matching limiter is registered in AppServiceProvider::boot().
        // When limited, RateLimited re-queues the job with backoff (default 3s).
        return [new RateLimited('nominatim')];
    }
}

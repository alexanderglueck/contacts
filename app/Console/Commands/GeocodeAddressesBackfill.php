<?php

namespace App\Console\Commands;

use App\Jobs\GeocodeContactAddress;
use App\Models\ContactAddress;
use Illuminate\Console\Command;

class GeocodeAddressesBackfill extends Command
{
    protected $signature = 'addresses:geocode-backfill
                            {--force : Re-queue even addresses that already have lat/lng}';

    protected $description = 'Dispatch geocoding jobs for ContactAddresses missing lat/lng (or all if --force).';

    public function handle(): int
    {
        $query = ContactAddress::query();

        if (! $this->option('force')) {
            $query->where(fn ($q) => $q->whereNull('latitude')->orWhereNull('longitude'));
        }

        $count = 0;

        $query->cursor()->each(function (ContactAddress $address) use (&$count) {
            GeocodeContactAddress::dispatch($address);
            $count++;
        });

        $this->info("Dispatched {$count} geocoding job(s). The queue worker will process them throttled to 1/sec.");

        return self::SUCCESS;
    }
}

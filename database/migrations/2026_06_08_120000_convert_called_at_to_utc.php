<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * contact_calls.called_at was historically stored as a NAIVE wall-clock string
 * with no timezone identity — entered by this single-owner app in local
 * (Europe/Vienna) time. Going forward the value is stored in UTC and clients
 * localize it themselves, so we reinterpret the existing rows as Europe/Vienna
 * and shift them to UTC. Carbon resolves historical DST for the zone correctly.
 *
 * Raw DB access on purpose: routing through the model would re-apply the new
 * datetime cast and double-convert.
 */
return new class extends Migration
{
    private const ASSUMED_ZONE = 'Europe/Vienna';

    public function up(): void
    {
        $this->shift(self::ASSUMED_ZONE, 'UTC');
    }

    public function down(): void
    {
        $this->shift('UTC', self::ASSUMED_ZONE);
    }

    private function shift(string $from, string $to): void
    {
        DB::table('contact_calls')
            ->whereNotNull('called_at')
            ->orderBy('id')
            ->each(function ($row) use ($from, $to) {
                $converted = Carbon::createFromFormat('Y-m-d H:i:s', $row->called_at, $from)
                    ->setTimezone($to)
                    ->format('Y-m-d H:i:s');

                DB::table('contact_calls')
                    ->where('id', $row->id)
                    ->update(['called_at' => $converted]);
            });
    }
};

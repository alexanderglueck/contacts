<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->string('e164', 32)->nullable()->after('number')->index();
        });

        $util = PhoneNumberUtil::getInstance();
        $region = config('contacts.phone_default_region', 'AT');

        // Backfill existing rows. Write through raw DB to skip the mutator
        // (which would re-parse and write the same value back) and any
        // model events (RecordsActivity etc. shouldn't fire for a backfill).
        DB::table('contact_numbers')
            ->select(['id', 'number'])
            ->whereNotNull('number')
            ->orderBy('id')
            ->lazyById()
            ->each(function ($row) use ($util, $region) {
                try {
                    $parsed = $util->parse($row->number, $region);
                    if (! $util->isValidNumber($parsed)) {
                        return;
                    }
                    DB::table('contact_numbers')
                        ->where('id', $row->id)
                        ->update(['e164' => $util->format($parsed, PhoneNumberFormat::E164)]);
                } catch (NumberParseException) {
                    // leave e164 null — caller can still fall back to the
                    // digit-suffix match path in ContactsController::byNumber
                }
            });
    }

    public function down(): void
    {
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->dropIndex(['e164']);
            $table->dropColumn('e164');
        });
    }
};

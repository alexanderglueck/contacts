<?php

use App\Models\ContactNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactNumber::withoutEvents(function () {
            ContactNumber::query()->whereNull('ulid')->cursor()->each(function (ContactNumber $number) {
                $number->ulid = (string) Str::ulid();
                $number->saveQuietly();
            });
        });

        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_numbers', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

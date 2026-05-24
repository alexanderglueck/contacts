<?php

use App\Models\ContactDate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_dates', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactDate::withoutEvents(function () {
            ContactDate::query()->whereNull('ulid')->cursor()->each(function (ContactDate $date) {
                $date->ulid = (string) Str::ulid();
                $date->saveQuietly();
            });
        });

        Schema::table('contact_dates', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_dates', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

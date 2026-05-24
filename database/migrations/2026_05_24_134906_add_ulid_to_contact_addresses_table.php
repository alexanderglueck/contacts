<?php

use App\Models\ContactAddress;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactAddress::withoutEvents(function () {
            ContactAddress::query()->whereNull('ulid')->cursor()->each(function (ContactAddress $address) {
                $address->ulid = (string) Str::ulid();
                $address->saveQuietly();
            });
        });

        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_addresses', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

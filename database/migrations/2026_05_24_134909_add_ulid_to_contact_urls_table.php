<?php

use App\Models\ContactUrl;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_urls', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactUrl::withoutEvents(function () {
            ContactUrl::query()->whereNull('ulid')->cursor()->each(function (ContactUrl $url) {
                $url->ulid = (string) Str::ulid();
                $url->saveQuietly();
            });
        });

        Schema::table('contact_urls', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_urls', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

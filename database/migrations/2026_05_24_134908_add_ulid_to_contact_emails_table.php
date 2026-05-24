<?php

use App\Models\ContactEmail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_emails', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactEmail::withoutEvents(function () {
            ContactEmail::query()->whereNull('ulid')->cursor()->each(function (ContactEmail $email) {
                $email->ulid = (string) Str::ulid();
                $email->saveQuietly();
            });
        });

        Schema::table('contact_emails', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_emails', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

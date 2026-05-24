<?php

use App\Models\Contact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        Contact::withoutEvents(function () {
            Contact::withoutGlobalScopes()->whereNull('ulid')->cursor()->each(function (Contact $contact) {
                $contact->ulid = (string) Str::ulid();
                $contact->saveQuietly();
            });
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

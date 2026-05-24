<?php

use App\Models\ContactNote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_notes', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactNote::withoutEvents(function () {
            ContactNote::query()->whereNull('ulid')->cursor()->each(function (ContactNote $note) {
                $note->ulid = (string) Str::ulid();
                $note->saveQuietly();
            });
        });

        Schema::table('contact_notes', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_notes', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

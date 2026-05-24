<?php

use App\Models\ContactCall;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_calls', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactCall::withoutEvents(function () {
            ContactCall::query()->whereNull('ulid')->cursor()->each(function (ContactCall $call) {
                $call->ulid = (string) Str::ulid();
                $call->saveQuietly();
            });
        });

        Schema::table('contact_calls', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_calls', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

<?php

use App\Models\ContactGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        ContactGroup::withoutEvents(function () {
            ContactGroup::query()->whereNull('ulid')->cursor()->each(function (ContactGroup $group) {
                $group->ulid = (string) Str::ulid();
                $group->saveQuietly();
            });
        });

        Schema::table('contact_groups', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

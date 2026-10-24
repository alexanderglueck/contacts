<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Activities had no team, so the activity log listed every team's entries to
 * everyone allowed to view activities. team_id scopes the log to a team, and
 * contact_id groups a contact's own entries with those of its numbers, emails,
 * addresses, URLs and dates into one history.
 *
 * Existing rows can only be backfilled for contacts that still exist. Rows of
 * deleted contacts keep a null team_id and so no longer show up anywhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->index()->after('user_id');
            $table->unsignedBigInteger('contact_id')->nullable()->index()->after('team_id');
        });

        DB::table('activities')
            ->where('object_type', 'App\\Models\\Contact')
            ->update(['contact_id' => DB::raw('object_id')]);

        DB::table('activities')
            ->whereNotNull('contact_id')
            ->update([
                'team_id' => DB::raw('(SELECT contacts.team_id FROM contacts WHERE contacts.id = activities.contact_id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
            $table->dropIndex(['contact_id']);
            $table->dropColumn(['team_id', 'contact_id']);
        });
    }
};

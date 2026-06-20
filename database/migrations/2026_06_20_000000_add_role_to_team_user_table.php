<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jetstream's HasTeams trait builds the user<->team relation through a
 * Membership pivot that reads a `role` column (->withPivot('role')). The
 * original mpociot/teamwork pivot had no such column, so add it. Roles are
 * not used by the app (every member is equal), hence nullable with no default.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->string('role')->nullable()->after('team_id');
        });
    }

    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

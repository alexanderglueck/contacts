<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Team-wide opt-out for the password-reset flow. When the owner sets
     * this on a team, every member of that team gets the same "no reset
     * email" behaviour as the per-user toggle on the Profile page —
     * even when sign-in policy is set by the team owner rather than the
     * individual member.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('password_reset_disabled')->default(false)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('password_reset_disabled');
        });
    }
};

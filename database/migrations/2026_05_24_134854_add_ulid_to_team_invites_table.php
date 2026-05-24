<?php

use App\Models\TeamInvite;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('team_invites', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        TeamInvite::withoutEvents(function () {
            TeamInvite::query()->whereNull('ulid')->cursor()->each(function (TeamInvite $invite) {
                $invite->ulid = (string) Str::ulid();
                $invite->saveQuietly();
            });
        });

        Schema::table('team_invites', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('team_invites', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

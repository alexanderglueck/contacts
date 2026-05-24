<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Contact groups were never tenant-scoped — the table predates the
     * multi-tenant rework and somehow stayed without a `team_id`. The
     * /api/v1/reference/contact-groups endpoint therefore returned every
     * tenant's groups to every authenticated caller, a real isolation
     * leak.
     *
     * Migration in three steps:
     *   1) Add a nullable team_id column so existing rows can survive
     *      the rename.
     *   2) Backfill from the creator's current_team_id (best-effort —
     *      a user's current team might have changed since they created
     *      a group, but it's the closest signal we have; ops can re-
     *      assign edge cases by hand if needed).
     *   3) Add the foreign key. Leaving `nullable()` rather than
     *      tightening to NOT NULL because rows whose creator has since
     *      had their account fully removed (current_team_id = null)
     *      would otherwise block the migration; the BelongsToTenant
     *      scope on the model gates visibility regardless.
     */
    public function up(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('id');
        });

        // Backfill. SQLite (tests) treats UPDATE … FROM differently from
        // MySQL — both fall through to this correlated-subquery form which
        // works on every Laravel-supported driver.
        DB::statement(<<<'SQL'
            UPDATE contact_groups
            SET team_id = (
                SELECT current_team_id
                FROM users
                WHERE users.id = contact_groups.created_by
            )
        SQL);

        Schema::table('contact_groups', function (Blueprint $table) {
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::table('contact_groups', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropIndex(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};

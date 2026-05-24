<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * When a comment with replies is deleted we mark it as tombstoned
     * instead of removing the row — the thread needs the parent to render
     * its children. Hard-delete only happens once a tombstoned row has no
     * remaining children.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('tombstoned_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('tombstoned_at');
        });
    }
};

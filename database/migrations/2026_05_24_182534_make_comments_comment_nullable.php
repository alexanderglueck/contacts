<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tombstoned comments hold a NULL `comment` body (we only keep the row
     * around so its replies can render). The column was created NOT NULL
     * in the original migration — relax it.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('comment')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('comment')->nullable(false)->change();
        });
    }
};

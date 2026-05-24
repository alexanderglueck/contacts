<?php

use App\Models\Admin\Announcement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        Announcement::withoutEvents(function () {
            Announcement::query()->whereNull('ulid')->cursor()->each(function (Announcement $announcement) {
                $announcement->ulid = (string) Str::ulid();
                $announcement->saveQuietly();
            });
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

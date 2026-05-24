<?php

use App\Models\System\News;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        News::withoutEvents(function () {
            News::query()->whereNull('ulid')->cursor()->each(function (News $news) {
                $news->ulid = (string) Str::ulid();
                $news->saveQuietly();
            });
        });

        Schema::table('news', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

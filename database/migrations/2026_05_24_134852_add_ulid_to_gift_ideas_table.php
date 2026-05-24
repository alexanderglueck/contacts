<?php

use App\Models\GiftIdea;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gift_ideas', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        GiftIdea::withoutEvents(function () {
            GiftIdea::query()->whereNull('ulid')->cursor()->each(function (GiftIdea $idea) {
                $idea->ulid = (string) Str::ulid();
                $idea->saveQuietly();
            });
        });

        Schema::table('gift_ideas', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('gift_ideas', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

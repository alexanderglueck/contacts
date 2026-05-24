<?php

use App\Models\Comment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable()->after('id');
        });

        Comment::withoutEvents(function () {
            Comment::query()->whereNull('ulid')->cursor()->each(function (Comment $comment) {
                $comment->ulid = (string) Str::ulid();
                $comment->saveQuietly();
            });
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropUnique(['ulid']);
            $table->dropColumn('ulid');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot first because of the FK from news_user.news_id → news.id.
        Schema::dropIfExists('news_user');
        Schema::dropIfExists('news');
    }

    public function down(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('ulid', 26)->nullable();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
            $table->dateTime('expired_at')->nullable()->default(null);
            $table->dateTime('pinned_at')->nullable()->default(null);
            $table->unique('ulid');
        });

        Schema::create('news_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('news_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('news_id')->references('id')->on('news')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['user_id', 'news_id']);
        });
    }
};

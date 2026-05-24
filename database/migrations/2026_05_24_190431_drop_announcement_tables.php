<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot first because of the FK from announcement_user.announcement_id
        // → announcements.id.
        Schema::dropIfExists('announcement_user');
        Schema::dropIfExists('announcements');
    }

    public function down(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('ulid', 26)->nullable();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
            $table->dateTime('expired_at')->nullable()->default(null);
            $table->dateTime('pinned_at')->nullable()->default(null);
            $table->unique('ulid');
        });

        Schema::create('announcement_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('announcement_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('announcement_id')->references('id')->on('announcements')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['user_id', 'announcement_id']);
        });
    }
};

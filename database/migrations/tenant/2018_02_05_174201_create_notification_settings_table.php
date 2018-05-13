<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('send_daily')->default(false);
            $table->boolean('send_weekly')->default(false);

            $table->unsignedInteger('user_id');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'users'
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_settings');
    }
}

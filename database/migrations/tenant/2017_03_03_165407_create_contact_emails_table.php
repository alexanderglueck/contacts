<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('contact_id')->unsigned();
            $table->string('name');
            $table->string('email');
            $table->boolean('is_default')->default(false);
            $table->string('slug');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();

            $table->foreign('created_by')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'users'
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'users'
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
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
        Schema::dropIfExists('contact_emails');
    }
}

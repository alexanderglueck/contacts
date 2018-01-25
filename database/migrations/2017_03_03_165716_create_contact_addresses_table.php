<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('contact_id')->unsigned();
            $table->string('name');
            $table->string('street');
            $table->string('zip');
            $table->string('city');
            $table->string('state');
            $table->integer('country_id')->unsigned()->nullable();

            $table->boolean('is_default')->default(false);

            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_addresses');
    }
}

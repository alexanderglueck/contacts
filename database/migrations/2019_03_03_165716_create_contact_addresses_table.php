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
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('contact_id');
            $table->string('name');
            $table->string('street');
            $table->string('zip');
            $table->string('city');
            $table->string('state');
            $table->unsignedBigInteger('country_id')->nullable();

            $table->boolean('is_default')->default(false);

            $table->double('longitude')->nullable();
            $table->double('latitude')->nullable();

            $table->string('slug');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
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
        Schema::dropIfExists('contact_addresses');
    }
}

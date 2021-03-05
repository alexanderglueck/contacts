<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateContactContactgroupPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_contact_group', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->index();
            $table->unsignedBigInteger('contact_group_id')->index();

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('contact_group_id')
                ->references('id')
                ->on('contact_groups')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['contact_id', 'contact_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contact_contact_group');
    }
}

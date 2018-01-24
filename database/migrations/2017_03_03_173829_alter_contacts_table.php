<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts',function(Blueprint $table) {
            $table->string('title')->nullable()->default(null);
            $table->string('title_after')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->string('salutation')->nullable()->default(null);
            $table->string('iban')->nullable()->default(null);

            $table->string('company')->nullable()->default(null);
            $table->string('department')->nullable()->default(null);
            $table->string('job')->nullable()->default(null);

            $table->string('custom_id')->nullable()->default(null);

            $table->integer('parent_id')->unsigned()->nullable()->default(null);



            $table->foreign('parent_id')
                ->references('id')->on('contacts')
                ->onDelete('restrict')
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
        Schema::table('contacts',function(Blueprint $table) {
            $table->dropColumn('company');
            $table->dropColumn('job');
            $table->dropColumn('department');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('title');
            $table->dropColumn('title_after');
            $table->dropColumn('salutation');
        });
    }
}

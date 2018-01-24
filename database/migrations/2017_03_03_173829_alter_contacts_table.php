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
            $table->string('company')->nullable();
            $table->string('job')->nullable();
            $table->string('department')->nullable();
            $table->string('title')->nullable();
            $table->string('title_after')->nullable();
            $table->string('salutation')->nullable();
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
            $table->dropColumn('title');
            $table->dropColumn('title_after');
            $table->dropColumn('salutation');
        });
    }
}

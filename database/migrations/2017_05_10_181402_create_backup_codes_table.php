<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackupCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tfa_backup_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->string('value');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['user_id', 'value'], 'tfa_backup_codes_user_id_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tfa_backup_codes', function (Blueprint $table) {
            $table->dropForeign('tfa_backup_codes_user_id_foreign');
            $table->dropUnique('tfa_backup_codes_user_id_value_unique');
        });

        Schema::dropIfExists('tfa_backup_codes');
    }
}

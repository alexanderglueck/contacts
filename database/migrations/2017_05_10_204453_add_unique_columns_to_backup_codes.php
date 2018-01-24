<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueColumnsToBackupCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tfa_backup_codes', function (Blueprint $table) {
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
            $table->dropUnique('tfa_backup_codes_user_id_value_unique');
        });
    }
}

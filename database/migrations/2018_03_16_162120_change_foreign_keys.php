<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'contact_addresses',
            'contact_dates',
            'contact_emails',
            'contact_numbers',
            'contact_urls',
            'contacts',
            'log_entries'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign($tableName . '_created_by_foreign');

                $table->foreign('created_by')->references('id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }


        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropForeign('notification_settings_user_id_foreign');

            $table->foreign('user_id')->references('id')->on('users')
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
        //
    }
}

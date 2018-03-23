<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamworkSetupTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(Config::get('teamwork.users_table'), function (Blueprint $table) {
            $table->unsignedInteger('current_team_id')->nullable();
        });

        Schema::create(Config::get('teamwork.teams_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('owner_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create(Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('team_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references(Config::get('teamwork.user_foreign_key'))
                ->on(Config::get('teamwork.users_table'))
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on(Config::get('teamwork.teams_table'))
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create(Config::get('teamwork.team_invites_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('team_id');
            $table->enum('type', ['invite', 'request']);
            $table->string('email');
            $table->string('accept_token');
            $table->string('deny_token');
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')
                ->on(Config::get('teamwork.teams_table'))
                ->onDelete('cascade');
        });

        Schema::table(Config::get('teamwork.users_table'), function (Blueprint $table) {
            $table->foreign('current_team_id')->references('id')
                ->on(Config::get('teamwork.teams_table'))
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        $tables = [
            'announcements',
            'contacts',
            'contact_groups'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('team_id')->nullable();

                $table->foreign('team_id')->references('id')
                    ->on(Config::get('teamwork.teams_table'))
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'announcements',
            'contacts',
            'contact_groups'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign($tableName . '_team_id_foreign');

                $table->dropColumn('team_id');
            });
        }

        Schema::table(Config::get('teamwork.users_table'), function (Blueprint $table) {
            $table->dropForeign(Config::get('teamwork.users_table') . '_current_team_id_foreign');

            $table->dropColumn('current_team_id');
        });

        Schema::table(Config::get('teamwork.team_user_table'), function (Blueprint $table) {
            $table->dropForeign(Config::get('teamwork.team_user_table') . '_user_id_foreign');
            $table->dropForeign(Config::get('teamwork.team_user_table') . '_team_id_foreign');
        });

        Schema::drop(Config::get('teamwork.team_user_table'));
        Schema::drop(Config::get('teamwork.team_invites_table'));
        Schema::drop(Config::get('teamwork.teams_table'));
    }
}

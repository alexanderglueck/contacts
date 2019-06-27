<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('title')->nullable()->default(null);
            $table->string('title_after')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->string('salutation')->nullable()->default(null);
            $table->string('iban')->nullable()->default(null);

            $table->string('company')->nullable()->default(null);
            $table->string('department')->nullable()->default(null);
            $table->string('job')->nullable()->default(null);

            $table->string('custom_id')->nullable()->default(null);
            $table->string('nickname')->nullable()->default(null);

            $table->unsignedBigInteger('parent_id')->nullable()->default(null);

            $table->text('first_met')->nullable();
            $table->text('note')->nullable();
            $table->date('died_at')->nullable();
            $table->text('died_from')->nullable();

            $table->unsignedBigInteger('nationality_id')->nullable();

            $table->boolean('active')->default(true);

            $table->string('image')->nullable();

            $table->string('slug');

            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('restrict')
                ->onUpdate('cascade');

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

            $table->foreign('gender_id')
                ->references('id')
                ->on('genders')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('nationality_id')
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
        Schema::dropIfExists('contacts');
    }
}

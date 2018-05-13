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
            $table->increments('id');
            $table->timestamps();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('title')->nullable()->default(null);
            $table->string('title_after')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->integer('gender_id')->unsigned()->nullable();
            $table->string('salutation')->nullable()->default(null);
            $table->string('iban')->nullable()->default(null);

            $table->string('company')->nullable()->default(null);
            $table->string('department')->nullable()->default(null);
            $table->string('job')->nullable()->default(null);

            $table->string('custom_id')->nullable()->default(null);
            $table->string('nickname')->nullable()->default(null);

            $table->integer('parent_id')->unsigned()->nullable()->default(null);

            $table->text('first_met')->nullable();
            $table->text('note')->nullable();
            $table->date('died_at')->nullable();
            $table->text('died_from')->nullable();

            $table->unsignedInteger('nationality_id')->nullable();


            $table->boolean('active')->default(true);

            $table->string('image')->nullable();

            $table->string('slug');

            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();

            $table->foreign('parent_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'users'
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'users'
                )
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('gender_id')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'genders'
                )
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('nationality_id')
                ->references('id')
                ->on(
                    env('DB_DATABASE') . '.' .
                    'countries'
                )
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

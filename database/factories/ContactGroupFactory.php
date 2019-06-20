<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactGroup::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

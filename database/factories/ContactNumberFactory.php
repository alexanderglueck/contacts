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
$factory->define(App\Models\ContactNumber::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::all()->keys()->toArray();

    return [
        'contact_id' => $faker->randomElement($contacts),
        'name' => $faker->word,
        'number' => $faker->phoneNumber,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

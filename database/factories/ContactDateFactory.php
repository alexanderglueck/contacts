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
$factory->define(App\Models\ContactDate::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::pluck('id')->toArray();

    $skipYear = $faker->boolean;

    return [
        'contact_id' => $faker->randomElement($contacts),
        'name' => $faker->streetName,
        'date' => $skipYear ? $faker->date('d.m.1900') : $faker->date('d.m.Y'),
        'skip_year' => $skipYear,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

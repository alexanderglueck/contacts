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
$factory->define(App\Models\Contact::class, function (Faker\Generator $faker) {
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'title' => $faker->title,
        'title_after' => $faker->title,
        'date_of_birth' => $faker->date('d.m.Y'),
        'iban' => $faker->iban('AT'),
        'salutation' => $faker->word,
        'gender_id' => $faker->numberBetween(1, 2),
        'company' => $faker->company,
        'vatin' => null,
        'department' => $faker->jobTitle,
        'job' => $faker->jobTitle,
        'custom_id' => $faker->unique()->randomNumber(),
        'nickname' => $faker->userName,
        'active' => 1,
        'first_met' => null,
        'note' => null,
        'died_at' => null,
        'died_from' => null,
        'nationality_id' => null,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

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
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'current_team_id' => function () {
            return factory(\App\Models\Team::class);
        }
    ];
});

$factory->define(App\Models\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->define(App\Models\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'team_id' => function () {
            return factory(\App\Models\Team::class);
        },
        'guard_name' => 'web'
    ];
});

$factory->define(\Spatie\Permission\Models\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'guard_name' => 'web'
    ];
});

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
        // 'is_company' => $faker->boolean() ? 1 : 0,
        // 'vatin' => $faker->bankAccountNumber,
        'company' => $faker->company,
        'department' => $faker->jobTitle,
        'job' => $faker->jobTitle,
        'custom_id' => $faker->unique()->randomNumber(),
        'nickname' => $faker->userName,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactAddress::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::all()->keys()->toArray();

    return [
        'contact_id' => $faker->randomElement($contacts),
        'name' => $faker->streetName,
        'street' => $faker->streetAddress,
        'zip' => $faker->postcode,
        'city' => $faker->city,
        'state' => '',
        'country_id' => 164,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactDate::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::all()->keys()->toArray();

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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactEmail::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::all()->keys()->toArray();

    return [
        'contact_id' => $faker->randomElement($contacts),
        'name' => $faker->word,
        'email' => $faker->email,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactUrl::class, function (Faker\Generator $faker) {
    static $contacts;

    $contacts = $contacts ?: App\Models\Contact::all()->keys()->toArray();

    return [
        'contact_id' => $faker->randomElement($contacts),
        'name' => $faker->word,
        'url' => $faker->url,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ContactGroup::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'created_by' => 1,
        'updated_by' => 1
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Admin\Announcement::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return (factory(App\Models\User::class)->create())->id;
        },
        'title' => $faker->sentence,
        'body' => $faker->paragraph(30)
    ];
});

<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Alexander Glück',
            'email' => 'alex.glueck@gmx.at',
            'password' => Hash::make(env('USER_PASSWORD', 'secret')),
            'slug' => 'glueck-alexander',
            'image' => null,
            'api_token' => str_random(60)
        ]);
    }
}

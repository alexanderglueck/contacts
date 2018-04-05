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
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make(env('USER_PASSWORD', 'secret')),
            'slug' => 'doe-john',
            'image' => null,
            'api_token' => str_random(60)
        ]);
    }
}

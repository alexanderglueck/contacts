<?php

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
        DB::table('users')->insert([
            'name' => 'Alexander Glück',
            'email' => 'alex.glueck@gmx.at',
            'password' => bcrypt(env('USER_PASSWORD')),
            'slug' => 'glueck-alexander',
            'image' => null,
            'api_token' => str_random(60)
        ]);
    }
}

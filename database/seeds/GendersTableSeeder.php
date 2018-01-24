<?php

use App\Models\Gender;
use Illuminate\Database\Seeder;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genders')->insert([
            [
                'id' => Gender::MALE,
                'gender' => 'male'
            ],
            [
                'id' => Gender::FEMALE,
                'gender' => 'female'
            ]
        ]);
    }
}

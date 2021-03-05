<?php

namespace Database\Seeders;

use App\Models\Gender;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

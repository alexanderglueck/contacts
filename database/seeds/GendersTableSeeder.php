<?php

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
                'gender' => 'Männlich'
            ],[
                'gender' => 'Weiblich'
            ]
        ]);
    }
}

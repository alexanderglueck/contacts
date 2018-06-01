<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(GendersTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PlansTableSeeder::class);
    }
}

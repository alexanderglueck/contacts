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
      //  $this->call(UsersTableSeeder::class);
//        $this->call(TeamsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(GendersTableSeeder::class);
//        $this->call(ContactsTableSeeder::class);
//        $this->call(ContactAddressesSeeder::class);
        $this->call(PermissionsTableSeeder::class);
//        $this->call(RolesTableSeeder::class);
    }
}

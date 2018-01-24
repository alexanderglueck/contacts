<?php

use Illuminate\Database\Seeder;

class ContactAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contact_addresses')->insert([
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'contact_id' => 1,
            'name' => 'Home',
            'street' => 'Blumengasse 278',
            'zip' => 8462,
            'city' => 'Gamlitz',
            'state' => 'Steiermark',
            'created_by' => 1,
            'updated_by' => 1,
            'country_id' => 164,
            'slug' => 'home'
        ]);
    }
}

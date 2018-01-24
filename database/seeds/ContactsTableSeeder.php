<?php

use Illuminate\Database\Seeder;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contacts')->insert([
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'lastname' => 'Glück',
                'firstname' => 'Alexander',
                'created_by' => 1,
                'updated_by' => 1,
                'company' => 'Körbler GmbH',
                'job' => 'Softwareentwickler',
                'department' => 'Team KundenMeister',
                'title' => '',
                'title_after' => '',
                'salutation' => 'Herr',
                'gender_id' => 1,
                'nickname' => null,
                'slug' => 'glueck-alexander',
                'image' => null
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'lastname' => 'Glück',
                'firstname' => 'Maria',
                'created_by' => 1,
                'updated_by' => 1,
                'company' => 'Raiffeisenbank Gamlitz',
                'job' => 'Angestellte',
                'department' => 'Kundenberater',
                'title' => '',
                'title_after' => '',
                'salutation' => 'Frau',
                'gender_id' => 2,
                'nickname' => 'Mama',
                'slug' => 'glueck-maria',
                'image' => null
            ]
        ]);
    }
}

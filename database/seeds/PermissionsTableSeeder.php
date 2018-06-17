<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'view' => [
                'contacts',
                'contactGroups',
                'calendar',
                'map',
                'announcements',
                'reports',
                'comments',
                'giftIdeas',
                'urls',
                'numbers',
                'emails',
                'dates',
                'addresses',
                'roles',
                'users',
                'activities',
                'notes'
            ],
            'create' => [
                'contacts',
                'contactGroups',
                'announcements',
                'import',
                'export',
                'comments',
                'giftIdeas',
                'urls',
                'numbers',
                'emails',
                'dates',
                'addresses',
                'roles',
                'notes'
            ],
            'edit' => [
                'contacts',
                'contactGroups',
                'announcements',
                'comments',
                'giftIdeas',
                'urls',
                'numbers',
                'emails',
                'dates',
                'addresses',
                'roles',
                'users',
                'notes'
            ],
            'delete' => [
                'contacts',
                'contactGroups',
                'announcements',
                'comments',
                'giftIdeas',
                'urls',
                'numbers',
                'emails',
                'dates',
                'addresses',
                'roles',
                'users',
                'notes'
            ],
            'invite' => [
                'users'
            ],
            'impersonate' => [
                'users'
            ]
        ];

        foreach ($permissions as $action => $entities) {
            foreach ($entities as $entity) {
                DB::table('permissions')->insert([
                    'name' => $action . ' ' . $entity,
                    'guard_name' => 'web',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}

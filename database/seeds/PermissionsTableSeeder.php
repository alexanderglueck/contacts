<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
                'users'
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
                'roles'
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
                'users'
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
                'users'
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
                Permission::create([
                    'name' => $action . ' ' . $entity
                ]);
            }
        }

    }
}

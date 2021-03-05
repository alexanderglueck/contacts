<?php

namespace Database\Seeders;

use App\Models\Permission;
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
                'notes',
                'calls'
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
                'notes',
                'calls'
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
                'notes',
                'calls'
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
                'notes',
                'calls'
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
                Permission::firstOrCreate([
                    'name' => $action . ' ' . $entity
                ]);
            }
        }
    }
}

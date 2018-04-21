<?php

use App\Models\User;
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
                'users',
                'activities'
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

        $user = User::firstOrFail();

        auth()->login($user);

        foreach ($permissions as $action => $entities) {
            foreach ($entities as $entity) {
                Permission::create([
                    'name' => $action . ' ' . $entity
                ]);
            }
        }
    }
}

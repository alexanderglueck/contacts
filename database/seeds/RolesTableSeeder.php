<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Auth::loginUsingId(1);

        $role = Role::create([
            'name' => 'admin',
            'team_id' => 1
        ]);

        $role->syncPermissions(Permission::all());

        $user = User::find(1);

        $user->assignRole($role);
    }
}

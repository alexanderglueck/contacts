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
        $user = User::firstOrFail();

        auth()->login($user);

        $team = $user->currentTeam;

        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
            'team_id' => $team->id
        ]);

        $role->syncPermissions(Permission::all());

        $user->assignRole($role);
    }
}

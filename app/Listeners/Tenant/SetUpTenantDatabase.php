<?php

namespace App\Listeners\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Tenant\Models\Tenant;
use App\Events\Tenant\TenantWasSetUp;
use Illuminate\Support\Facades\Artisan;
use App\Events\Tenant\TenantDatabaseWasCreated;

class SetUpTenantDatabase
{
    /**
     * Handle the event.
     *
     * @param  TenantDatabaseWasCreated $event
     *
     * @return void
     */
    public function handle(TenantDatabaseWasCreated $event)
    {
        if ($this->migrate($event->tenant)) {
//            $this->seed($event->tenant);
        }

        $this->setupPermissions($event->user, $event->tenant);

        event(new TenantWasSetUp($event->tenant));
    }

    protected function migrate(Tenant $tenant)
    {
        $migration = Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id]
        ]);

        return $migration === 0;
    }

    /**
     * @param User   $user
     * @param Tenant $tenant
     */
    protected function setupPermissions(User $user, Tenant $tenant)
    {
        $role = Role::create([
            'name' => 'admin',
            'team_id' => $tenant->id
        ]);

        $role->syncPermissions(Permission::all());

        $user->assignRole($role);
    }

    protected function seed(Tenant $tenant)
    {
        $migration = Artisan::call('tenants:seed', [
            '--tenants' => [$tenant->id]
        ]);

        return $migration === 0;
    }
}

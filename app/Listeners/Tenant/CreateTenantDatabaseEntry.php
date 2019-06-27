<?php

namespace App\Listeners\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Tenant\Models\Tenant;
use App\ContactIndexConfigurator;
use App\Events\Auth\UserSignedUp;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTenantDatabaseEntry implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    protected $tries = 5;

    /**
     * Handle the event.
     *
     * @param TenantWasCreated $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle(TenantWasCreated $event)
    {
        /*
         * Setup tenant database
         */
        //  $this->seed($event->tenant);

        /*
         * Setup roles and permissions
         */
        $this->setupPermissions($event->user, $event->tenant);

        /*
         * Create elastic search index
         */
        $this->setupSearchIndex($event->tenant);

        /*
         * Finish tenant setup
         */
        $event->tenant->update([
            'created' => true
        ]);

        /*
         * If the user is not activated the event was fired in the
         * RegisterController. We should now notify the user that his account
         * was set up.
         */
        if ($event->user->isNotActivated()) {
            event(new UserSignedUp($event->user));
        }
    }

    /**
     * @param Tenant $tenant
     *
     * @return bool
     */
    protected function seed(Tenant $tenant)
    {
        $seeding = Artisan::call('db:seed', [
            '--datab' => [$tenant->id]
        ]);

        return $seeding === 0;
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

    /**
     * @param Tenant $tenant
     */
    protected function setupSearchIndex(Tenant $tenant): void
    {
        config()->set('scout.prefix', 'tenant_' . $tenant->id . '_');

        Artisan::call('elastic:create-index', [
            'index-configurator' => ContactIndexConfigurator::class
        ]);
    }
}

<?php

namespace App\Listeners\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Tenant\Models\Tenant;
use App\Events\Auth\UserSignedUp;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Support\Facades\Artisan;
use App\Tenant\Database\DatabaseCreator;
use App\Tenant\Database\DatabaseManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTenantDatabase implements ShouldQueue
{
    /**
     * @var DatabaseCreator
     */
    protected $databaseCreator;

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    protected $tries = 5;

    /**
     * Create the event listener.
     *
     * @param DatabaseCreator $databaseCreator
     * @param DatabaseManager $databaseManager
     */
    public function __construct(
        DatabaseCreator $databaseCreator,
        DatabaseManager $databaseManager
    ) {
        $this->databaseCreator = $databaseCreator;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Handle the event.
     *
     * @param  TenantWasCreated $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle(TenantWasCreated $event)
    {
        /*
         * Create tenant database
         */
        if ( ! $this->databaseCreator->create($event->tenant)) {
            throw new \Exception('Database failed to be created.');
        }

        /*
         * Setup tenant database
         */
        if ($this->migrate($event->tenant)) {
            $this->seed($event->tenant);
        }

        /*
         * Configure the required tenant connection.
         */
        $this->databaseManager->createConnection($event->tenant);

        /*
         * Setup roles and permissions
         */
        $this->setupPermissions($event->user, $event->tenant);

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
    protected function migrate(Tenant $tenant)
    {
        $migration = Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id]
        ]);

        return $migration === 0;
    }

    /**
     * @param Tenant $tenant
     *
     * @return bool
     */
    protected function seed(Tenant $tenant)
    {
        $seeding = Artisan::call('tenants:seed', [
            '--tenants' => [$tenant->id]
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
}

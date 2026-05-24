<?php

namespace App\Listeners\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Tenant\Models\Tenant;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;
use MeiliSearch\Client;
use MeiliSearch\Exceptions\ApiException;
use MeiliSearch\MeiliSearch;

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
        if ( ! app()->environment('testing')) {
            $this->setupSearchIndex($event->tenant);
        }

        /*
         * Finish tenant setup
         */
        $event->tenant->update([
            'created' => true
        ]);
    }

    /**
     * @param Tenant $tenant
     *
     * @return bool
     */
    protected function seed(Tenant $tenant)
    {
        $seeding = Artisan::call('db:seed', [
            '--database' => [$tenant->id]
        ]);

        return $seeding === 0;
    }

    /**
     * @param User $user
     * @param Tenant $tenant
     */
    protected function setupPermissions(User $user, Tenant $tenant)
    {
        // The listener implements ShouldQueue with $tries=5. firstOrCreate
        // keeps a retry from creating a second 'admin' role on the same
        // tenant (we saw this happen — duplicate admin roles in the
        // tenant-scoped role list, both for the same team_id).
        $role = Role::withoutGlobalScopes()
            ->firstOrCreate(
                ['name' => 'admin', 'team_id' => $tenant->id],
            );

        $role->permissions()->sync(Permission::pluck('id')->all());

        if (! $user->roles()->where('roles.id', $role->id)->exists()) {
            $user->assignRole($role);
        }
    }

    /**
     * @param Tenant $tenant
     */
    protected function setupSearchIndex(Tenant $tenant): void
    {
        config()->set('scout.prefix', 'tenant_' . $tenant->id . '_');

        $this->dropExistingIndex();

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $client->createIndex(config('scout.prefix') . 'contact');
    }

    protected function dropExistingIndex(): void
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        try {
            $index = $client->getIndex(config('scout.prefix') . 'contact');
            $client->deleteIndex(config('scout.prefix') . 'contact');
        } catch (ApiException $e) {
            // Index not found
        }
    }
}

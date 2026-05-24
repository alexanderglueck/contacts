<?php

namespace App\Listeners\Tenant;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Tenant\Models\Tenant;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        // The listener implements ShouldQueue with $tries=5. Every step in
        // here has to be re-entrant — a retry mustn't double-insert.

        // firstOrCreate keeps a retry from creating a second 'admin' role on
        // the same tenant. withoutGlobalScopes because the user's
        // current_team_id may still be the OLD team at this point (the
        // controller hasn't switched them over yet), and Role has a
        // BelongsToTenantScope that filters by current_team_id.
        $role = Role::withoutGlobalScopes()
            ->firstOrCreate(['name' => 'admin', 'team_id' => $tenant->id]);

        $role->permissions()->sync(Permission::pluck('id')->all());

        // assignRole() does a raw attach() which would PK-violate on retry.
        // We can't use $user->roles()->syncWithoutDetaching([$role->id])
        // either: its internal "what's currently attached?" select goes
        // through the Role model and trips the BelongsToTenantScope.
        // Hitting the pivot table directly sidesteps both problems.
        DB::table('role_user')->updateOrInsert(
            ['role_id' => $role->id, 'user_id' => $user->id],
            [],
        );

        $user->forgetCachedPermissions();
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

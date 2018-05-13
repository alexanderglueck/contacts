<?php

namespace App\Listeners\Tenant;

use App\Tenant\Models\Tenant;
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
    }

    protected function migrate(Tenant $tenant)
    {
        $migration = Artisan::call('tenants:migrate', [
            '--tenants' => [$tenant->id]
        ]);

        return $migration === 0;
    }

    protected function seed(Tenant $tenant)
    {
        $migration = Artisan::call('tenants:seed', [
            '--tenants' => [$tenant->id]
        ]);

        return $migration === 0;
    }
}

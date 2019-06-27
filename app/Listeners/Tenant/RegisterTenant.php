<?php

namespace App\Listeners\Tenant;

use App\Tenant\Manager;
use App\Events\Tenant\TenantIdentified;

class RegisterTenant
{
    /**
     * Handle the event.
     *
     * @param  TenantIdentified $event
     *
     * @return void
     */
    public function handle(TenantIdentified $event)
    {
        /*
         * Set the tenant
         */
        app(Manager::class)->setTenant($event->tenant);

        /*
         * Set the scout search prefix
         */
        config()->set('scout.prefix', 'tenant_' . $event->tenant->id . '_');
    }
}

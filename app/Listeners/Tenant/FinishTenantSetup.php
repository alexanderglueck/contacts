<?php

namespace App\Listeners\Tenant;

use App\Events\Tenant\TenantWasSetUp;
use Illuminate\Contracts\Queue\ShouldQueue;

class FinishTenantSetup implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  TenantWasSetUp $event
     *
     * @return void
     */
    public function handle(TenantWasSetUp $event)
    {
        $event->tenant->update([
            'created' => true
        ]);
    }
}

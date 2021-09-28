<?php

namespace App\Events\Tenant;

use App\Tenant\Models\Tenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TenantIdentified
{
    use Dispatchable, SerializesModels;


    /**
     * Create a new event instance.
     */
    public function __construct(
        public Tenant $tenant
    )
    {
        //
    }
}

<?php

namespace App\Events\Tenant;

use App\Models\User;
use App\Tenant\Models\Tenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TenantWasCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Tenant $tenant, public User $user)
    {
        //
    }
}

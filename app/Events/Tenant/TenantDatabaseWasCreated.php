<?php

namespace App\Events\Tenant;

use App\Models\User;
use App\Tenant\Models\Tenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TenantDatabaseWasCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var Tenant
     */
    public $tenant;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Tenant $tenant
     * @param User   $user
     */
    public function __construct(Tenant $tenant, User $user)
    {
        $this->tenant = $tenant;
        $this->user = $user;
    }
}

<?php

namespace App\Tenant;

use App\Tenant\Models\Tenant;

class Manager
{
    protected $tenant;

    public function setTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * @return mixed
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    public function hasTenant()
    {
        return isset($this->tenant);
    }
}

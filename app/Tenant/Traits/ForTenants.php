<?php

namespace App\Tenant\Traits;

trait ForTenants
{
    public function getConnectionName()
    {
        return 'tenant';
    }
}

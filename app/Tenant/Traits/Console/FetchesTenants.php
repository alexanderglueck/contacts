<?php

namespace App\Tenant\Traits\Console;

use App\Models\Team;

trait FetchesTenants
{
    public function tenants($ids = null)
    {
        $tenants = Team::query();

        if ($ids) {
            $tenants = $tenants->whereIn('id', $ids);
        }

        return $tenants;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.05.2018
 * Time: 14:35
 */

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

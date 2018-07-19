<?php

namespace App\Tenant\Database;

use App\Tenant\Models\Tenant;
use Illuminate\Support\Facades\DB;

class DatabaseCreator
{
    public function create(Tenant $tenant)
    {
        try {
            return DB::connection('system')->statement(sprintf(
                'CREATE DATABASE `%s`',
                $this->getTenantDatabaseName($tenant)
            ));
        } catch (\Exception $ex) {
            return false;
        }
    }

    private function getTenantDatabaseName(Tenant $tenant)
    {
        return config('contacts.tenant.prefix') . $tenant->id;
    }
}

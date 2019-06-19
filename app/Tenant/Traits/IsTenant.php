<?php

namespace App\Tenant\Traits;

use Illuminate\Support\Str;
use App\Tenant\Models\Tenant;
use App\Models\TenantConnection;

trait IsTenant
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            $tenant->uuid = (string) Str::uuid();;
        });

        static::created(function ($tenant) {
            $tenant->tenantConnection()->save(
                static::newDatabaseConnection($tenant)
            );
        });
    }

    protected static function newDatabaseConnection(Tenant $tenant)
    {
        return new TenantConnection([
            'database' => config('contacts.tenant.prefix') . $tenant->id
        ]);
    }

    public function tenantConnection()
    {
        return $this->hasOne(TenantConnection::class, 'team_id', 'id');
    }
}

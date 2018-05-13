<?php

namespace App\Tenant\Traits;

use Webpatser\Uuid\Uuid;
use App\TenantConnection;
use App\Tenant\Models\Tenant;

trait IsTenant
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            $tenant->uuid = Uuid::generate(4);
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

<?php

namespace App\Tenant\Traits;

use Illuminate\Support\Str;

trait IsTenant
{
    public static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            $tenant->uuid = (string) Str::uuid();
        });
    }
}

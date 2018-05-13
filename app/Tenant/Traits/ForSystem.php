<?php

namespace App\Tenant\Traits;

trait ForSystem
{
    public function getConnectionName()
    {
        return config('database.default');
    }
}

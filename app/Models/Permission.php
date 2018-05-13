<?php

namespace App\Models;

use App\Tenant\Traits\ForSystem;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use ForSystem;
}

<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasRolesAndPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        $this->belongsToMany(Permission::class);
    }

    public function hasAnyPermission($permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->checkPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    public function checkPermissionTo($permission)
    {
        try {
            return $this->hasPermissionTo($permission);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasPermissionTo($permissionName): bool
    {
        $permission = Permission::where(['name' => $permissionName])->first();

        if ( ! $permission instanceof Permission) {
            throw new \Exception("Permission does not exist");
        }

        return $this->hasPermissionViaRole($permission);
    }

    protected function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }
        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }
        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }
}

<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasPermissions
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
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
            throw new \Exception('Permission does not exist');
        }

        if ($this instanceof Role) {
            return $this->hasDirectPermission($permission);
        }

        return $this->hasPermissionViaRole($permission);
    }

    /**
     * Determine if the model has the given permission.
     *
     * @param string|int|\App\Models\Permission $permission
     *
     * @return bool
     */
    public function hasDirectPermission($permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::where(['name' => $permission])->first();
            if ( ! $permission) {
                return false;
            }
        }

        if (is_int($permission)) {
            $permission = Permission::find($permission);
            if ( ! $permission) {
                return false;
            }
        }

        if ( ! $permission instanceof Permission) {
            return false;
        }

        return $this->permissions->contains('id', $permission->id);
    }

    protected function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    protected function getStoredPermission($permissions)
    {
        if (is_numeric($permissions)) {
            return Permission::find($permissions);
        }
        if (is_string($permissions)) {
            return Permission::where(['name' => $permissions])->first();
        }
        if (is_array($permissions)) {
            return Permission::whereIn('name', $permissions)->get();
        }

        return $permissions;
    }

    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->permissions()->sync($permissions, false);
            $model->load('permissions');
        } else {
            $class = \get_class($model);
            $class::saved(
                function ($object) use ($permissions, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->permissions()->sync($permissions, false);
                    $object->load('permissions');
                    $modelLastFiredOn = $object;
                }
            );
        }

        return $this;
    }
}

<?php

namespace App\Models\Traits;

use Exception;
use App\Models\Permission;
use App\Permission\PermissionRegistrar;

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
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasPermissionTo($permissionName): bool
    {
        if (is_string($permissionName)) {
            $permission = Permission::findByName($permissionName);
        }

        if (is_int($permissionName)) {
            $permission = Permission::findById($permissionName);
        }

        if ( ! $permission instanceof Permission) {
            throw new Exception('Permission does not exist');
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
            $permission = Permission::findByName($permission);
            if ( ! $permission) {
                return false;
            }
        }

        if (is_int($permission)) {
            $permission = Permission::findById($permission);
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
            return Permission::findById($permissions);
        }

        if (is_string($permissions)) {
            return Permission::findByName($permissions)->first();
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

        $this->forgetCachedPermissions();

        return $this;
    }

    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    /**
     * Forget the cached permissions.
     */
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

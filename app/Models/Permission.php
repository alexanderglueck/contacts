<?php

namespace App\Models;

use Exception;
use App\Models\Traits\HasRoles;
use Illuminate\Support\Collection;
use App\Permission\PermissionRegistrar;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use HasRoles;

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     *
     * @return Permission
     * @throws Exception
     */
    public static function findByName(string $name)
    {
        $permission = static::getPermissions(['name' => $name])->first();
        if ( ! $permission) {
            throw new Exception('Permission does not exist');
        }

        return $permission;
    }

    /**
     * Find a permission by its id (and optionally guardName).
     *
     * @param int $id
     *
     * @return Permission
     * @throws Exception
     */
    public static function findById(int $id)
    {
        $permission = static::getPermissions(['id' => $id])->first();
        if ( ! $permission) {
            throw new Exception('Permission does not exist');
        }

        return $permission;
    }

    /**
     * Get the current cached permissions.
     *
     * @param array $params
     *
     * @return Collection
     */
    protected static function getPermissions(array $params = []): Collection
    {
        return app(PermissionRegistrar::class)->getPermissions($params);
    }
}

<?php

namespace App\Permission;

use App\Models\Permission;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Authorizable;

class PermissionRegistrar
{
    /** @var Gate */
    protected $gate;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var Collection */
    protected $permissions;

    /** @var int */
    public static $cacheExpirationTime;

    /** @var string */
    private static $cacheKey;

    /**
     * PermissionRegistrar constructor.
     *
     * @param Gate         $gate
     * @param CacheManager $cacheManager
     */
    public function __construct(Gate $gate, CacheManager $cacheManager)
    {
        $this->gate = $gate;

        $this->cacheManager = $cacheManager;

        $this->initializeCache();
    }

    protected function initializeCache()
    {
        self::$cacheExpirationTime = 60 * 60;

        self::$cacheKey = 'permissions';

        $this->cache = $this->cacheManager->store();
    }

    /**
     * Register the permission check method on the gate.
     *
     * @return bool
     */
    public function registerPermissions(): bool
    {
        $this->gate->before(function (Authorizable $user, string $ability) {
            try {
                if (method_exists($user, 'hasPermissionTo')) {
                    return $user->hasPermissionTo($ability) ?: null;
                }
            } catch (\Exception $e) {
            }
        });

        return true;
    }

    /**
     * Flush the cache.
     */
    public function forgetCachedPermissions()
    {
        $this->permissions = null;

        return $this->cache->forget(self::$cacheKey);
    }

    /**
     * Get the permissions based on the passed params.
     *
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions(array $params = []): Collection
    {
        if ($this->permissions === null) {
            $this->permissions = $this->cache->remember(self::$cacheKey . "-" . auth()->user()->current_team_id, self::$cacheExpirationTime, function () {
                return Permission::with('roles')->get();
            });
        }

        $permissions = clone $this->permissions;

        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }

        return $permissions;
    }
}

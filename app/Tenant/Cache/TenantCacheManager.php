<?php

namespace App\Tenant\Cache;

use App\Tenant\Manager;
use Illuminate\Cache\CacheManager;

class TenantCacheManager extends CacheManager
{
    public function __call($method, $parameters)
    {
        if ($method == 'tags') {
            return $this->store()->tags(
                array_merge($this->getTenantCacheTag(), ...$parameters)
            );
        }

        if ($method == 'getStore') {
            return $this->store()->$method(...$parameters);
        }

        return $this->store()->tags($this->getTenantCacheTag())->$method(...$parameters);
    }

    protected function getTenantCacheTag()
    {
        return ['tenant_' . $this->app(Manager::class)->getTenant()->uuid];
    }
}

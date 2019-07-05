<?php

namespace App\Providers;

use App\Permission\PermissionRegistrar;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param PermissionRegistrar $permissionLoader
     *
     * @return void
     */
    public function boot(PermissionRegistrar $permissionLoader)
    {
        $permissionLoader->registerPermissions();

        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($permissionLoader) {
            return $permissionLoader;
        });
    }
}

<?php

namespace App\Providers;

use App\Imports\MappingHolder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MappingHolder::class, function () {
            return new MappingHolder();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Fix for the "Specified key was too long" error
         *
         * @see https://laravel-news.com/laravel-5-4-key-too-long-error
         */
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}

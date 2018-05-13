<?php

namespace App\Listeners\Tenant;

use App\Events\Tenant\TenantIdentified;
use App\Tenant\Models\Tenant;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UseTenantFilesystem
{
    /**
     * @var Factory
     */
    private $filesystem;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Factory $filesystem)
    {

        $this->filesystem = $filesystem;
    }

    /**
     * Handle the event.
     *
     * @param  TenantIdentified $event
     *
     * @return void
     */
    public function handle(TenantIdentified $event)
    {
        $config = $this->getFilesystemConfig($event->tenant);

        $this->filesystem->set('tenant', $this->createDriver($config));
    }

    protected function createDriver($config)
    {
        $method = $this->getCreationMethod();

        return $this->filesystem->{$method}($config);
    }

    protected function getFilesystemConfig(Tenant $tenant)
    {
        $config = config('filesystems.disks.' . config('filesystems.default'));

        $config['root'] = $tenant->uuid;

        return $config;
    }

    protected function getCreationMethod()
    {
        return "create" . ucfirst(config('filesystems.default')) . "Driver";
    }
}

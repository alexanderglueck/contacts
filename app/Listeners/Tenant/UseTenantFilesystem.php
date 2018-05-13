<?php

namespace App\Listeners\Tenant;

use App\Tenant\Models\Tenant;
use App\Events\Tenant\TenantIdentified;
use Illuminate\Contracts\Filesystem\Factory;

class UseTenantFilesystem
{
    /**
     * @var Factory
     */
    private $filesystem;

    /**
     * Create the event listener.
     *
     * @param Factory $filesystem
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
        return 'create' . ucfirst(config('filesystems.default')) . 'Driver';
    }
}

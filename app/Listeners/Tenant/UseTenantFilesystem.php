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

    /**
     * @param Tenant $tenant
     *
     * @return array
     */
    protected function getFilesystemConfig(Tenant $tenant)
    {
        $config = config('filesystems.disks.' . config('filesystems.default'));

        $config['root'] = $config['root'] . DIRECTORY_SEPARATOR . $tenant->uuid;

        return $config;
    }

    /**
     * @param array $config
     *
     * @return mixed
     */
    protected function createDriver($config)
    {
        $method = $this->getCreationMethod();

        return $this->filesystem->{$method}($config);
    }

    /**
     * @return string
     */
    protected function getCreationMethod()
    {
        return 'create' . ucfirst(config('filesystems.default')) . 'Driver';
    }
}

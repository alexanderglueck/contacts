<?php

namespace App\Tenant\Database;

use App\Tenant\Models\Tenant;
use Illuminate\Database\DatabaseManager as BaseDatabaseManager;

class DatabaseManager
{
    /**
     * @var BaseDatabaseManager
     */
    protected $db;

    /**
     * DatabaseManager constructor.
     *
     * @param BaseDatabaseManager $db
     */
    public function __construct(BaseDatabaseManager $db)
    {
        $this->db = $db;
    }

    public function createConnection(Tenant $tenant)
    {
        config()->set('database.connections.tenant', $this->getTenantConnection($tenant));
    }

    public function connectToTenant()
    {
        $this->db->reconnect('tenant');
    }

    public function connectToSystem()
    {
        $this->db->reconnect(config('database.default'));
    }

    public function purge()
    {
        $this->db->purge('tenant');
    }

    protected function getTenantConnection($tenant)
    {
        return array_merge(
            config()->get($this->getConfigConnectionPath()),
            $tenant->tenantConnection->only('database')
        );
    }

    protected function getDefaultConnectionName()
    {
        return config('database.default');
    }

    protected function getConfigConnectionPath()
    {
        return sprintf('database.connections.%s', $this->getDefaultConnectionName());
    }
}

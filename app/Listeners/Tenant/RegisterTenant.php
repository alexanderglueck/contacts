<?php

namespace App\Listeners\Tenant;

use App\Tenant\Manager;
use App\Events\Tenant\TenantIdentified;
use App\Tenant\Database\DatabaseManager;

class RegisterTenant
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * Create the event listener.
     *
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->db = $databaseManager;
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
        app(Manager::class)->setTenant($event->tenant);

        $this->db->createConnection($event->tenant);

        config()->set('scout.prefix', 'tenant_' . $event->tenant->id . '_');
    }
}

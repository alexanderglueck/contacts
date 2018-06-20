<?php

namespace App\Console\Commands\Scout;

use App\Tenant\Database\DatabaseManager;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Scout\Console\ImportCommand;
use App\Tenant\Traits\Console\FetchesTenants;
use App\Tenant\Traits\Console\AcceptsMultipleTenants;

class Import extends ImportCommand
{
    use FetchesTenants;
    use AcceptsMultipleTenants;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data for tenants';

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * Create a new command instance.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct();
        $this->setName('tenants:import {model}');

        $this->specifyParameters();

        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @param Dispatcher $events
     *
     * @return mixed
     */
    public function handle(Dispatcher $events)
    {
        $this->tenants($this->option('tenants'))->each(function ($tenant) use ($events) {
            /*
             * Purge the tenant connection (connection could already be
             * established (eg. using artisan queue:work))
             */
            $this->db->purge();

            $this->db->createConnection($tenant);
            $this->db->connectToTenant();

            /*
             * Set the scout prefix so the parent handle method imports the
             * models into the correct index
             */
            config()->set('scout.prefix', 'tenant_' . $tenant->id . '_');

            parent::handle($events);

            $this->db->purge();
        });
    }
}

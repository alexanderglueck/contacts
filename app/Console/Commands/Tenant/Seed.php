<?php

namespace App\Console\Commands\Tenant;

use App\Tenant\Database\DatabaseManager;
use App\Tenant\Traits\Console\FetchesTenants;
use Illuminate\Database\Console\Seeds\SeedCommand;
use App\Tenant\Traits\Console\AcceptsMultipleTenants;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class Seed extends SeedCommand
{
    use FetchesTenants;
    use AcceptsMultipleTenants;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds tenant databases';

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * Create a new command instance.
     *
     * @param Resolver        $resolver
     * @param DatabaseManager $db
     */
    public function __construct(Resolver $resolver, DatabaseManager $db)
    {
        parent::__construct($resolver);

        $this->setName('tenants:seed');

        $this->specifyParameters();

        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $this->confirmToProceed()) {
            return;
        }

        /*
         * Required for the parent::handle() call.
         * Uses the tenant database connection to seed the database.
         * Set the class option to specify the tenant seeder entry point.
         */
        $this->input->setOption('database', 'tenant');
        $this->input->setOption('class', 'TenantDatabaseSeeder');

        /*
         * Iterate over every (specified) tenant and seed the database
         */
        $this->tenants($this->option('tenants'))->each(function ($tenant) {
            /*
             * Purge the tenant connection (connection could already be
             * established (eg. using artisan queue:work)
             */
            $this->db->purge();

            /*
             * Create a new tenant connection
             */
            $this->db->createConnection($tenant);
            $this->db->connectToTenant();

            parent::handle();

            $this->db->purge();
        });

        $this->db->connectToSystem();
    }
}

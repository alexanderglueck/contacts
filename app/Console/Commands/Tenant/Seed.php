<?php

namespace App\Console\Commands\Tenant;

use App\Tenant\Database\DatabaseManager;
use App\Tenant\Traits\Console\FetchesTenants;
use Illuminate\Database\Console\Seeds\SeedCommand;
use App\Tenant\Traits\Console\AcceptsMultipleTenants;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Symfony\Component\Console\Input\InputOption;

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

        $this->input->setOption('database', 'tenant');
        $this->input->setOption('class', 'TenantDatabaseSeeder');

        $this->tenants($this->option('tenants'))->each(function ($tenant) {
            $this->db->createConnection($tenant);
            $this->db->connectToTenant();

            parent::handle();

            $this->db->purge();
        });

        $this->db->connectToSystem();
    }
}

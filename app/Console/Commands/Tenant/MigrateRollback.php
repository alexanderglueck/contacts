<?php

namespace App\Console\Commands\Tenant;

use App\Tenant\Database\DatabaseManager;
use Illuminate\Database\Migrations\Migrator;
use App\Tenant\Traits\Console\FetchesTenants;
use App\Tenant\Traits\Console\AcceptsMultipleTenants;
use Illuminate\Database\Console\Migrations\RollbackCommand;

class MigrateRollback extends RollbackCommand
{
    use FetchesTenants;
    use AcceptsMultipleTenants;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations for tenants';

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * Create a new command instance.
     *
     * @param Migrator        $migrator
     * @param DatabaseManager $db
     */
    public function __construct(Migrator $migrator, DatabaseManager $db)
    {
        parent::__construct($migrator);

        $this->setName('tenants:rollback');

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
         * Uses the tenant database connection to rollback the migrations
         */
        $this->input->setOption('database', 'tenant');

        /*
         * Iterate over every (specified) tenant and rollback the migrations
         */
        $this->tenants($this->option('tenants'))->each(function ($tenant) {
            $this->db->createConnection($tenant);
            $this->db->connectToTenant();

            parent::handle();

            $this->db->purge();
        });

        $this->db->connectToSystem();
    }

    protected function getMigrationPaths()
    {
        return [
            database_path('migrations/tenant')
        ];
    }
}

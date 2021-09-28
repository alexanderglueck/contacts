<?php

namespace App\Console\Commands\Scout;

use Laravel\Scout\Console\ImportCommand;
use Illuminate\Contracts\Events\Dispatcher;
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
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName('tenants:import {model}');

        $this->specifyParameters();
    }

    /**
     * Execute the console command.
     *
     * @param Dispatcher $events
     * @return int
     */
    public function handle(Dispatcher $events): int
    {
        $this->tenants($this->option('tenants'))->each(function ($tenant) use ($events) {
            /*
             * Set the scout prefix so the parent handle method imports the
             * models into the correct index
             */
            config()->set('scout.prefix', 'tenant_' . $tenant->id . '_');

            parent::handle($events);
        });

        return 0;
    }
}

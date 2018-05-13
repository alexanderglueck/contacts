<?php

namespace App\Listeners\Tenant;

use App\Events\Tenant\TenantDatabaseWasCreated;
use App\Events\Tenant\TenantWasCreated;
use App\Tenant\Database\DatabaseCreator;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTenantDatabase
{
    /**
     * @var DatabaseCreator
     */
    protected $databaseCreator;

    /**
     * Create the event listener.
     *
     * @param DatabaseCreator $databaseCreator
     */
    public function __construct(DatabaseCreator $databaseCreator)
    {
        //
        $this->databaseCreator = $databaseCreator;
    }

    /**
     * Handle the event.
     *
     * @param  TenantWasCreated $event
     *
     * @return void
     * @throws \Exception
     */
    public function handle(TenantWasCreated $event)
    {
        if ( ! $this->databaseCreator->create($event->tenant)) {
            throw new \Exception('Database failed to be created.');
        }

        event(new TenantDatabaseWasCreated($event->tenant));
    }
}

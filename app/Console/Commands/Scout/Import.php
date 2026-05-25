<?php

namespace App\Console\Commands\Scout;

use App\Tenant\Traits\Console\AcceptsMultipleTenants;
use App\Tenant\Traits\Console\FetchesTenants;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Input\InputArgument;

class Import extends Command
{
    use AcceptsMultipleTenants;
    use FetchesTenants;

    protected $name = 'tenants:import';

    protected $description = 'Import a Searchable model into each tenant\'s prefixed Scout index';

    public function handle(): int
    {
        $class = $this->argument('model');

        if (! class_exists($class)) {
            $this->error("Model class [{$class}] does not exist.");

            return 1;
        }

        $instance = new $class;

        if (! $instance instanceof Model || ! method_exists($instance, 'searchable')) {
            $this->error("Model [{$class}] is not Searchable.");

            return 1;
        }

        $chunkSize = (int) config('scout.chunk.searchable', 500);

        $this->tenants($this->option('tenants'))->each(function ($tenant) use ($class, $chunkSize) {
            // Per-tenant Scout prefix mirrors what App\Listeners\Tenant\RegisterTenant
            // does on a normal HTTP request — Contact::searchableAs() reads this.
            config()->set('scout.prefix', 'tenant_' . $tenant->id . '_');

            $this->info("Tenant {$tenant->id} ({$tenant->name}) → index prefix tenant_{$tenant->id}_");

            // Bypass BelongsToTenantScope (auth-based, no-op in CLI) and filter
            // explicitly by team_id so each tenant's index holds only its own rows.
            $query = $class::query()->withoutGlobalScopes()->where('team_id', $tenant->id);

            $total = (clone $query)->count();
            $this->info("  importing {$total} row(s)…");

            $query->chunkById($chunkSize, function ($models) {
                $models->searchable();
            });
        });

        $this->info('Done.');

        return 0;
    }

    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::REQUIRED, 'The Searchable model class (e.g. "App\\Models\\Contact")'],
        ];
    }
}

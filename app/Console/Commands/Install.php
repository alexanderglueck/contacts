<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Question\Question;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install contacts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->welcome();

        $this->createEnvFile();

        $this->copyEnvFile();

        $credentials = $this->requestCredentials();

        $this->updateEnvironmentFile($credentials);

        $this->call('cache:clear');

        if ($this->confirm('Do you want to migrate the database?', false)) {
            $this->migrateDatabaseWithFreshCredentials($credentials);
            $this->line('Database successfully migrated.');

            if ($this->confirm('Do you want to seed the database?', false)) {
                $this->seedDatabase($credentials);
                $this->line('Database successfully seeded.');
            }
        }

        $this->call('cache:clear');

        $this->goodbye();
    }

    /**
     * Update the .env file from an array of $key => $value pairs.
     *
     * @param  array $updatedValues
     *
     * @return void
     */
    protected function updateEnvironmentFile($updatedValues)
    {
        $envFile = $this->laravel->environmentFilePath();

        foreach ($updatedValues as $key => $value) {
            if ($key === 'password' && $value == 'null') {
                $value = '';
            }

            file_put_contents($envFile, preg_replace(
                "/{$key}=(.*)/",
                "{$key}={$value}",
                file_get_contents($envFile)
            ));
        }
    }

    private function createEnvFile()
    {
        if ( ! file_exists('.env')) {
            copy('.env.example', '.env');
            $this->line('.env file successfully created');
        }
    }

    protected function migrateDatabaseWithFreshCredentials($credentials)
    {
        foreach ($credentials as $key => $value) {
            $configKey = strtolower(str_replace('DB_', '', $key));
            if ($configKey === 'password' && $value == 'null') {
                config(["database.connections.mysql.{$configKey}" => '']);
                continue;
            }

            if (in_array($configKey, ['contacts_tenant_prefix'])) {
                continue;
            }

            config(["database.connections.mysql.{$configKey}" => $value]);
        }

        config(['contacts.tenant.system' => $credentials['DB_DATABASE']]);
        config(['permission.table_names.permissions' => $credentials['DB_DATABASE'] . '.permissions']);

        $this->reconnectToDatabase();

        $this->call('cache:clear');
        $this->call('config:clear');

        $this->call('migrate');
    }

    /**
     * Request the local database details from the user.
     *
     * @return array
     */
    protected function requestCredentials()
    {
        return [
            'DB_DATABASE' => $this->ask('System database name', 'contacts_main'),
            'DB_HOST' => $this->ask('Database host', '127.0.0.1'),
            'DB_PORT' => $this->ask('Database port', 3306),
            'DB_USERNAME' => $this->ask('Database user'),
            'DB_PASSWORD' => $this->askHiddenWithDefault('Database password (leave blank for no password)'),
            'CONTACTS_TENANT_PREFIX' => $this->ask('Tenant database name prefix', 'contact_')
        ];
    }

    /**
     * Prompt the user for optional input but hide the answer from the console.
     *
     * @param  string $question
     * @param  bool   $fallback
     *
     * @return string
     */
    public function askHiddenWithDefault($question, $fallback = true)
    {
        $question = new Question($question, 'null');
        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * Display the welcome message.
     */
    protected function welcome()
    {
        $this->info('>> Welcome to the Contacts installation process! <<');
    }

    /**
     * Display the completion message.
     */
    protected function goodbye()
    {
        $this->info('>> The installation process is complete. Enjoy your new contacts installation! <<');
    }

    protected function seedDatabase()
    {
        $this->call('db:seed');
    }

    public function copyEnvFile(): void
    {
        if (strlen(config('app.key')) === 0) {
            $this->call('key:generate');
            $this->line('Secret key properly generated.');
        }
    }

    protected function reconnectToDatabase(): void
    {
        DB::disconnect('mysql');
        DB::purge('mysql');
        DB::reconnect('mysql');
    }
}

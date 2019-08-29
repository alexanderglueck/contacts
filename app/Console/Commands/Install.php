<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

        if ($this->confirm('Do you want to migrate the database?', false)) {
            $this->migrateDatabaseWithFreshCredentials($credentials);
            $this->line('Database successfully migrated.');

            if ($this->confirm('Do you want to seed the database?', false)) {
                $this->seedDatabase();
                $this->line('Database successfully seeded.');
            }
        }

        $this->clearCache();

        $this->symlinkStorage();

        $this->goodbye();
    }

    /**
     * Update the .env file from an array of $key => $value pairs.
     *
     * @param array $updatedValues
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
            'DB_DATABASE' => $this->ask('Database name', config('database.connections.mysql.database')),
            'DB_HOST' => $this->ask('Database host', config('database.connections.mysql.host')),
            'DB_PORT' => $this->ask('Database port', config('database.connections.mysql.port')),
            'DB_USERNAME' => $this->ask('Database user', config('database.connections.mysql.username')),
            'DB_PASSWORD' => $this->askHiddenWithDefault('Database password (enter null for no password)')
        ];
    }

    /**
     * Prompt the user for optional input but hide the answer from the console.
     *
     * @param string $question
     * @param bool   $fallback
     *
     * @return string
     */
    public function askHiddenWithDefault($question, $fallback = true)
    {
        $question = new Question($question, config('database.connections.mysql.password'));
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

    protected function symlinkStorage(): void
    {
        $this->call('storage:link');
    }

    protected function clearCache(): void
    {
        $this->call('cache:clear');
    }
}

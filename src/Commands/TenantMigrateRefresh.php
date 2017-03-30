<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Tenant;

class TenantMigrateRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate:refresh {tenant : Tenant name}
                            {--force : Force the operation to run when in production.}
                            {--path= : The path of migrations files to be executed.}
                            {--seed : Indicates if the seed task should be re-run.}
                            {--seeder= : The class name of the root seeder.}
                            {--step : The number of migrations to be reverted & re-run.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate a tenant database';

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
        $tenant = $this->argument('tenant');
        $force = $this->option('force');
        $path = $this->option('path');
        $seed = $this->option('seed');
        $seeder = $this->option('seeder');
        $step = $this->option('step');

        if (!$path) {
            $path = 'database/migrations/tenants';
        }

        if (!$seeder) {
            $seeder = 'DatabaseSeeder';
        }

        $loaded = Tenant::setTenantByKey($tenant);

        $database = env('TENANT_DB_CONNECTION', 'tenant');

        $this->call('migrate:refresh', [
            '--database' => $database,
            '--force' => $force,
            '--path' => $path,
            '--seed' => $seed,
            '--seeder' => $seeder,
            '--step' => $step,
        ]);
    }
}

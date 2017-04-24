<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Tenant;

class TenantMigrateRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate:rollback {tenant : Tenant name}
                            {--force : Force the operation to run when in production.}
                            {--path= : The path of migrations files to be executed.}
                            {--pretend : Dump the SQL queries that would be run.}
                            {--step : The number of migrations to be reverted.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last tenant database migration';

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
        $pretend = $this->option('pretend');
        $step = $this->option('step');

        if (!$path) {
            $path = 'database/migrations/tenants';
        }

        $loaded = Tenant::setTenantByKey($tenant);

        $database = env('TENANT_DB_CONNECTION', 'tenant');

        $this->call('migrate:rollback', [
            '--database' => $database,
            '--force' => $force,
            '--path' => $path,
            '--pretend' => $pretend,
            '--step' => $step,
        ]);
    }
}

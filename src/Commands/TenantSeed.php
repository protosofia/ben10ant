<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Tenant;

class TenantSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:db:seed {tenant : Tenant name}
                            {--force : Force the operation to run when in production.}
                            {--class : The class name of the root seeder [default: "DatabaseSeeder"]}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a tenant database';

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
        $class = $this->option('class');

        if (!$class) {
            $class = 'DatabaseSeeder';
        }

        $loaded = Tenant::setTenantByKey($tenant);

        $database = env('TENANT_DB_CONNECTION', 'tenant');

        $this->call('db:seed', [
            '--database' => $database,
            '--force' => $force,
            '--class' => $class,
        ]);
    }
}

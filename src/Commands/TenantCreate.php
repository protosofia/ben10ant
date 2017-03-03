<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\Contracts\WizardServiceAbstract;
use Protosofia\Ben10ant\DatabaseCreatorFactory;
use Protosofia\Ben10ant\StorageConfigFactory;
use Protosofia\Ben10ant\StorageWizardService;
use Protosofia\Ben10ant\DatabaseWizardService;
use Protosofia\Ben10ant\Models\TenantModel;
use Tenant;
use Webpatser\Uuid\Uuid;

class TenantCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name : Tenant name} {keyname : Tenant keyname, for auth}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a tenant';

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
        $name = $this->argument('name');
        $keyname = $this->argument('keyname');

        list($dbHandler, $dbConnection, $dbConfig) = $this->getWizardData(new DatabaseWizardService($this), $keyname);

        list(,,$storageConfig) = $this->getWizardData(new StorageWizardService($this), $keyname);

        $this->createDatabase($dbHandler, $dbConnection, $dbConfig);

        Tenant::new([
            'name' => $name,
            'keyname' => $keyname,
            'database' => json_encode($dbConfig),
            'storage' => json_encode($storageConfig),
        ]);

        $this->info("Tenant '{$name}' created successfully.");
    }

    protected function getWizardData(WizardServiceAbstract $wizard, $keyname)
    {
        $wizard->run($keyname);

        $handler = $wizard->getHandler();
        $connection = $wizard->getConnection();
        $config = $wizard->getConfig();

        return [$handler, $connection, $config];
    }

    protected function createDatabase($creator, $connection, $config)
    {
        if (!$this->confirm('Create the database now ?')) {
            return false;
        }

        $params = $config;
        $params['connection'] = $connection;

        return $creator->createDatabase($params);
    }
}

<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\DatabaseCreatorFactory;

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
        $user = $this->argument('name');
        $keyname = $this->argument('keyname');

        /* TODO: Extract database creation methods to a database wizard class */

        $connection = $this->getDatabaseConnection();
        $parameters = $this->getDatabaseParameters($connection);
        $config = $this->getDatabaseConfig($connection, $keyname, $parameters);

        /* TODO: Do the same to the storage */

        $storage = $this->getStorageConfig();
    }

    protected function getDatabaseConnection()
    {
        $available = $this->getConnectionsAvailable();

        if (!is_array($available) || count($available) < 1) {
            throw new \Exception('No configured connections available!');
            exit();
        }

        $default = Config::get('database.default');
        $default = array_search($default, $available);

        $connection = $this->choice('What is the tenant connection ?',
                                    $available,
                                    $default);

        return $connection;
    }

    protected function getConnectionsAvailable()
    {
        $connections = array_keys(Config::get('database.connections'));

        $tenant = array_search('tenant', $connections);

        if (is_integer($tenant)) {
            array_splice($connections, $tenant, 1);
        }

        return $connections;
    }

    protected function getDatabaseParameters($connection)
    {
        $driver = Config::get("database.connections.{$connection}.driver");

        $creator = DatabaseCreatorFactory::getCreator($driver);

        return $creator->getParameters();
    }

    protected function getDatabaseConfig($connection, $keyname, array $parameters)
    {
        $config = Config::get("database.connections.{$connection}");

        foreach ($parameters as $key => $data) {
            $_message = (!isset($data['message'])) ? "Inform '{$key}'" : $data['message'];
            $_type = (!isset($data['type'])) ? 'ask' : $data['type'];
            $_default = false;

            if (isset($data['default'])) {
                $_type = 'anticipate';
                $_default = str_replace([':keyname'],[$keyname], $data['default']);
            }

            if (!$_default) {
                $config[$key] = $this->$_type($_message);
                continue;
            }

            $config[$key] = $this->$_type($_message, [$_default]);
        }

        return $config;
    }

    protected function getStorageConfig()
    {
        return [];
    }
}

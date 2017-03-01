<?php

namespace Protosofia\Ben10ant\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\DatabaseCreatorFactory;
use Protosofia\Ben10ant\StorageConfigFactory;
use Protosofia\Ben10ant\Models\TenantModel;
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
     * The console command description.
     *
     * @var string
     */
    protected $dbCreator;

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

        /* TODO: Extract database creation methods to a database wizard class */

        $dbConn = $this->getDatabaseConnection();
        $dbParams = $this->getDatabaseParameters($dbConn);
        $dbConfig = $this->getDatabaseConfig($dbConn, $keyname, $dbParams);

        /* TODO: Do the same to the storage */

        $storageConn = $this->getStorageConnection();
        $storageParams = $this->getStorageParameters($storageConn);
        $storageConfig = $this->getStorageConfig($storageConn, $keyname, $storageParams);

        $this->createDatabase($dbConn, $dbConfig);

        $uuid = Uuid::generate(4);

        $tenant = new TenantModel();
        $tenant->uuid = $uuid;
        $tenant->name = $name;
        $tenant->keyname = $keyname;
        $tenant->database = json_encode($dbConfig);
        $tenant->storage = json_encode($storageConfig);
        $tenant->save();

        $this->info("Tenant '{$name}' created successfully.");
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

        $tenant = array_search(env('TENANT_DB_CONNECTION','tenant'), $connections);

        if (is_integer($tenant)) {
            array_splice($connections, $tenant, 1);
        }

        return $connections;
    }

    protected function getDatabaseParameters($connection)
    {
        $driver = Config::get("database.connections.{$connection}.driver");

        $this->dbCreator = DatabaseCreatorFactory::getCreator($driver);

        return $this->dbCreator->getParameters();
    }

    protected function getDatabaseConfig($connection, $keyname, array $parameters)
    {
        $config = Config::get("database.connections.{$connection}");

        foreach ($parameters as $key => $data) {
            $this->askParameters($config, $keyname, $key, $data);
        }

        return $config;
    }

    protected function getStorageConnection()
    {
        $available = $this->getStorageConnectionsAvailable();

        if (!is_array($available) || count($available) < 1) {
            throw new \Exception('No configured storage connections available!');
            exit();
        }

        $default = Config::get('filesystems.default');
        $default = array_search($default, $available);

        $connection = $this->choice('What is the tenant storage connection ?',
                                    $available,
                                    $default);

        return $connection;
    }

    protected function getStorageConnectionsAvailable()
    {
        $connections = array_keys(Config::get('filesystems.disks'));

        $tenant = array_search(env('TENANT_STORAGE_CONNECTION','tenant'), $connections);

        if (is_integer($tenant)) {
            array_splice($connections, $tenant, 1);
        }

        return $connections;
    }

    protected function getStorageParameters($connection)
    {
        $driver = Config::get("filesystems.disks.{$connection}.driver");

        $creator = StorageConfigFactory::getConfig($driver);

        return $creator->getParameters();
    }

    protected function getStorageConfig($connection, $keyname, array $parameters)
    {
        $config = Config::get("filesystems.disks.{$connection}");

        foreach ($parameters as $key => $data) {
            $this->askParameters($config, $keyname, $key, $data);
        }

        return $config;
    }

    protected function askParameters(&$config, $keyname, $key, array $data)
    {
        $_message = (!isset($data['message'])) ? false : $data['message'];
        $_type = (!isset($data['type'])) ? 'ask' : $data['type'];
        $_default = false;
        $_helpers = (!isset($data['helpers'])) ? false : explode('|', $data['helpers']);
        $_choices = (!isset($data['choices'])) ? false : $data['choices'];

        if (isset($data['default'])) {
            $_type = 'anticipate';
            $_default = str_replace([':keyname'],[$keyname], $data['default']);
        }

        if (is_array($_choices)) {
            $_type = 'choice';
            if (!$_default) $_default = reset($_choices);
        }

        if (!$_message) {
            $_message = (!$_default) ? "Inform '{$key}'"
                                     : "Inform '{$key}' (default: {$_default})";
        }

        switch ($_type) {
            case 'choice':
                $config[$key] = $this->$_type($_message, $_choices, $_default);
                break;
            case 'anticipate':
                $config[$key] = $this->$_type($_message, [$_default]);
                break;
            default:
                $config[$key] = $this->$_type($_message);
        }

        if (is_array($_helpers)) $this->applyHelpers($config[$key], $_helpers);
    }

    protected function applyHelpers(&$value, $helpers)
    {
        $tmp = $value;

        foreach ($helpers as $method) {
            $tmp = $method($tmp);
        }

        $value = $tmp;
    }

    protected function createDatabase($conn, $config)
    {
        if (!$this->confirm('Create the database now ?')) {
            return false;
        }

        $params = $config;
        $params['connection'] = $conn;

        return $this->dbCreator->createDatabase($params);
    }
}

<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\Contracts\WizardServiceAbstract;

class DatabaseWizardService extends WizardServiceAbstract
{
    protected function setConnection()
    {
        $available = $this->getConnectionsAvailable();

        if (!is_array($available) || count($available) < 1) {
            throw new \Exception('No configured connections available!');
            exit();
        }

        $default = Config::get('database.default');
        $default = array_search($default, $available);

        $connection = $this->command->choice('What is the tenant connection ?',
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

    protected function setParameters()
    {
        $connection = $this->connection;

        $driver = Config::get("database.connections.{$connection}.driver");

        $this->handler = DatabaseCreatorFactory::getCreator($driver);

        return $this->handler->getParameters();
    }

    protected function setConfig()
    {
        $connection = $this->connection;
        $keyname = $this->keyname;
        $parameters = $this->params;

        $config = Config::get("database.connections.{$connection}");

        foreach ($parameters as $key => $data) {
            $this->askParameters($config, $keyname, $key, $data);
        }

        return $config;
    }
}

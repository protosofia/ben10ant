<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\Contracts\WizardServiceAbstract;

class StorageWizardService extends WizardServiceAbstract
{
    protected function setConnection()
    {
        $available = $this->getConnectionsAvailable();

        if (!is_array($available) || count($available) < 1) {
            throw new \Exception('No configured storage connections available!');
            exit();
        }

        $default = Config::get('filesystems.default');
        $default = array_search($default, $available);

        $connection = $this->command->choice('What is the tenant storage connection ?',
                                             $available,
                                             $default);

        return $connection;
    }

    protected function getConnectionsAvailable()
    {
        $connections = array_keys(Config::get('filesystems.disks'));

        $tenant = array_search(env('TENANT_STORAGE_CONNECTION','tenant'), $connections);

        if (is_integer($tenant)) {
            array_splice($connections, $tenant, 1);
        }

        return $connections;
    }

    protected function setParameters()
    {
        $connection = $this->connection;

        $driver = Config::get("filesystems.disks.{$connection}.driver");

        $this->handler = StorageConfigFactory::getConfig($driver);

        return $this->handler->getParameters();
    }

    protected function setConfig()
    {
        $connection = $this->connection;
        $keyname = $this->keyname;
        $parameters = $this->params;

        $config = Config::get("filesystems.disks.{$connection}");

        foreach ($parameters as $key => $data) {
            $this->askParameters($config, $keyname, $key, $data);
        }

        return $config;
    }
}

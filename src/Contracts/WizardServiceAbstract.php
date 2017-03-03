<?php

namespace Protosofia\Ben10ant\Contracts;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

abstract class WizardServiceAbstract
{
    protected $keyname;
    protected $connection;
    protected $params;
    protected $config;
    protected $handler;
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function run($keyname)
    {
        $this->keyname = $keyname;
        $this->connection = $this->setConnection();
        $this->params = $this->setParameters();
        $this->config = $this->setConfig();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    protected function setConnection()
    {
        throw new \Exception('Must implement!');
    }

    protected function getConnectionsAvailable()
    {
        throw new \Exception('Must implement!');
    }

    protected function setParameters()
    {
        throw new \Exception('Must implement!');
    }

    protected function setConfig()
    {
        throw new \Exception('Must implement!');
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
                $config[$key] = $this->command->$_type($_message, $_choices, $_default);
                break;
            case 'anticipate':
                $config[$key] = $this->command->$_type($_message, [$_default]);
                break;
            default:
                $config[$key] = $this->command->$_type($_message);
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
}

<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;

class StorageConfigFactory
{
    public static function getConfig($driver)
    {
        $creators = Config::get('tenant.storage.drivers');

        if (!isset($creators[$driver])) {
            throw new \Exception("No storage config registered for '{$driver}' driver.");
            exit();
        }

        return new $creators[$driver]();
    }
}

<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;

class DatabaseCreatorFactory
{
    public static function getCreator($driver)
    {
        $creators = Config::get('tenant.database.drivers');

        if (!isset($creators[$driver])) {
            throw new \Exception("No database creator registered for '{$driver}' driver.");
            exit();
        }

        return new $creators[$driver]();
    }
}

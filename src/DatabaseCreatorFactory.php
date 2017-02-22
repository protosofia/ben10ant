<?php

namespace Protosofia\Ben10ant;

class DatabaseCreatorFactory
{
    protected static $creators = [
        'sqlite' => \Protosofia\Ben10ant\SQLiteDatabaseCreator::class,
        'mysql' => \Protosofia\Ben10ant\MySQLDatabaseCreator::class,
    ];

    public static function getCreator($driver)
    {
        if (!isset(self::$creators[$driver])) {
            throw new \Exception("No database creator registered for '{$driver}' driver.");
            exit();
        }

        return new self::$creators[$driver]();
    }
}

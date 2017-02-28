<?php

namespace Protosofia\Ben10ant;

class StorageConfigFactory
{
    protected static $creators = [
        'local' => \Protosofia\Ben10ant\LocalStorageConfig::class,
        'public' => \Protosofia\Ben10ant\PublicStorageConfig::class,
        's3' => \Protosofia\Ben10ant\S3StorageConfig::class,
    ];

    public static function getConfig($driver)
    {
        if (!isset(self::$creators[$driver])) {
            throw new \Exception("No storage config registered for '{$driver}' driver.");
            exit();
        }

        return new self::$creators[$driver]();
    }
}

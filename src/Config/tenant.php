<?php

return [

    'database' => [

        'drivers' => [
            'sqlite' => \Protosofia\Ben10ant\SQLiteDatabaseCreator::class,
            'mysql' => \Protosofia\Ben10ant\MySQLDatabaseCreator::class,
        ]

    ],

    'storage' => [

        'drivers' => [
            'local' => \Protosofia\Ben10ant\LocalStorageConfig::class,
            'public' => \Protosofia\Ben10ant\PublicStorageConfig::class,
            's3' => \Protosofia\Ben10ant\S3StorageConfig::class,
        ]

    ]

];

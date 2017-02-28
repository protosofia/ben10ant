<?php

namespace Protosofia\Ben10ant;

use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\StorageConfigInterface;

class PublicStorageConfig implements StorageConfigInterface
{
    public function getParameters()
    {
        return [
            'root' => [
                'default' => 'app/tenants/:keyname',
                'helpers' => 'storage_path'
            ],
            'url' => [
                'default' => 'storage',
                'helpers' => 'url'
            ],
            'visibility' => [
                'default' => FilesystemContract::VISIBILITY_PUBLIC,
                'choice' => [FilesystemContract::VISIBILITY_PUBLIC,
                             FilesystemContract::VISIBILITY_PRIVATE]
            ]
        ];
    }
}

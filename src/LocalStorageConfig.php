<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\StorageConfigInterface;

class LocalStorageConfig implements StorageConfigInterface
{
    public function getParameters()
    {
        return [
            'root' => [
                'default' => 'app/tenants/:keyname',
                'helpers' => 'storage_path'
            ]
        ];
    }
}

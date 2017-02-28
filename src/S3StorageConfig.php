<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\StorageConfigInterface;

class S3StorageConfig implements StorageConfigInterface
{
    public function getParameters()
    {
        return [
            'key' => [
                'default' => env('AWS_KEY', false),
            ],
            'secret' => [
                'default' => env('AWS_SECRET', false),
            ],
            'region' => [
                'default' => env('AWS_REGION', false),
            ],
            'bucket' => [
                'default' => env('AWS_BUCKET', false),
            ],
            'root' => [
                'default' => ':keyname',
            ]
        ];
    }
}

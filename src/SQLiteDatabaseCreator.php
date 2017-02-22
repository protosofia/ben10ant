<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\DatabaseCreatorInterface;

class SQLiteDatabaseCreator implements DatabaseCreatorInterface
{
    public function createDatabase(array $params)
    {
        $validator = Validator::make($params, [
            'database' => 'required|string',
            'mode' => 'sometimes|required|regex:/^0[0-7]{3}$/'
        ]);

        if ($validator->fails()) {
            throw new \Exception("Required data is missing.\n"
                                 .implode("\n", $validator->errors()->all()));
            exit();
        }

        $path = database_path("tenants/{$params['database']}");
        $mode = (isset($params['mode'])) ? $params['mode'] : '0666';

        try {
            $db = new \PDO("sqlite:{$path}");
        } catch (\PDOException $e) {
            throw new \Exception("Database could not be created.\n
                                {$e->getMessage()}");
            exit();
        }

        return true;
    }

    public function getParameters()
    {
        return [
            'database' => [
                'default' => ':keyname'
            ],
            'mode' => [
                'noConfig' => true,
                'default' => '0666'
            ]
        ];
    }
}

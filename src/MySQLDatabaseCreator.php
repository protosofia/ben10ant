<?php

namespace Protosofia\Ben10ant;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\DatabaseCreatorInterface;

class MySQLDatabaseCreator implements DatabaseCreatorInterface
{
    public function createDatabase(array $params)
    {
        $validator = Validator::make($params, [
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'connection' => 'sometimes|required|string',
            'host' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception("Required data is missing.\n"
                                 .implode("\n", $validator->errors()->all()));
            return false;
        }

        if (!isset($params['connection'])) {
            $params['connection'] = Config::get('database.default');
        }

        if (!Config::get("database.connections.{$params['connection']}")) {
            throw new \Exception("Connection specified is not defined.");
            return false;
        }

        $db = $params['database'];
        $username = $params['username'];
        $pass = $params['password'];
        $conn = $params['connection'];
        $host = $params['host'] ?? 'localhost';

        DB::beginTransaction();

        try {
            DB::connection($conn)->statement("CREATE DATABASE IF NOT EXISTS `{$db}`;");
            DB::connection($conn)->statement("GRANT ALL PRIVILEGES ON `{$db}`.* TO '{$username}'@'{$host}' IDENTIFIED BY '{$pass}';");
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            throw new \Exception("Database could not be created.\n
                                {$e->getMessage()}");
            return false;
        }

        return true;
    }

    public function getParameters()
    {
        return [
            'database' => [
                'default' => ':keyname'
            ],
            'username' => [],
            'password' => [
                'type' => 'secret',
            ]
        ];
    }
}

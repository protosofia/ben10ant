<?php

namespace Protosofia\Ben10ant\Contracts;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Protosofia\Ben10ant\Contracts\TenantModelInterface;
use Webpatser\Uuid\Uuid;

abstract class TenantServiceAbstract {

    protected $tenant;

    public function __construct(TenantModelInterface $tenant)
    {
        $this->tenant = $tenant;
    }

    public function setTenantByID($id)
    {
        $tenant = $this->tenant->find($id);

        if (!$tenant) {
            throw new Exception('Tenant not found.');
            return false;
        }

        return $this->setTenant($tenant);
    }

    public function setTenantByUUID($uuid)
    {
        $tenant = $this->tenant->where('uuid', $uuid)->first();

        if (!$tenant) {
            throw new Exception('Tenant not found.');
            return false;
        }

        return $this->setTenant($tenant);
    }

    public function setTenantByKey($key)
    {
        $tenant = $this->tenant->where('keyname', $key)->first();

        if (!$tenant) {
            throw new Exception('Tenant not found.');
            return false;
        }

        return $this->setTenant($tenant);
    }

    public function setTenant(TenantModelInterface $tenant)
    {
        $dbConn = env('TENANT_DB_CONNECTION', 'tenant');
        $storageConn = env('TENANT_STORAGE_CONNECTION', 'tenant');

        $dbConfig = json_decode($tenant->database, true);
        $storageConfig = json_decode($tenant->storage, true);

        $this->setDatabaseConnection($dbConn, $dbConfig);
        $this->setStorageConnection($storageConn, $storageConfig);

        return $tenant;
    }

    protected function setDatabaseConnection($conn, $config)
    {
        Config::set("database.connections.{$conn}", $config);
        //If you want to use query builder without having to specify the connection
        // Config::set('database.default', 'tenant');
        DB::purge($conn);
        DB::reconnect($conn);
        return DB::connection($conn);
    }

    protected function setStorageConnection($conn, $config)
    {
        Config::set("filesystems.disks.{$conn}", $config);
        return Storage::disk($conn);
    }

    public function new(array $params)
    {
        $validator = Validator::make($params, [
            'name' => 'required',
            'keyname' => 'required',
            'database' => 'required|json',
            'storage' => 'required|json',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Name, Keyname, Storage (json) and Database (json) are required fields for Tenant creation.');
            return false;
        }

        $params['uuid'] = Uuid::generate(4)->string;

        return $this->tenant->create($params);
    }

    public function hello ()
    {
        return 'hello';
    }

}

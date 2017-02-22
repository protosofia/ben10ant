<?php

namespace Protosofia\Ben10ant\Contracts;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Protosofia\Ben10ant\Contracts\TenantModelInterface;

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
        $tenantCon = env('TENANT_CONNECTION', 'tenant');
        $config = json_decode($tenant->database, true);

        Config::set("database.connections.{$tenantCon}", $config);

        //If you want to use query builder without having to specify the connection
        // Config::set('database.default', 'tenant');

        DB::purge($tenantCon);
        DB::reconnect($tenantCon);

        return DB::connection($tenantCon);
    }

    public function hello ()
    {
        return 'hello';
    }

}

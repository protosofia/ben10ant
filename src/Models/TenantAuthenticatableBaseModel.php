<?php

namespace Protosofia\Ben10ant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantAuthenticatableBaseModel extends Authenticatable
{
    protected $connection;

    public function __construct(array $attributes = [])
    {
        $this->connection = env('TENANT_DB_CONNECTION', 'tenant');
        parent::__construct($attributes);
    }
}

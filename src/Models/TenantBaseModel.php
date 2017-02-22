<?php

namespace Protosofia\Ben10ant\Models;

use Illuminate\Database\Eloquent\Model;

class TenantBaseModel extends Model
{
    protected $connection = env('TENANT_CONNECTION', 'tenant');
}

<?php

namespace Protosofia\Ben10ant\Models;

use Illuminate\Database\Eloquent\Model;
use Protosofia\Ben10ant\Contracts\TenantModelInterface;

class TenantModel extends Model implements TenantModelInterface
{
    protected $table = 'tenants';
    protected $fillable = ['uuid', 'name', 'keyname', 'database'];

    public function __construct()
    {
        $this->connection = config('database.default');
        parent::__construct();
    }
}

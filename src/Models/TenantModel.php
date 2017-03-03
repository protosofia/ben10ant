<?php

namespace Protosofia\Ben10ant\Models;

use Illuminate\Database\Eloquent\Model;
use Protosofia\Ben10ant\Contracts\TenantModelInterface;

class TenantModel extends Model implements TenantModelInterface
{
    protected $table = 'tenants';
    protected $guarded = [];
    protected $fillable = ['uuid', 'name', 'keyname', 'database', 'storage'];

    public function __construct(array $attributes = [])
    {
        $this->connection = config('database.default');
        parent::__construct($attributes);
    }
}

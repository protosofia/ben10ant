<?php

namespace Protosofia\Ben10ant\Facades;

use Illuminate\Support\Facades\Facade;

class TenantFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'Protosofia\Ben10ant\TenantServiceInterface';
    }
}

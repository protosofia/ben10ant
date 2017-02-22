<?php

namespace Protosofia\Ben10ant\Providers;

use Illuminate\Support\ServiceProvider;
use Protosofia\Ben10ant\Tenant;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $root = dirname(__DIR__);

        $this->loadMigrationsFrom("{$root}/Migrations");

        $this->publishes([
            "{$root}/Database/ben10ant.sqlite" => database_path('tenants/ben10ant.sqlite'),
            "{$root}/Database/4n0th3r.sqlite" => database_path('tenants/4n0th3r.sqlite'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Protosofia\Ben10ant\TenantServiceInterface',
                              'Protosofia\Ben10ant\DefaultTenantService');

        $this->app->bind('Protosofia\Ben10ant\Contracts\TenantModelInterface',
                         'Protosofia\Ben10ant\Models\TenantModel');

        $this->mergeConfigFrom(__DIR__.'/../Config/database.php',
                               'database.connections');
    }
}

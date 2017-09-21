# ben10ant
A really usefull Multi-Tenancy package for Laravel, it's serious, at least i'm trying to...

## Installation

    composer require protosofia/ben10ant

## Configuration

### Console Commands Configuration
Add to app/Console/Kernel.php:

    ...
    protected $commands = [
        ...
        \Protosofia\Ben10ant\Commands\TenantCreate::class,
        \Protosofia\Ben10ant\Commands\TenantMigrate::class,
        \Protosofia\Ben10ant\Commands\TenantMigrateRefresh::class,
        \Protosofia\Ben10ant\Commands\TenantMigrateRollback::class,
        \Protosofia\Ben10ant\Commands\TenantSeed::class,
        ...
    ];
    ...

### Middleware Configuration
Add to app/Http/Kernel.php:

    ...
    protected $middleware = [
        ...
        \Protosofia\Ben10ant\Middlewares\CheckTenantByKey::class,
        ...
    ];
    ...

### Provider Configuration
Add to config/app.php:

    ...
    'providers' => [
        ...
        \Protosofia\Ben10ant\Providers\TenantServiceProvider::class,
        ...
    ],
    ...

### Facade
Add to config/app.php:

    ...
    'aliases' => [
        ...
        'Tenant' => Protosofia\Ben10ant\Facades\TenantFacade::class,
        ...
    ],
    ...

### Models
This package have 3 types of models:

* **Protosofia\Ben10ant\Models\TenantModel**: This model will always target the *main database*, where is the "tenants" table;
* **Protosofia\Ben10ant\Models\TenantBaseModel**: This model will always target the *current tenant database*, in other words, the current logged user tenant;
* **Protosofia\Ben10ant\Models\TenantAuthenticatableBaseModel**: This model is exactly as *TenantBaseModel*, but implements *Illuminate\Foundation\Auth\User*;

To manage the "tenants" table, you can create a model as below:

    ...
    use Protosofia\Ben10ant\Models\TenantModel;

    class Tenant extends TenantModel
    {
        //
    }

To have a tenant user (authenticatable), you can create a model as below:

    ...
    use Protosofia\Ben10ant\Models\TenantAuthenticatableBaseModel;

    class User extends TenantAuthenticatableBaseModel
    {
        use Notifiable;
        ...
    }

To have models to handle tenant data (no authenticatable), you can create a model as below:

    ...
    use Protosofia\Ben10ant\Models\TenantBaseModel;

    class YourTenantTable extends TenantBaseModel
    {
        protected $table = 'your_tenant_table'; //optional
        ...
    }

## Usage

### Config

    php artisan vendor:publish

This will publish the config file.

### Console Commands

#### Create a New Tenant

    tenant:create {name} {keyname}

* name      Name of the Tenant. E.g. 'Tenant Alpha';
* keyname   Tenant nickname for auth (like a subdomain). E.g. 'tenant-alpha';

This command is a tenant creator wizard, you can configure the database connection and storage.

#### Run Migrations on Tenant Database

    tenant:migrate {tenant} {--force} {--path} {--pretend} {--step}

* tenant        Tenant keyname. E.g. 'tenant-alpha';
* --force       Force the operation to run when in production;
* --path        The path of migrations files to be executed.
* --pretend     Dump the SQL queries that would be run.
* --step        Force the migrations to be run so they can be rolled back individually.

This command is an indirect call to 'migrate' command, it set tenant conenction automatically. If the path option is not defined it assumes default path: database/migrations/tenant.

#### Refresh and Run Migrations on Tenant Database

    tenant:migrate:refresh {tenant} {--force} {--path} {--seed} {--seeder} {--step}

* tenant        Tenant keyname. E.g. 'tenant-alpha';
* --force       Force the operation to run when in production;
* --path        The path of migrations files to be executed.
* --seed        Indicates if the seed task should be re-run.
* --seeder      The class name of the root seeder.
* --step        The number of migrations to be reverted & re-run.

This command is an indirect call to 'migrate:refresh' command, it set tenant conenction automatically. If the path option is not defined it assumes default path: database/migrations/tenant.

#### Rollback a Migrations on Tenant Database

    tenant:migrate:rollback {tenant} {--force} {--path} {--seed} {--seeder} {--step}

* tenant        Tenant keyname. E.g. 'tenant-alpha';
* --force       Force the operation to run when in production;
* --path        The path of migrations files to be executed.
* --pretend     Dump the SQL queries that would be run.
* --step        The number of migrations to be reverted.

This command is an indirect call to 'migrate:rollback' command, it set tenant conenction automatically. If the path option is not defined it assumes default path: database/migrations/tenant.

#### Seed a Tenant Database

    tenant:db:seed {tenant} {--force} {--class}

* tenant        Tenant keyname. E.g. 'tenant-alpha';
* --force       Force the operation to run when in production;
* --class       The class name of the root seeder [default: "DatabaseSeeder"]

This command is an indirect call to 'db:seed' command, it set tenant conenction automatically. If the class option is not defined it assumes default class: DatabaseSeeder.

### Middleware

There are 3 middlewares available on the package:

* Protosofia\Ben10ant\Middlewares\CheckTenantByID: Get value from header TENANT and try to match the tenant pk database record;
* Protosofia\Ben10ant\Middlewares\CheckTenantByKey: Get value from header TENANT and try to match the tenant keyname database record;
* Protosofia\Ben10ant\Middlewares\CheckTenantByUUID: Get value from header TENANT and try to match the tenant uuid database record;

### Singleton Service / Facade

The service is a singleton, accessible from a Facade Tenant. E.g:

    ...
    use Tenant;

    $tenant = Tenant::setTenantByUUID($uuid);

    if (!$tenant) {
        return response()->json(['error' => 'No tenant found.'], 404);
    }
    ...

The service has 4 methods to set tenant:

* setTenantByID($id) - Set tenant by pk;
* setTenantByUUID($uuid)  - Set tenant by uuid;
* setTenantByKey($key) - Set tenant by keyname;
* setTenant(TenantModelInterface $tenant) - Set tenant with a Tenant model instance;

## TODO

* <s>Console command to create tenant</s> - Done
  * <s>Database config</s> - Done
  * <s>Storage config</s> - Done
* <s>Console command to migrate tenant</s> - Done
  * <s>Migrate</s> - Done
  * <s>Refresh and Migrate</s> - Done
* <s>Console command to seed a tenant</s> - Done
* <s>Middleware for auth</s> - Done
* <s>Service Provider for Singleton Tenant Service and Facade</s> - Done
* <s>Database migrations (Main database, where all tenants are stored)</s> - Done
* <s>Tenant model</s> - Done

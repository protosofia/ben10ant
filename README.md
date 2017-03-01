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

## Usage

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

* <s>Console command to create tenant</s>
  * <s>Database config</s>
  * <s>Storage config</s>
* <s>Console command to migrate tenant</s>
  * <s>Migrate</s>
  * <s>Refresh and Migrate</s>
* <s>Console command to seed a tenant</s>
* <s>Middleware for auth</s>
* <s>Service Provider for Singleton Tenant Service and Facade</s>
* <s>Database migrations (Main database, where all tenants are stored)</s>
* <s>Tenant model</s>

## NOTE
Package is still experimental, understand as an alpha.

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

    tenant:create {name : Tenant name} {keyname : Tenant keyname, for auth}

* name: Name of the Tenant. E.g. 'Tenant Alpha';
* keyname: Tenant nickname (like a subdomain). E.g. 'tenant-alpha';

This command is a tenant creator wizard, you can configure the database connection and storage.

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

* Console command to create tenant
  * <s>Database config</s>
  * Storage config
* <s>Middleware for auth</s>
* <s>Service Provider for Singleton Tenant Service and Facade</s>
* <s>Database migrations (Main database, where all tenants are stored)</s>
* <s>Tenant model</s>

## NOTE
Package is still experimental, understand as an alpha.

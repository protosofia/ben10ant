<?php

namespace Protosofia\Ben10ant\Middlewares;

use Closure;
use Tenant;

class CheckTenantByKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tenantKey = $request->header('TENANT', false);

        if (!$tenantKey) {
            return response()->json(['error' => 'No tenant header sent.'], 400);
        }

        $tenant = Tenant::setTenantByKey($tenantKey);

        if (!$tenant) {
            return response()->json(['error' => 'No tenant found.'], 404);
        }

        return $next($request);
    }
}

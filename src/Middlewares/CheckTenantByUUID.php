<?php

namespace Protosofia\Ben10ant\Middlewares;

use Closure;
use Tenant;

class CheckTenantByUUID
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
        $tenantUUID = $request->header('TENANT', false);

        if (!$tenantUUID) {
            return response()->json(['error' => 'No tenant header sent.'], 400);
        }

        $tenant = Tenant::setTenantByUUID($tenantUUID);

        if (!$tenant) {
            return response()->json(['error' => 'No tenant found.'], 404);
        }

        return $next($request);
    }
}

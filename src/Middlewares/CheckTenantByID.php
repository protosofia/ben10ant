<?php

namespace Protosofia\Ben10ant\Middlewares;

use Closure;
use Tenant;

class CheckTenantByID
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
        $tenantID = $request->header('TENANT', false);

        if (!$tenantID) {
            return response()->json(['error' => 'No tenant header sent.'], 400);
        }

        $tenant = Tenant::setTenantByID($tenantID);

        if (!$tenant) {
            return response()->json(['error' => 'No tenant found.'], 404);
        }

        return $next($request);
    }
}

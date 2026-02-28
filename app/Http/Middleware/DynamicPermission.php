<?php

namespace App\Http\Middleware;

use App\Models\RoutePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DynamicPermission
{
    /**
     * Check the route_permissions table to determine if the authenticated user
     * has the required permission for the current route.
     *
     * - If no mapping exists for the route, access is granted (open to any staff).
     * - If a mapping exists, the user must have that permission.
     * - The route_permissions table is cached for 60 seconds to avoid N+1 queries.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return $next($request);
        }

        // Load all route→permission mappings from cache (refreshed when seeder runs)
        $map = Cache::remember('route_permissions_map', 60, function () {
            return RoutePermission::pluck('permission_identifier', 'route_name')->all();
        });

        if (!isset($map[$routeName])) {
            // No restriction defined — allow any authenticated staff
            return $next($request);
        }

        $user = $request->user();
        if (!$user || !$user->hasPermission($map[$routeName])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

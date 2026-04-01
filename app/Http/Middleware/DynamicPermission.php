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

        // If route has no name, we can't look up a permission mapping.
        // For our architecture, all staff/portal routes MUST be named.
        if (! $routeName) {
            return $next($request);
        }

        // Load all route→permission mappings from cache
        $map = Cache::rememberForever('route_permissions_map', function () {
            return RoutePermission::pluck('permission_identifier', 'route_name')->all();
        });

        // If no restriction is defined in the database, we treat it as "Staff Only"
        // provided they are authenticated (the 'auth' middleware handles that).
        if (! isset($map[$routeName])) {
            return $next($request);
        }

        $user = $request->user();
        $permission = $map[$routeName];

        // Explicit "public" or "open" bypass can be handled if needed,
        // but for now, we check the user's permissions.
        if (! $user || ! $user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }

            return redirect()->back()->with('error', "You do not have the required permission ($permission) to access this page.");
        }

        return $next($request);
    }
}

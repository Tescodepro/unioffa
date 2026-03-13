<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\RoutePermission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SyncRoutePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan all named staff routes and register them as granular permissions';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Scanning routes for staff-side permissions...');

        $routes = Route::getRoutes();
        $count = 0;

        // Staff-side prefixes or patterns
        $staffPrefixes = [
            'admin', 'ict', 'burser', 'bursary', 'registrar', 'vc',
            'dean', 'hod', 'lecturer', 'staff', 'admitted-students',
        ];

        foreach ($routes as $route) {
            $name = $route->getName();

            // Only process named routes that have a staff prefix or are protected by dynamic.permission
            if (! $name) {
                continue;
            }

            $isStaffRoute = false;

            // Check by prefix
            foreach ($staffPrefixes as $prefix) {
                if (str_starts_with($name, $prefix) || str_contains($route->uri(), 'staff/')) {
                    $isStaffRoute = true;
                    break;
                }
            }

            if ($isStaffRoute) {
                $permissionIdentifier = "route.{$name}";
                $permissionName = 'Access Route: '.str_replace('.', ' ➔ ', $name);

                // 1. Ensure Permission exists
                Permission::firstOrCreate(
                    ['identifier' => $permissionIdentifier],
                    [
                        'id' => (string) Str::uuid(),
                        'name' => $permissionName,
                    ]
                );

                // 2. Ensure RoutePermission mapping exists
                // Note: We only auto-map if no mapping exists yet, OR if it's already a route.* mapping
                $currentMapping = RoutePermission::where('route_name', $name)->first();

                if (! $currentMapping || str_starts_with($currentMapping->permission_identifier, 'route.')) {
                    RoutePermission::updateOrCreate(
                        ['route_name' => $name],
                        ['permission_identifier' => $permissionIdentifier]
                    );
                }

                $count++;
            }
        }

        Cache::forget('route_permissions_map');
        $this->info("Successfully synced {$count} staff-side routes to permissions.");
    }
}

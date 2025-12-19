<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;

if (! function_exists('activeSession')) {
    function activeSession()
    {
        return AcademicSession::where('status', '1')->first();
    }
}

if (! function_exists('activeSemester')) {
    function activeSemester()
    {
        return AcademicSemester::where('status', '1')->first();
    }
}

/**
 * Check if current route matches any of the given routes
 * @param string|array $routes
 * @return bool
 */
if (! function_exists('isRouteActive')) {
    function isRouteActive($routes)
    {
        if (is_string($routes)) {
            $routes = [$routes];
        }
        
        return request()->routeIs(...$routes);
    }
}

/**
 * Check if current URL path matches any of the given paths
 * @param string|array $paths
 * @return bool
 */
if (! function_exists('isPathActive')) {
    function isPathActive($paths)
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }
        
        foreach ($paths as $path) {
            if (request()->is($path)) {
                return true;
            }
        }
        
        return false;
    }
}

/**
 * Get active CSS class for sidebar items
 * @param string|array $routes Route names to check
 * @param string $activeClass CSS class to return if active
 * @return string
 */
if (! function_exists('activeClass')) {
    function activeClass($routes, $activeClass = 'active')
    {
        return isRouteActive($routes) ? $activeClass : '';
    }
}

/**
 * Get open CSS class for sidebar menu groups
 * @param string|array $paths URL paths to check
 * @param string $openClass CSS class to return if active
 * @return string
 */
if (! function_exists('openMenuClass')) {
    function openMenuClass($paths, $openClass = 'open')
    {
        return isPathActive($paths) ? $openClass : '';
    }
}
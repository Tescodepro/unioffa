<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;

if (! function_exists('activeSession')) {
    function activeSession($user = null)
    {
        $user = $user ?? auth()->user();

        // If user is authenticated
        if ($user) {
            $student = clone $user->student;
            $lecturer = clone $user->lecturer;

            $userId = $user->id;

            // Query for specific overrides:
            $query = AcademicSession::where('status', '1')->where(function ($q) use ($student, $userId) {
                // 1. By exact ID
                $q->whereJsonContains('students_ids', $userId)
                    ->orWhereJsonContains('lecturar_ids', $userId);

                // 2. By Stream and Campus
                if ($student) {
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->where('stream', $student->stream)->whereNotNull('stream');
                        // also an optional campus check could go here if both must match, but usually it's one or the other or both.
                        // For flexibility, if session has a stream match, we use it.
                        // If it has a campus match, we use it.
                        // Or you can match both if both are present on the session. Let's match if stream OR campus matches,
                        // but if a session has BOTH, we should really match BOTH. The cleanest way:
                        // "If session stream is X AND session campus_id is Y (ignoring nulls)"
                    });
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->where('campus_id', $student->campus_id)->whereNotNull('campus_id');
                    });
                }
            });

            $specificSession = $query->first();
            if ($specificSession) {
                return $specificSession;
            }
        }

        // Fallback to the globally active session (no overrides)
        return AcademicSession::where('status', '1')
            ->whereNull('stream')
            ->whereNull('campus_id')
            ->whereNull('students_ids')
            ->whereNull('lecturar_ids')
            ->first();
    }
}

if (! function_exists('activeSemester')) {
    function activeSemester($user = null)
    {
        $user = $user ?? auth()->user();

        if ($user) {
            $student = clone $user->student;
            $lecturer = clone $user->lecturer;

            $userId = $user->id;

            $query = AcademicSemester::where('status', '1')->where(function ($q) use ($student, $userId) {
                // 1. By exact ID
                $q->whereJsonContains('students_ids', $userId)
                    ->orWhereJsonContains('lecturar_ids', $userId);

                // 2. By Stream and Campus
                if ($student) {
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->where('stream', $student->stream)->whereNotNull('stream');
                    });
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->where('campus_id', $student->campus_id)->whereNotNull('campus_id');
                    });
                }
            });

            $specificSemester = $query->first();
            if ($specificSemester) {
                return $specificSemester;
            }
        }

        return AcademicSemester::where('status', '1')
            ->whereNull('stream')
            ->whereNull('campus_id')
            ->whereNull('students_ids')
            ->whereNull('lecturar_ids')
            ->first();
    }
}

/**
 * Check if current route matches any of the given routes
 *
 * @param  string|array  $routes
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
 *
 * @param  string|array  $paths
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
 *
 * @param  string|array  $routes  Route names to check
 * @param  string  $activeClass  CSS class to return if active
 * @return string
 */
if (! function_exists('activeClass')) {
    function activeClass($routes, $activeClass = 'active')
    {
        return (isRouteActive($routes) || isPathActive($routes)) ? $activeClass : '';
    }
}

/**
 * Get open CSS class for sidebar menu groups
 *
 * @param  string|array  $paths  URL paths to check
 * @param  string  $openClass  CSS class to return if active
 * @return string
 */
if (! function_exists('openMenuClass')) {
    function openMenuClass($paths, $openClass = 'open')
    {
        return isPathActive($paths) ? $openClass : '';
    }
}

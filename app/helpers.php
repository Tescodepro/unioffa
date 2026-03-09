<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;

if (! function_exists('activeSession')) {
    function activeSession($user = null)
    {
        $user = $user ?? auth()->user();

        // If user is authenticated
        if ($user) {
            $student = $user->student ? clone $user->student : null;
            $lecturer = $user->lecturer ? clone $user->lecturer : null;

            $userId = $user->id;

            // Query for specific overrides:
            $query = AcademicSession::where('status', '1')->where(function ($q) use ($student, $userId) {
                // 1. By exact ID
                $q->whereJsonContains('students_ids', $userId)
                    ->orWhereJsonContains('lecturar_ids', $userId);

                // 2. By Stream
                if ($student) {
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->whereNotNull('stream')->whereJsonContains('stream', (string) $student->stream);
                    });

                    // 3. By Campus
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->whereNotNull('campus_id')->whereJsonContains('campus_id', $student->campus_id);
                    });

                    // 4. By Programme (student_type)
                    if ($student->programme) {
                        $q->orWhere(function ($subQ) use ($student) {
                            $subQ->whereNotNull('programme')->whereJsonContains('programme', $student->programme);
                        });
                    }
                }
            });

            $specificSession = $query->first();
            if ($specificSession) {
                return $specificSession;
            }
        }

        // Fallback to the globally active session (no overrides)
        // students_ids / lecturar_ids / stream / programme may be stored as NULL or empty JSON array []
        return AcademicSession::where('status', '1')
            ->where(fn ($q) => $q->whereNull('stream')->orWhere('stream', '')->orWhere('stream', '[]'))
            ->where(fn ($q) => $q->whereNull('campus_id')->orWhere('campus_id', ''))
            ->where(fn ($q) => $q->whereNull('programme')->orWhereJsonLength('programme', 0))
            ->where(fn ($q) => $q->whereNull('students_ids')->orWhereJsonLength('students_ids', 0))
            ->where(fn ($q) => $q->whereNull('lecturar_ids')->orWhereJsonLength('lecturar_ids', 0))
            ->first();
    }
}

if (! function_exists('activeSemester')) {
    function activeSemester($user = null)
    {
        $user = $user ?? auth()->user();

        if ($user) {
            $student = $user->student ? clone $user->student : null;
            $lecturer = $user->lecturer ? clone $user->lecturer : null;

            $userId = $user->id;

            $query = AcademicSemester::where('status', '1')->where(function ($q) use ($student, $userId) {
                // 1. By exact ID
                $q->whereJsonContains('students_ids', $userId)
                    ->orWhereJsonContains('lecturar_ids', $userId);

                // 2. By Stream
                if ($student) {
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->whereNotNull('stream')->whereJsonContains('stream', (string) $student->stream);
                    });

                    // 3. By Campus
                    $q->orWhere(function ($subQ) use ($student) {
                        $subQ->whereNotNull('campus_id')->whereJsonContains('campus_id', $student->campus_id);
                    });

                    // 4. By Programme (student_type)
                    if ($student->programme) {
                        $q->orWhere(function ($subQ) use ($student) {
                            $subQ->whereNotNull('programme')->whereJsonContains('programme', $student->programme);
                        });
                    }
                }
            });

            $specificSemester = $query->first();
            if ($specificSemester) {
                return $specificSemester;
            }
        }

        // students_ids / lecturar_ids / stream / programme may be stored as NULL or empty JSON array []
        return AcademicSemester::where('status', '1')
            ->where(fn ($q) => $q->whereNull('stream')->orWhere('stream', '')->orWhere('stream', '[]'))
            ->where(fn ($q) => $q->whereNull('campus_id')->orWhere('campus_id', ''))
            ->where(fn ($q) => $q->whereNull('programme')->orWhereJsonLength('programme', 0))
            ->where(fn ($q) => $q->whereNull('students_ids')->orWhereJsonLength('students_ids', 0))
            ->where(fn ($q) => $q->whereNull('lecturar_ids')->orWhereJsonLength('lecturar_ids', 0))
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

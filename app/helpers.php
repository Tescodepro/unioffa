<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;

if (! function_exists('findBestAcademicMatch')) {
    /**
     * Finds the best matching active academic record (Session or Semester) for a user.
     * Implements "AND" logic with specificity scoring.
     */
    function findBestAcademicMatch($modelClass, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $modelClass::where('status', '1')
                ->where(fn ($q) => $q->whereNull('stream')->orWhereJsonLength('stream', 0))
                ->where(fn ($q) => $q->whereNull('campus_id')->orWhereJsonLength('campus_id', 0))
                ->where(fn ($q) => $q->whereNull('programme')->orWhereJsonLength('programme', 0))
                ->where(fn ($q) => $q->whereNull('students_ids')->orWhereJsonLength('students_ids', 0))
                ->where(fn ($q) => $q->whereNull('lecturar_ids')->orWhereJsonLength('lecturar_ids', 0))
                ->first();
        }

        $activeRecords = $modelClass::where('status', '1')->get();

        if ($activeRecords->isEmpty()) {
            return null;
        }

        $student = $user->student;
        $lecturer = $user->lecturer;
        $userId = $user->id;

        $matches = $activeRecords->filter(function ($record) use ($student, $lecturer, $userId) {
            // 1. Specific User Overrides (Must match if set)
            $hasSpecificIds = !empty($record->students_ids) || !empty($record->lecturar_ids);
            if ($hasSpecificIds) {
                $idMatch = false;
                if (!empty($record->students_ids) && in_array($userId, (array)$record->students_ids)) {
                    $idMatch = true;
                }
                if (!empty($record->lecturar_ids) && in_array($userId, (array)$record->lecturar_ids)) {
                    $idMatch = true;
                }
                if (!$idMatch) return false;
            }

            // 2. Group Overrides (AND logic)
            // If any override is set on the record, the student MUST match it.

            // Stream
            if (!empty($record->stream)) {
                if (!$student || !$student->stream || !in_array((string)$student->stream, (array)$record->stream)) {
                    return false;
                }
            }

            // Campus
            if (!empty($record->campus_id)) {
                if (!$student || !$student->campus_id || !in_array($student->campus_id, (array)$record->campus_id)) {
                    return false;
                }
            }

            // Programme (e.g., REGULAR, TOPUP)
            if (!empty($record->programme)) {
                if (!$student || !$student->programme || !in_array($student->programme, (array)$record->programme)) {
                    return false;
                }
            }

            return true;
        });

        if ($matches->isEmpty()) {
            return null;
        }

        // Score matches by specificity to pick the "best" one
        return $matches->sortByDesc(function ($record) {
            $score = 0;
            // Personal ID overrides are extremely specific
            if (!empty($record->students_ids) || !empty($record->lecturar_ids)) $score += 1000;
            
            // Attribute matches
            if (!empty($record->stream)) $score += 10;
            if (!empty($record->campus_id)) $score += 10;
            if (!empty($record->programme)) $score += 10;
            
            return $score;
        })->first();
    }
}

if (! function_exists('activeSession')) {
    function activeSession($user = null)
    {
        return findBestAcademicMatch(AcademicSession::class, $user);
    }
}

if (! function_exists('activeSemester')) {
    function activeSemester($user = null)
    {
        return findBestAcademicMatch(AcademicSemester::class, $user);
    }
}

if (! function_exists('isRouteActive')) {
    function isRouteActive($routes)
    {
        if (is_string($routes)) {
            $routes = [$routes];
        }

        return request()->routeIs(...$routes);
    }
}

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

if (! function_exists('activeClass')) {
    function activeClass($routes, $activeClass = 'active')
    {
        return (isRouteActive($routes) || isPathActive($routes)) ? $activeClass : '';
    }
}

if (! function_exists('getSemesterName')) {
    function getSemesterName($code)
    {
        $mapping = [
            '1st' => 'First Semester',
            '2nd' => 'Second Semester',
            '3nd' => 'Summer Semester',
            '3r' => 'Summer Semester',
            '3rd' => 'Summer Semester',
        ];

        return $mapping[$code] ?? $code;
    }
}

if (! function_exists('openMenuClass')) {
    function openMenuClass($paths, $openClass = 'open')
    {
        return isPathActive($paths) ? $openClass : '';
    }
}

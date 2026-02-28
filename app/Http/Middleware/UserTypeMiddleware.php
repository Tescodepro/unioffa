<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserTypeMiddleware
{
    /**
     * All staff types in the system.
     * The special keyword 'staff' in a route means "any of these types".
     */
    const STAFF_TYPES = [
        'administrator',
        'dean',
        'lecturer',
        'hod',
        'registrar',
        'vice-chancellor',
        'bursary',
        'ict',
        'center-director',
        'programme-director',
    ];

    /**
     * Handle an incoming request.
     *
     * $type can be:
     *   - A single type:              'user.type:dean'
     *   - Pipe-separated list:        'user.type:dean|hod|lecturer'
     *   - The keyword 'staff':        'user.type:staff'  → any staff type is allowed
     */
    public function handle(Request $request, Closure $next, string $type)
    {
        $allowedTypes = explode('|', $type);

        // Expand the 'staff' keyword to all known staff types
        if (in_array('staff', $allowedTypes)) {
            $allowedTypes = self::STAFF_TYPES;
        }

        // 1️⃣ Not logged in → send to correct login page
        if (!Auth::check()) {
            $firstType = $allowedTypes[0] ?? 'staff';
            $redirectRoute = match (true) {
                $firstType === 'applicant' => route('application.login'),
                $firstType === 'student' => route('student.login'),
                default => route('staff.login'),
            };

            return redirect($redirectRoute)
                ->with('error', 'You need to be logged in to access this page.');
        }

        // 2️⃣ Logged in but no user type set (corrupt account)
        $userType = Auth::user()->userType->name ?? null;
        if (!$userType) {
            Auth::logout();
            return redirect()->route('staff.login')
                ->with('error', 'Your account has no user type assigned. Please contact the administrator.');
        }

        // 3️⃣ Student can access both student and applicant routes
        if ($userType === 'student' && array_intersect(['student', 'applicant'], $allowedTypes)) {
            return $next($request);
        }

        // 4️⃣ Type check — DO NOT log out on failure, just redirect to their dashboard
        if (!in_array($userType, $allowedTypes)) {
            // Redirect to the staff login page only if they have no session-based
            // dashboard to go back to; otherwise send them back so they stay logged in.
            return redirect()->back()
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
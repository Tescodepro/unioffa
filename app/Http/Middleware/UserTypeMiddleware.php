<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, $type)
    {
        // Check if user is logged in
        if (! Auth::check()) {
            if ($type === 'applicant') {
                $redirectRoute = route('application.login');
            } elseif ($type === 'student') {
                $redirectRoute = route('student.login');
            } elseif ($type === 'administrator') {
                $redirectRoute = route('staff.login');
            }

            return redirect($redirectRoute)
                ->with('error', 'You need to be logged in to access this page.');
        }

        $userType = Auth::user()->userType->name;

        // Allow student to also access applicant routes
        if ($userType === 'student' && in_array($type, ['student', 'applicant'])) {
            return $next($request);
        }

        // Normal strict check
        if ($userType !== $type) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}

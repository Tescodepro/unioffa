<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * $category can be: 'staff', 'student', 'applicant'
     */
    public function handle(Request $request, Closure $next, string $category)
    {
        // 1️⃣ Not logged in → send to correct login page
        if (! Auth::check()) {
            $redirectRoute = match ($category) {
                'applicant' => route('application.login'),
                'student' => route('student.login'),
                default => route('staff.login'),
            };

            return redirect($redirectRoute)
                ->with('error', 'You need to be logged in to access this page.');
        }

        $user = Auth::user();
        $userType = $user->userType->name ?? null;

        if (! $userType) {
            Auth::logout();

            return redirect()->route('staff.login')
                ->with('error', 'Your account has no user type assigned.');
        }

        // 2️⃣ Verify the user belongs to the requested category
        // This logic remains flexible: Staff are anything that isn't student/applicant
        $actualCategory = match ($userType) {
            'student' => 'student',
            'applicant' => 'applicant',
            default => 'staff',
        };

        if ($actualCategory !== $category) {
            // Special case: Students can often access applicant routes
            if ($userType === 'student' && $category === 'applicant') {
                return $next($request);
            }

            return redirect()->back()
                ->with('error', 'Unauthorized area.');
        }

        return $next($request);
    }
}

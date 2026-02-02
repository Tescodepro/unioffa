<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $type)
    {
        // 1️⃣ Check if user is logged in
        if (!Auth::check()) {
            // Redirect based on user type
            switch ($type) {
                case 'applicant':
                    $redirectRoute = route('application.login');
                    break;
                case 'student':
                    $redirectRoute = route('student.login');
                    break;
                case 'administrator':
                case 'bursary':
                case 'registrar':
                case 'vice-chancellor':
                case 'ict':
                case 'dean':
                case 'hod':
                case 'lecturer':
                    $redirectRoute = route('staff.login');
                    break;
                default:
                    $redirectRoute = route('home'); // fallback
                    break;
            }

            return redirect($redirectRoute)
                ->with('error', 'You need to be logged in to access this page.');
        }


        // 2️⃣ Get the logged-in user type
        $userType = Auth::user()->userType->name ?? null;
        if (!$userType) {
            Auth::logout();
            return redirect()->route('staff.login')->with('error', 'Invalid user type.');
        }

        // 3️⃣ Allow student to also access applicant routes
        if ($userType === 'student' && in_array($type, ['student', 'applicant'])) {
            return $next($request);
        }

        // 4️⃣ Allow any staff type to access shared staff routes
        if (
            in_array($userType, ['administrator', 'dean', 'lecturer', 'hod', 'registrar', 'vice-chancellor', 'bursary', 'ict']) &&
            in_array($type, ['administrator', 'dean', 'lecturer', 'hod', 'registrar', 'vice-chancellor', 'bursary', 'ict'])
        ) {
            return $next($request);
        }
        // 5️⃣ Strict check for other cases
        // If none of the above conditions are met, deny access (abort with a message or redirect as needed)
        if ($userType !== $type) {
            Auth::logout();
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
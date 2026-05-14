<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->must_change_password) {
                // If we are already on a password change route or logout, allow it.
                if ($request->routeIs('staff.password.change', 'staff.password.update', 'staff.logout')) {
                    return $next($request);
                }

                // Otherwise, redirect to password change page
                \Log::info('MustChangePassword Redirecting', ['user' => $user->email]);

                return redirect()->route('staff.password.change')
                    ->with('warning', 'For security, you must change your password before continuing.');
            }
        }

        return $next($request);
    }
}

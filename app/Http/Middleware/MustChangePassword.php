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
        $user = Auth::user();

        // Safely check if must_change_password exists and is true
        // We use isset() here to avoid potential QueryExceptions if the column hasn't been migrated yet on the server.
        if (Auth::check() && isset($user->must_change_password) && $user->must_change_password) {
            // Allow access to the change password routes and logout
            if (! $request->routeIs('staff.password.change', 'staff.password.update', 'staff.logout')) {
                return redirect()->route('staff.password.change')
                    ->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}

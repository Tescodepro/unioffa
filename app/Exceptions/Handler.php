<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Catch expired session / CSRF token mismatch (419)
        if ($exception instanceof TokenMismatchException) {

            $currentRoute = optional($request->route())->getName();

            // Decide which login page to redirect to
            if ($currentRoute && (
                str_contains($currentRoute, 'application') ||
                str_contains($currentRoute, 'applicant') ||
                str_contains($currentRoute, 'admission')
            )) {
                $redirect = route('application.login');
            }
            elseif ($currentRoute && (
                str_contains($currentRoute, 'student') ||
                str_contains($currentRoute, 'students')
            )) {
                $redirect = route('student.login');
            }
            elseif ($currentRoute && (
                str_contains($currentRoute, 'staff') ||
                str_contains($currentRoute, 'burser') ||
                str_contains($currentRoute, 'bursary') ||
                str_contains($currentRoute, 'ict') ||
                str_contains($currentRoute, 'admin')
            )) {
                $redirect = route('staff.login');
            }
            else {
                $redirect = route('home');
            }

            return redirect($redirect)->with('error', 'Your session has expired. Please log in again.');
        }

        return parent::render($request, $exception);
    }
}

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
        if ($exception instanceof TokenMismatchException) {
            // Flush out the bad session and regenerate a clean one
            try {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } catch (Throwable $e) {
                // ignore any issue here - we just want a clean session
            }

            // Figure out which login page to send them to
            $path = $request->path();
            if (str_contains($path, 'students')) {
                $redirect = route('student.login');
            } elseif (str_contains($path, 'admission') || str_contains($path, 'application')) {
                $redirect = route('application.login');
            } elseif (str_contains($path, 'staff') || str_contains($path, 'bursary') || str_contains($path, 'ict') || str_contains($path, 'admin')) {
                $redirect = route('staff.login');
            } else {
                $redirect = route('home');
            }

            return redirect($redirect)->with('error', 'Your session expired or was invalid. Please log in again.');
        }

        return parent::render($request, $exception);
    }
}

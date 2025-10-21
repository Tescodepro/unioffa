<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // HANDLE SESSION EXPIRY (419) - REDIRECT TO LOGIN
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            
            // 👇 YOUR REAL ROUTE NAMES 👇
            if (str_contains($currentRoute ?? '', 'application') || 
                str_contains($currentRoute ?? '', 'applicant') ||
                str_contains($currentRoute ?? '', 'admission')) {
                $redirect = route('application.login');  // admission
            }
            elseif (str_contains($currentRoute ?? '', 'student') || 
                    str_contains($currentRoute ?? '', 'students')) {
                $redirect = route('student.login');      // students
            }
            elseif (str_contains($currentRoute ?? '', 'staff') || 
                    str_contains($currentRoute ?? '', 'burser') ||
                    str_contains($currentRoute ?? '', 'bursary') ||
                    str_contains($currentRoute ?? '', 'ict') ||
                    str_contains($currentRoute ?? '', 'admin')) {
                $redirect = route('staff.login');        // staff
            }
            else {
                $redirect = route('home');
            }
            
            return redirect($redirect)
                ->with('error', 'Your session has expired. Please log in again.');
        }

        return parent::render($request, $exception);
    }
}
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'user.type' => \App\Http\Middleware\UserTypeMiddleware::class,
            'dynamic.permission' => \App\Http\Middleware\DynamicPermission::class,
            'must-change-password' => \App\Http\Middleware\MustChangePassword::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => match (true) {
            $request->is('admission/*') || $request->is('admission') => route('application.login'),
            $request->is('students/*') || $request->is('students') => route('student.login'),
            default => route('staff.login'),
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

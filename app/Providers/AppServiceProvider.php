<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AcademicSession;
use App\Models\AcademicSemester;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $activeSession = AcademicSession::active();
            $activeSemester = AcademicSemester::active();

            $view->with([
                'activeSession' => $activeSession,
                'activeSemester' => $activeSemester,
            ]);
        });
    }
}

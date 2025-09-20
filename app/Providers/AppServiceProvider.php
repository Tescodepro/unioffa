<?php

namespace App\Providers;

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use Illuminate\Support\ServiceProvider;

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

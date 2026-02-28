<?php

namespace App\Providers;

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\SystemSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('administrator') ? true : null;
        });

        // Define a global gate that checks if the user has the permission
        Gate::define('permission', function ($user, $permission) {
            return $user->hasPermission($permission);
        });

        try {
            if (\Schema::hasTable('permissions')) {
                foreach (\App\Models\Permission::all() as $permission) {
                    Gate::define($permission->identifier, function ($user) use ($permission) {
                        return $user->hasPermission($permission->identifier);
                    });
                }
            }
        } catch (\Exception $e) {
            // Log or ignore if database is not ready
        }

        view()->composer('*', function ($view) {
            $activeSession = AcademicSession::active();
            $activeSemester = AcademicSemester::active();

            // Load system settings once and share with ALL views
            try {
                if (\Schema::hasTable('system_settings')) {
                    $settings = SystemSetting::all()->keyBy('key');
                    $view->with([
                        'activeSession' => $activeSession,
                        'activeSemester' => $activeSemester,
                        // Convenient individual variables for use in layouts
                        'schoolName' => $settings->get('school_name')?->value ?? 'University of Offa',
                        'schoolMotto' => $settings->get('school_motto')?->value ?? '',
                        'schoolAddress' => $settings->get('school_address')?->value ?? '',
                        'contactEmail' => $settings->get('contact_email')?->value ?? '',
                        'contactPhone' => $settings->get('contact_phone')?->value ?? '',
                        'schoolLogo' => $settings->get('logo_path')?->value ?? 'assets/img/logo/logo_white.svg',
                        'letterheadPath' => $settings->get('letterhead_path')?->value ?? 'portal_assets/img/users/letter_head.png',
                        'registrarSig' => $settings->get('registrar_signature_path')?->value ?? 'portal_assets/img/users/signature.png',
                        'registrarName' => $settings->get('registrar_name')?->value ?? '',
                        'systemSettings' => $settings,   // full collection for advanced use
                    ]);
                } else {
                    $view->with([
                        'activeSession' => $activeSession,
                        'activeSemester' => $activeSemester,
                        'schoolName' => 'University of Offa',
                        'schoolLogo' => 'assets/img/logo/logo_white.svg',
                        'schoolMotto' => '',
                        'schoolAddress' => '',
                        'contactEmail' => '',
                        'contactPhone' => '',
                        'letterheadPath' => 'portal_assets/img/users/letter_head.png',
                        'registrarSig' => 'portal_assets/img/users/signature.png',
                        'registrarName' => '',
                        'systemSettings' => collect(),
                    ]);
                }
            } catch (\Exception $e) {
                $view->with([
                    'activeSession' => $activeSession,
                    'activeSemester' => $activeSemester,
                    'schoolName' => 'University of Offa',
                    'schoolLogo' => 'assets/img/logo/logo_white.svg',
                    'schoolMotto' => '',
                    'schoolAddress' => '',
                    'contactEmail' => '',
                    'contactPhone' => '',
                    'letterheadPath' => 'portal_assets/img/users/letter_head.png',
                    'registrarSig' => 'portal_assets/img/users/signature.png',
                    'registrarName' => '',
                    'systemSettings' => collect(),
                ]);
            }
        });
    }
}

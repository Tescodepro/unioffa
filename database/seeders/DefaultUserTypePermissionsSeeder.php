<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;
use App\Models\Permission;

/**
 * Seeds the default permissions for each user type.
 * This runs automatically when you do: php artisan db:seed --class=DefaultUserTypePermissionsSeeder
 *
 * Safe to re-run — uses syncWithoutDetaching() so it only ADDS, never removes.
 * To fully reset a user type's permissions, use the UI: User Types → Permissions.
 */
class DefaultUserTypePermissionsSeeder extends Seeder
{
    /**
     * Map of user type name → data (permissions and dashboard route).
     */
    protected array $defaults = [
        'ict' => [
            'route' => 'ict.dashboard',
            'perms' => [
                'view_dashboard', 'manage_students', 'manage_users', 'manage_user_types',
                'manage_settings', 'manage_website', 'manage_agents', 'upload_results',
                'view_uploaded_results', 'manage_result_status', 'view_result_summary',
                'view_transcripts', 'view_all_courses', 'view_course_assignments',
                'manage_staff', 'access_ict_portal',
            ],
        ],
        'bursary' => [
            'route' => 'burser.dashboard',
            'perms' => [
                'view_dashboard', 'view_payment_summary', 'view_transactions', 'verify_payments',
                'upload_manual_payment', 'approve_payments', 'view_reports', 'manage_payment_settings',
                'manage_transactions', 'access_bursary_portal',
            ],
        ],
        'dean' => [
            'route' => 'lecturer.dean.dashboard',
            'perms' => [
                'view_dashboard', 'upload_results', 'view_uploaded_results', 'manage_result_status',
                'view_result_summary', 'view_transcripts', 'view_all_courses', 'view_course_assignments',
                'manage_staff', 'access_dean_portal',
            ],
        ],
        'hod' => [
            'route' => 'lecturer.dashboard',
            'perms' => [
                'view_dashboard', 'upload_results', 'view_uploaded_results', 'view_result_summary',
                'view_all_courses', 'view_course_assignments', 'manage_staff', 'access_hod_portal',
            ],
        ],
        'lecturer' => [
            'route' => 'lecturer.dashboard',
            'perms' => [
                'view_dashboard', 'upload_results', 'view_uploaded_results', 'view_result_summary',
                'view_all_courses', 'view_course_assignments', 'access_lecturer_portal',
            ],
        ],
        'registrar' => [
            'route' => 'registrar.dashboard',
            'perms' => [
                'view_dashboard', 'manage_students', 'view_transcripts', 'view_result_summary',
                'manage_staff', 'access_registrar_portal',
            ],
        ],
        'vice-chancellor' => [
            'route' => 'vc.dashboard',
            'perms' => [
                'view_dashboard', 'manage_students', 'view_uploaded_results', 'view_result_summary',
                'view_transcripts', 'view_reports', 'view_payment_summary', 'approve_payments',
                'manage_agents', 'access_vc_portal',
            ],
        ],
        'administrator' => [
            'route' => 'admin.dashboard',
            'perms' => [
                'view_dashboard', 'manage_students', 'manage_users', 'manage_user_types',
                'manage_settings', 'manage_website', 'manage_agents', 'upload_results',
                'view_uploaded_results', 'manage_result_status', 'view_result_summary',
                'view_transcripts', 'view_all_courses', 'view_course_assignments',
                'manage_staff', 'access_admin_portal',
            ],
        ],
        'programme-director' => [
            'route' => 'programme-director.dashboard',
            'perms' => [
                'view_dashboard', 'access_programme_director_portal', 'view_admission',
            ],
        ],
        'center-director' => [
            'route' => 'center-director.dashboard',
            'perms' => [
                'view_dashboard', 'access_center_director_portal', 'view_admission',
            ],
        ],
        'public relations officer' => [
            'route' => 'pro.dashboard',
            'perms' => [
                'view_dashboard', 'access_pro_portal',
            ],
        ],
        'applicant' => [
            'route' => 'students.dashboard', // Applicant and student likely land on same/similar dashboard
            'perms' => ['view_dashboard'],
        ],
        'student' => [
            'route' => 'students.dashboard',
            'perms' => ['view_dashboard'],
        ],
    ];

    public function run(): void
    {
        // Pre-load all permissions keyed by identifier for fast lookup
        $allPermissions = Permission::all()->keyBy('identifier');

        foreach ($this->defaults as $userTypeName => $data) {
            $userType = UserType::where('name', $userTypeName)->first();

            if (!$userType) {
                // Try case-insensitive or slugified search if exact match fails
                $userType = UserType::where('name', 'like', $userTypeName)->first();
            }

            if (!$userType) {
                $this->command->warn("UserType '{$userTypeName}' not found — skipping.");
                continue;
            }

            // Update dashboard route
            $userType->update(['dashboard_route' => $data['route']]);

            // Resolve identifiers to UUIDs
            $ids = collect($data['perms'])
                ->filter(fn($id) => $allPermissions->has($id))
                ->map(fn($id) => $allPermissions->get($id)->id)
                ->values()
                ->toArray();

            // syncWithoutDetaching = only ADD, never remove existing assignments
            $userType->permissions()->syncWithoutDetaching($ids);

            $this->command->info("✓ '{$userTypeName}' — route: {$data['route']}, assigned " . count($ids) . " default permissions.");
        }
    }
}

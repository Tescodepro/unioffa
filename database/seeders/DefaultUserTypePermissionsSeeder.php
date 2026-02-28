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
     * Map of user type name → array of permission identifiers to assign by default.
     * Add or remove entries here to control what each role gets out of the box.
     */
    protected array $defaults = [

        // ─── ICT: full system access (except super-admin bypass) ─────────────
        'ict' => [
            'view_dashboard',
            'manage_students',
            'manage_users',
            'manage_user_types',   // <-- this is what grants access to User Types page
            'manage_settings',
            'manage_website',
            'manage_agents',
            'upload_results',
            'view_uploaded_results',
            'manage_result_status',
            'view_result_summary',
            'view_transcripts',
            'view_all_courses',
            'view_course_assignments',
            'manage_staff',
        ],

        // ─── Bursary: financial management ───────────────────────────────────
        'bursary' => [
            'view_dashboard',
            'view_payment_summary',
            'view_transactions',
            'verify_payments',
            'upload_manual_payment',
            'approve_payments',
            'view_reports',
            'manage_payment_settings',
            'manage_transactions',
        ],

        // ─── Dean: academic leadership ────────────────────────────────────────
        'dean' => [
            'view_dashboard',
            'upload_results',
            'view_uploaded_results',
            'manage_result_status',
            'view_result_summary',
            'view_transcripts',
            'view_all_courses',
            'view_course_assignments',
            'manage_staff',
        ],

        // ─── HOD: departmental management (subset of dean) ────────────────────
        'hod' => [
            'view_dashboard',
            'upload_results',
            'view_uploaded_results',
            'view_result_summary',
            'view_all_courses',
            'view_course_assignments',
            'manage_staff',
        ],

        // ─── Lecturer: teaching staff ─────────────────────────────────────────
        'lecturer' => [
            'view_dashboard',
            'upload_results',
            'view_uploaded_results',
            'view_result_summary',
            'view_all_courses',
            'view_course_assignments',
        ],

        // ─── Registrar: student records ───────────────────────────────────────
        'registrar' => [
            'view_dashboard',
            'manage_students',
            'view_transcripts',
            'view_result_summary',
            'manage_staff',
        ],

        // ─── Vice-Chancellor: full read + approval ────────────────────────────
        'vice-chancellor' => [
            'view_dashboard',
            'manage_students',
            'view_uploaded_results',
            'view_result_summary',
            'view_transcripts',
            'view_reports',
            'view_payment_summary',
            'approve_payments',
            'manage_agents',
        ],
    ];

    public function run(): void
    {
        // Pre-load all permissions keyed by identifier for fast lookup
        $allPermissions = Permission::all()->keyBy('identifier');

        foreach ($this->defaults as $userTypeName => $permissionIdentifiers) {
            $userType = UserType::where('name', $userTypeName)->first();

            if (!$userType) {
                $this->command->warn("UserType '{$userTypeName}' not found — skipping.");
                continue;
            }

            // Resolve identifiers to UUIDs
            $ids = collect($permissionIdentifiers)
                ->filter(fn($id) => $allPermissions->has($id))
                ->map(fn($id) => $allPermissions->get($id)->id)
                ->values()
                ->toArray();

            // syncWithoutDetaching = only ADD, never remove existing assignments
            $userType->permissions()->syncWithoutDetaching($ids);

            $this->command->info("✓ '{$userTypeName}' — assigned " . count($ids) . " default permissions.");
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Portal Access Permissions
            ['identifier' => 'access_admin_portal', 'name' => 'Access Admin Portal'],
            ['identifier' => 'access_vc_portal', 'name' => 'Access VC Portal'],
            ['identifier' => 'access_registrar_portal', 'name' => 'Access Registrar Portal'],
            ['identifier' => 'access_bursary_portal', 'name' => 'Access Bursary Portal'],
            ['identifier' => 'access_ict_portal', 'name' => 'Access ICT Portal'],
            ['identifier' => 'access_dean_portal', 'name' => 'Access Dean Portal'],
            ['identifier' => 'access_hod_portal', 'name' => 'Access Hod Portal'],
            ['identifier' => 'access_lecturer_portal', 'name' => 'Access Lecturer Portal'],
            ['identifier' => 'access_programme_director_portal', 'name' => 'Access Programme Director Portal'],
            ['identifier' => 'access_center_director_portal', 'name' => 'Access Center Director Portal'],
            ['identifier' => 'access_pro_portal', 'name' => 'Access PRO Portal'],
            ['identifier' => 'manage_admission', 'name' => 'Manage Admission'],

            // General
            ['identifier' => 'view_dashboard', 'name' => 'View Dashboard'],

            // User Management
            ['identifier' => 'manage_users', 'name' => 'Manage Users'],
            ['identifier' => 'manage_user_types', 'name' => 'Manage User Types'],

            // Student Management
            ['identifier' => 'manage_students', 'name' => 'Manage Students'],

            // Result Management
            ['identifier' => 'upload_results', 'name' => 'Upload Results'],
            ['identifier' => 'view_uploaded_results', 'name' => 'View Uploaded Results'],
            ['identifier' => 'manage_result_status', 'name' => 'Manage Result Status'],
            ['identifier' => 'view_result_summary', 'name' => 'View Result Summary'],
            ['identifier' => 'view_transcripts', 'name' => 'View Transcripts'],

            // Course Management
            ['identifier' => 'view_all_courses', 'name' => 'View All Courses'],
            ['identifier' => 'view_course_assignments', 'name' => 'View Course Assignments'],

            // Staff Management
            ['identifier' => 'manage_staff', 'name' => 'Manage Staff'],

            // Financial Management
            ['identifier' => 'view_reports', 'name' => 'View Reports'],
            ['identifier' => 'manage_settings', 'name' => 'Manage Settings'], // Application/System Settings
            ['identifier' => 'view_transactions', 'name' => 'View Transactions'],
            ['identifier' => 'manage_transactions', 'name' => 'Manage Transactions'],
            ['identifier' => 'view_payment_summary', 'name' => 'View Payment Summary'],
            ['identifier' => 'verify_payments', 'name' => 'Verify Payments'],
            ['identifier' => 'upload_manual_payment', 'name' => 'Upload Manual Payment'],
            ['identifier' => 'manage_payment_settings', 'name' => 'Manage Payment Settings'],
            ['identifier' => 'approve_payments', 'name' => 'Approve Payments'],

            // Website Management
            ['identifier' => 'manage_website', 'name' => 'Manage Website'], // News, etc.

            // Agent Management
            ['identifier' => 'manage_agents', 'name' => 'Manage Agents'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['identifier' => $perm['identifier']],
                [
                    'id' => Str::uuid(),
                    'name' => $perm['name'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
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
                    'name' => $perm['name']
                ]
            );
        }
    }
}

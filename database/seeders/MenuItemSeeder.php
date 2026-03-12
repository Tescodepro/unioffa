<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Seeds the sidebar navigation.
     * section     = heading group shown in the sidebar
     * label       = link text
     * icon        = Tabler icon class (ti ti-*)
     * route_name  = Laravel named route
     * route_pattern = passed to activeClass() helper (optional)
     * permission_identifier = null means "any authenticated staff can see it"
     * sort_order  = controls order within a section
     */
    public function run(): void
    {
        MenuItem::truncate();

        $items = [
            // ── Admission (VC view) ───────────────────────────────────────────
            [
                'section' => 'Admission',
                'label' => 'Overview',
                'icon' => 'ti ti-layout-dashboard',
                'route_name' => 'vc.admission.overview',
                'route_pattern' => 'staff/vc/admission/overview',
                'permission_identifier' => 'view_admission',
                'sort_order' => 10,
                'user_type_scope' => 'vice-chancellor',
            ],
            [
                'section' => 'Admission',
                'label' => 'All Applicants',
                'icon' => 'ti ti-users',
                'route_name' => 'vc.admission.applicants',
                'route_pattern' => 'staff/vc/admission/applicants',
                'permission_identifier' => 'view_admission',
                'sort_order' => 20,
                'user_type_scope' => 'vice-chancellor',
            ],
            // ── Admission (Registrar view) ────────────────────────────────────
            [
                'section' => 'Admission',
                'label' => 'Overview',
                'icon' => 'ti ti-layout-dashboard',
                'route_name' => 'registrar.admission.overview',
                'route_pattern' => 'staff/registrar/admission/overview',
                'permission_identifier' => 'view_admission',
                'sort_order' => 10,
                'user_type_scope' => 'registrar',
            ],
            [
                'section' => 'Admission',
                'label' => 'Applicants',
                'icon' => 'ti ti-users',
                'route_name' => 'registrar.admission.applicants',
                'route_pattern' => 'staff/registrar/admission/applicants',
                'permission_identifier' => 'view_admission',
                'sort_order' => 20,
                'user_type_scope' => 'registrar',
            ],

            // ── Results ───────────────────────────────────────────────────────
            [
                'section' => 'Result Management',
                'label' => 'Upload Results',
                'icon' => 'ti ti-upload',
                'route_name' => 'staff.results.upload',
                'route_pattern' => 'staff/dean/results/upload',
                'permission_identifier' => 'upload_results',
                'sort_order' => 10,
            ],
            [
                'section' => 'Result Management',
                'label' => 'Backlog Upload',
                'icon' => 'ti ti-history',
                'route_name' => 'backlog.upload.page',
                'route_pattern' => 'staff/dean/backlog-upload',
                'permission_identifier' => 'upload_results',
                'sort_order' => 15,
            ],
            [
                'section' => 'Result Management',
                'label' => 'Uploaded Results',
                'icon' => 'ti ti-eye',
                'route_name' => 'results.viewUploaded',
                'route_pattern' => 'staff/dean/results/view-uploaded',
                'permission_identifier' => 'view_uploaded_results',
                'sort_order' => 20,
            ],
            [
                'section' => 'Result Management',
                'label' => 'Manage Status',
                'icon' => 'ti ti-settings',
                'route_name' => 'results.manage.status',
                'route_pattern' => 'staff/dean/results/manage-status',
                'permission_identifier' => 'manage_result_status',
                'sort_order' => 30,
            ],
            [
                'section' => 'Result Management',
                'label' => 'Result Summary',
                'icon' => 'ti ti-report',
                'route_name' => 'results.summary',
                'route_pattern' => 'staff/dean/results/summary',
                'permission_identifier' => 'view_result_summary',
                'sort_order' => 40,
            ],
            [
                'section' => 'Result Management',
                'label' => 'Transcript',
                'icon' => 'ti ti-certificate',
                'route_name' => 'transcript.search.page',
                'route_pattern' => 'staff/dean/transcript*',
                'permission_identifier' => 'view_transcripts',
                'sort_order' => 50,
            ],

            // ── Courses ───────────────────────────────────────────────────────
            [
                'section' => 'Course Management',
                'label' => 'All Courses',
                'icon' => 'ti ti-book',
                'route_name' => 'staff.courses.index',
                'route_pattern' => 'staff/dean/courses',
                'permission_identifier' => 'view_all_courses',
                'sort_order' => 10,
            ],
            [
                'section' => 'Course Management',
                'label' => 'Course Assignments',
                'icon' => 'ti ti-clipboard-list',
                'route_name' => 'staff.course.assignments',
                'route_pattern' => 'staff/dean/staff/course-assignments',
                'permission_identifier' => 'view_course_assignments',
                'sort_order' => 20,
            ],

            // ── Reports ───────────────────────────────────────────────────────
            [
                'section' => 'Academic Reports',
                'label' => 'Result Broadsheet',
                'icon' => 'ti ti-file-report',
                'route_name' => 'broadsheet.sessional',
                'route_pattern' => 'reports/broadsheet/sessional',
                'permission_identifier' => 'view_broadsheet',
                'sort_order' => 10,
            ],
            [
                'section' => 'Academic Reports',
                'label' => 'Semester Result',
                'icon' => 'ti ti-file-analytics',
                'route_name' => 'broadsheet.semester',
                'route_pattern' => 'reports/broadsheet/semester',
                'permission_identifier' => 'view_semester_result',
                'sort_order' => 20,
            ],

            // ── Staff Management ──────────────────────────────────────────────
            [
                'section' => 'Staff Management',
                'label' => 'All Staff',
                'icon' => 'ti ti-users',
                'route_name' => 'staff.index',
                'route_pattern' => 'staff/dean/staff',
                'permission_identifier' => 'manage_staff',
                'sort_order' => 10,
            ],

            // ── Student Management ────────────────────────────────────────────
            [
                'section' => 'Student Management',
                'label' => 'All Students',
                'icon' => 'ti ti-users',
                'route_name' => 'ict.students.index',
                'route_pattern' => 'staff/ict/students',
                'permission_identifier' => 'manage_students',
                'sort_order' => 10,
            ],
            [
                'section' => 'Student Management',
                'label' => 'Add Student',
                'icon' => 'ti ti-user-plus',
                'route_name' => 'ict.students.create',
                'route_pattern' => 'staff/ict/students/create',
                'permission_identifier' => 'manage_students',
                'sort_order' => 20,
            ],
            [
                'section' => 'Student Management',
                'label' => 'Bulk Upload',
                'icon' => 'ti ti-table-import',
                'route_name' => 'ict.students.bulk',
                'route_pattern' => 'staff/ict/students/bulk',
                'permission_identifier' => 'manage_students',
                'sort_order' => 30,
            ],
            [
                'section' => 'Student Management',
                'label' => 'Admitted Students',
                'icon' => 'ti ti-user-check',
                'route_name' => 'admitted-students.index',
                'route_pattern' => 'staff/admitted-students',
                'permission_identifier' => 'manage_students',
                'sort_order' => 40,
            ],

            // ── User Management ───────────────────────────────────────────────
            [
                'section' => 'System Management',
                'label' => 'Users',
                'icon' => 'ti ti-user-cog',
                'route_name' => 'ict.staff.users.index',
                'route_pattern' => 'staff/ict/users',
                'permission_identifier' => 'manage_users',
                'sort_order' => 10,
            ],
            [
                'section' => 'System Management',
                'label' => 'User Types & Permissions',
                'icon' => 'ti ti-shield-lock',
                'route_name' => 'ict.user-types.index',
                'route_pattern' => 'staff/ict/user-types*',
                'permission_identifier' => 'manage_user_types',
                'sort_order' => 20,
            ],
            [
                'section' => 'System Management',
                'label' => 'Application Settings',
                'icon' => 'ti ti-adjustments',
                'route_name' => 'ict.application_settings.index',
                'route_pattern' => 'staff/ict/application-settings*',
                'permission_identifier' => 'manage_settings',
                'sort_order' => 30,
            ],
            [
                'section' => 'System Management',
                'label' => 'System Settings',
                'icon' => 'ti ti-settings-2',
                'route_name' => 'ict.system_settings.index',
                'route_pattern' => 'staff/ict/system-settings',
                'permission_identifier' => 'manage_settings',
                'sort_order' => 40,
            ],
            [
                'section' => 'System Management',
                'label' => 'Website / News',
                'icon' => 'ti ti-news',
                'route_name' => 'ict.news.index',
                'route_pattern' => 'staff/ict/news*',
                'permission_identifier' => 'manage_website',
                'sort_order' => 50,
            ],

            // ── Finance ───────────────────────────────────────────────────────
            [
                'section' => 'Finance',
                'label' => 'Payment Summary',
                'icon' => 'ti ti-chart-bar',
                'route_name' => 'burser.dashboard',
                'route_pattern' => 'staff/burser/dashboard',
                'permission_identifier' => 'view_payment_summary',
                'sort_order' => 10,
            ],
            [
                'section' => 'Finance',
                'label' => 'Transactions',
                'icon' => 'ti ti-credit-card',
                'route_name' => 'bursary.transactions',
                'route_pattern' => 'staff/burser/transactions',
                'permission_identifier' => 'view_transactions',
                'sort_order' => 20,
            ],
            [
                'section' => 'Finance',
                'label' => 'Verify Payments',
                'icon' => 'ti ti-checkbox',
                'route_name' => 'bursary.verify.form',
                'route_pattern' => 'staff/burser/verify-payment',
                'permission_identifier' => 'verify_payments',
                'sort_order' => 30,
            ],
            [
                'section' => 'Finance',
                'label' => 'Manual Payment',
                'icon' => 'ti ti-receipt',
                'route_name' => 'bursary.transactions.create',
                'route_pattern' => 'staff/burser/transactions/create',
                'permission_identifier' => 'upload_manual_payment',
                'sort_order' => 40,
            ],
            [
                'section' => 'Finance',
                'label' => 'Report by Faculty',
                'icon' => 'ti ti-building-community',
                'route_name' => 'bursary.reports.faculty',
                'route_pattern' => 'staff/burser/reports/faculty',
                'permission_identifier' => 'view_reports',
                'sort_order' => 50,
            ],
            [
                'section' => 'Finance',
                'label' => 'Report by Department',
                'icon' => 'ti ti-briefcase',
                'route_name' => 'bursary.reports.department',
                'route_pattern' => 'staff/burser/reports/department',
                'permission_identifier' => 'view_reports',
                'sort_order' => 51,
            ],
            [
                'section' => 'Finance',
                'label' => 'Report by Level',
                'icon' => 'ti ti-layers-intersect',
                'route_name' => 'bursary.reports.level',
                'route_pattern' => 'staff/burser/reports/level',
                'permission_identifier' => 'view_reports',
                'sort_order' => 52,
            ],
            [
                'section' => 'Finance',
                'label' => 'Report by Student',
                'icon' => 'ti ti-users',
                'route_name' => 'bursary.reports.student',
                'route_pattern' => 'staff/burser/reports/student',
                'permission_identifier' => 'view_reports',
                'sort_order' => 53,
            ],
            [
                'section' => 'Finance',
                'label' => 'Student History',
                'icon' => 'ti ti-history',
                'route_name' => 'bursary.student.history',
                'route_pattern' => 'staff/burser/student-history*',
                'permission_identifier' => 'view_transactions',
                'sort_order' => 55,
            ],
            [
                'section' => 'Finance',
                'label' => 'Payment Settings',
                'icon' => 'ti ti-settings',
                'route_name' => 'bursary.payment-settings.index',
                'route_pattern' => 'staff/burser/payment-settings*',
                'permission_identifier' => 'manage_payment_settings',
                'sort_order' => 60,
            ],

            // ── Admission (Center Director) ──────────────────────────────────
            [
                'section' => 'Admission',
                'label' => 'Applicants',
                'icon' => 'ti ti-users',
                'route_name' => 'center-director.admission.applicants',
                'route_pattern' => 'staff/center-director/admission/applicants',
                'permission_identifier' => 'view_admission',
                'sort_order' => 10,
                'user_type_scope' => 'center-director',
            ],

            // ── Admission (Programme Director) ────────────────────────────────
            [
                'section' => 'Admission',
                'label' => 'Applicants',
                'icon' => 'ti ti-users',
                'route_name' => 'programme-director.admission.applicants',
                'route_pattern' => 'staff/programme-director/admission',
                'permission_identifier' => 'view_admission',
                'sort_order' => 10,
                'user_type_scope' => 'programme-director',
            ],

            // ── Agents ────────────────────────────────────────────────────────
            [
                'section' => 'Agents',
                'label' => 'Agent Applicants',
                'icon' => 'ti ti-user-star',
                'route_name' => 'admin.agent.applicants',
                'route_pattern' => 'staff/agent-applicants*',
                'permission_identifier' => 'manage_agents',
                'sort_order' => 10,
            ],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}

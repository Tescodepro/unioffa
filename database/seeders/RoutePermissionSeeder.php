<?php

namespace Database\Seeders;

use App\Models\RoutePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class RoutePermissionSeeder extends Seeder
{
    public function run()
    {
        $mappings = [
            // --- Admission Management ---
            'admission.overview' => 'manage_admission',
            'admission.applicants' => 'manage_admission',
            'admission.details' => 'manage_admission',
            'admission.admit' => 'manage_admission',
            'admission.recommend' => 'manage_admission',
            'admission.exportApplicants' => 'manage_admission',
            'admin.agent.applicants' => 'view_agent_applications',
            'admin.agent.application.update_status' => 'manage_agent_applications',

            // --- Dashboards ---
            'admin.dashboard' => 'view_dashboard',
            'vc.dashboard' => 'access_vc_portal',
            'registrar.dashboard' => 'access_registrar_portal',
            'center-director.dashboard' => 'access_center_director_portal',
            'center-director.admission.applicants' => 'access_center_director_portal',
            'programme-director.dashboard' => 'access_programme_director_portal',
            'burser.dashboard' => 'access_bursary_portal',
            'ict.dashboard' => 'access_ict_portal',
            'lecturer.dean.dashboard' => 'access_dean_portal',
            'lecturer.dashboard' => 'access_lecturer_portal',

            // --- Student & ICT Management ---
            'admitted-students.index' => 'manage_students',
            'admitted-students.download' => 'manage_students',
            'ict.students.index' => 'manage_students',
            'ict.students.create' => 'manage_students',
            'ict.students.store' => 'manage_students',
            'ict.students.edit' => 'manage_students',
            'ict.students.update' => 'manage_students',
            'ict.students.destroy' => 'manage_students',
            'ict.students.bulk' => 'manage_students',
            'ict.students.bulk.upload' => 'manage_students',
            'ict.students.bulk.template' => 'manage_students',

            // --- Financial / Bursary ---
            'bursary.transactions' => 'view_transactions',
            'bursary.reports.faculty' => 'view_reports',
            'bursary.reports.department' => 'view_reports',
            'bursary.reports.level' => 'view_reports',
            'bursary.reports.student' => 'view_reports',
            'bursary.reports.export' => 'view_reports',
            'bursary.student.history' => 'view_transactions',
            'bursary.student.receipt' => 'view_transactions',
            'bursary.transactions.export' => 'view_transactions',
            'bursary.transactions.verify' => 'verify_payments',
            'bursary.verify.form' => 'verify_payments',
            'bursary.verify.action' => 'verify_payments',
            'bursary.transactions.create' => 'manage_transactions',
            'bursary.transactions.store' => 'manage_transactions',
            'bursary.transactions.update' => 'manage_transactions',
            'bursary.transactions.destroy' => 'manage_transactions',
            'bursary.payment-settings.index' => 'manage_payment_settings',
            'bursary.payment-settings.store' => 'manage_payment_settings',
            'bursary.payment-settings.edit' => 'manage_payment_settings',
            'bursary.payment-settings.update' => 'manage_payment_settings',
            'bursary.payment-settings.destroy' => 'manage_payment_settings',

            // --- Staff & Course Management ---
            'staff.users.index' => 'manage_staff',
            'staff.users.store' => 'manage_staff',
            'staff.users.update' => 'manage_staff',
            'staff.users.destroy' => 'manage_staff',
            'staff.course.assignments' => 'manage_course_assignments',
            'staff.course.assign' => 'manage_course_assignments',
            'staff.course.assign.delete' => 'manage_course_assignments',
            'dean.department.students' => 'manage_staff',
            'staff.index' => 'manage_staff',
            'staff.store' => 'manage_staff',
            'staff.update' => 'manage_staff',
            'staff.destroy' => 'manage_staff',
            'staff.courses.index' => 'view_all_courses',
            'staff.courses.store' => 'manage_settings',
            'staff.courses.update' => 'manage_settings',
            'staff.courses.destroy' => 'manage_settings',

            // --- Academic / Results ---
            'broadsheet.sessional' => 'view_result_summary',
            'broadsheet.semester' => 'view_result_summary',
            'broadsheet.printOfficial' => 'view_result_summary',
            'staff.results.upload' => 'upload_results',
            'staff.results.process' => 'upload_results',
            'staff.results.download' => 'view_uploaded_results',
            'results.manage.status' => 'manage_result_status',
            'results.update.status' => 'manage_result_status',
            'transcript.search.page' => 'view_transcripts',
            'results.printTranscript' => 'view_transcripts',
            'results.viewUploaded' => 'view_uploaded_results',
            'results.printUploaded' => 'view_uploaded_results',
            'results.download' => 'view_uploaded_results',
            'results.summary' => 'view_result_summary',
            'results.printSummary' => 'view_result_summary',
            'transcript.search' => 'view_transcripts',
            'results.bulk.update' => 'manage_result_status',
            'backlog.upload.page' => 'upload_results',
            'backlog.upload.process' => 'upload_results',
            'backlog.upload.template' => 'upload_results',

            // --- ICT / System Setup ---
            'ict.application-settings.index' => 'manage_settings',
            'ict.application-settings.create' => 'manage_settings',
            'ict.application-settings.store' => 'manage_settings',
            'ict.application-settings.edit' => 'manage_settings',
            'ict.application-settings.update' => 'manage_settings',
            'ict.semesters.index' => 'manage_settings',
            'ict.semesters.store' => 'manage_settings',
            'ict.semesters.update' => 'manage_settings',
            'ict.semesters.destroy' => 'manage_settings',
            'ict.sessions.index' => 'manage_settings',
            'ict.sessions.store' => 'manage_settings',
            'ict.sessions.update' => 'manage_settings',
            'ict.sessions.destroy' => 'manage_settings',
            'ict.faculties.index' => 'manage_settings',
            'ict.faculties.store' => 'manage_settings',
            'ict.faculties.update' => 'manage_settings',
            'ict.faculties.destroy' => 'manage_settings',
            'ict.departments.index' => 'manage_settings',
            'ict.departments.store' => 'manage_settings',
            'ict.departments.update' => 'manage_settings',
            'ict.departments.destroy' => 'manage_settings',
            'ict.staff.users.index' => 'manage_users',
            'ict.staff.users.store' => 'manage_users',
            'ict.staff.users.update' => 'manage_users',
            'ict.staff.users.destroy' => 'manage_users',
            'ict.applications.incomplete' => 'manage_admission',
            'ict.applications.unsubmit' => 'manage_admission',
            'ict.search.students' => 'view_students',
            'ict.search.lecturers' => 'view_staff',
            'ict.news.index' => 'manage_website',
            'ict.news.create' => 'manage_website',
            'ict.news.store' => 'manage_website',
            'ict.user-types.index' => 'manage_user_types',
            'ict.user-types.create' => 'manage_user_types',
            'ict.user-types.store' => 'manage_user_types',
            'ict.user-types.permissions' => 'manage_user_types',
            'ict.user-types.permissions.update' => 'manage_user_types',
            'ict.permissions.index' => 'manage_user_types',
            'ict.permissions.store' => 'manage_user_types',
            'ict.permissions.update' => 'manage_user_types',
            'ict.permissions.destroy' => 'manage_user_types',
            'ict.menu-items.index' => 'manage_settings',
            'ict.menu-items.store' => 'manage_settings',
            'ict.menu-items.update' => 'manage_settings',
            'ict.menu-items.destroy' => 'manage_settings',
            'ict.system_settings.index' => 'manage_settings',
            'ict.system_settings.update' => 'manage_settings',
        ];

        foreach ($mappings as $route => $permission) {
            RoutePermission::updateOrCreate(
                ['route_name' => $route],
                ['permission_identifier' => $permission]
            );
        }

        Cache::forget('route_permissions_map');
    }
}

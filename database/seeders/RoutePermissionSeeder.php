<?php

namespace Database\Seeders;

use App\Models\RoutePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class RoutePermissionSeeder extends Seeder
{
    /**
     * Maps every named staff route to its required permission identifier.
     * Edit this table in the DB to change access — no code deploy needed.
     */
    public function run(): void
    {
        $mappings = [
            // ── Student Management ────────────────────────────────────────────
            'ict.students.index' => 'manage_students',
            'ict.students.create' => 'manage_students',
            'ict.students.store' => 'manage_students',
            'ict.students.edit' => 'manage_students',
            'ict.students.update' => 'manage_students',
            'ict.students.destroy' => 'manage_students',
            'ict.students.bulk' => 'manage_students',
            'ict.students.bulk.upload' => 'manage_students',
            'ict.students.bulk.template' => 'manage_students',
            'admitted-students.index' => 'manage_students',
            'admitted-students.download' => 'manage_students',

            // ── Academic Reports ─────────────────────────────────────────────
            'broadsheet.sessional' => 'view_broadsheet',
            'broadsheet.semester' => 'view_semester_result',

            // ── Financial / Bursary ──────────────────────────────────────────
            'bursary.student.history' => 'view_transactions',
            'bursary.student.receipt' => 'view_transactions',
            'bursary.transactions' => 'view_transactions',
            'bursary.transactions.export' => 'view_transactions',
            'bursary.transactions.verify' => 'view_transactions',
            'bursary.verify.form' => 'verify_payments',
            'bursary.verify.action' => 'verify_payments',
            'bursary.transactions.create' => 'upload_manual_payment',
            'bursary.transactions.store' => 'upload_manual_payment',
            'bursary.transactions.update' => 'upload_manual_payment',
            'bursary.transactions.destroy' => 'upload_manual_payment',
            'bursary.reports.faculty' => 'view_reports',
            'bursary.reports.department' => 'view_reports',
            'bursary.reports.level' => 'view_reports',
            'bursary.reports.student' => 'view_reports',
            'bursary.reports.export' => 'view_reports',
            'bursary.payment-settings.index' => 'manage_payment_settings',
            'bursary.payment-settings.create' => 'manage_payment_settings',
            'bursary.payment-settings.store' => 'manage_payment_settings',
            'bursary.payment-settings.edit' => 'manage_payment_settings',
            'bursary.payment-settings.update' => 'manage_payment_settings',
            'bursary.payment-settings.destroy' => 'manage_payment_settings',

            // ── Results ──────────────────────────────────────────────────────
            'staff.results.upload' => 'upload_results',
            'staff.results.process' => 'upload_results',
            'staff.results.template' => 'upload_results',
            'staff.results.download' => 'upload_results',
            'backlog.upload.page' => 'upload_results',
            'backlog.upload.process' => 'upload_results',
            'backlog.upload.template' => 'upload_results',
            'results.update' => 'upload_results',
            'results.delete' => 'upload_results',
            'results.viewUploaded' => 'view_uploaded_results',
            'results.download' => 'view_uploaded_results',
            'results.manage.status' => 'manage_result_status',
            'results.update.status' => 'manage_result_status',
            'results.bulk.update' => 'manage_result_status',
            'results.summary' => 'view_result_summary',

            // ── Transcripts ──────────────────────────────────────────────────
            'transcript.search' => 'view_transcripts',
            'transcript.search.page' => 'view_transcripts',

            // ── Courses ──────────────────────────────────────────────────────
            'staff.courses.index' => 'view_all_courses',
            'staff.courses.store' => 'view_all_courses',
            'staff.courses.update' => 'view_all_courses',
            'staff.courses.destroy' => 'view_all_courses',
            'staff.course.assignments' => 'view_course_assignments',
            'staff.course.assign' => 'view_course_assignments',
            'staff.course.assign.delete' => 'view_course_assignments',

            // ── Staff Management ─────────────────────────────────────────────
            'staff.index' => 'manage_staff',
            'staff.store' => 'manage_staff',
            'staff.update' => 'manage_staff',
            'staff.destroy' => 'manage_staff',

            // ── ICT / Users / Settings ───────────────────────────────────────
            'ict.staff.users.index' => 'manage_users',
            'ict.staff.users.update' => 'manage_users',
            'ict.staff.users.disable' => 'manage_users',
            'ict.staff.users.enable' => 'manage_users',
            'ict.staff.users.destroy' => 'manage_users',
            'ict.news.index' => 'manage_website',
            'ict.news.create' => 'manage_website',
            'ict.news.store' => 'manage_website',
            'ict.news.edit' => 'manage_website',
            'ict.news.update' => 'manage_website',
            'ict.news.destroy' => 'manage_website',
            'ict.application_settings.index' => 'manage_settings',
            'ict.application_settings.create' => 'manage_settings',
            'ict.application_settings.store' => 'manage_settings',
            'ict.application_settings.edit' => 'manage_settings',
            'ict.application_settings.update' => 'manage_settings',
            'ict.system_settings.index' => 'manage_settings',
            'ict.system_settings.update' => 'manage_settings',
            'ict.system_settings.grading.update' => 'manage_settings',
            'ict.user-types.index' => 'manage_user_types',
            'ict.user-types.create' => 'manage_user_types',
            'ict.user-types.store' => 'manage_user_types',
            'ict.user-types.permissions' => 'manage_user_types',
            'ict.user-types.permissions.update' => 'manage_user_types',

            // ── Admission ────────────────────────────────────────────────────
            'admission.overview' => 'manage_admission',
            'admission.applicants' => 'manage_admission',
            'admission.applicants.details' => 'manage_admission',
            'admission.admit' => 'manage_admission',
            'admission.recommend' => 'manage_admission',
            'admission.exportApplicants' => 'manage_admission',

            // ── Dashboards & Portal Access ─────────────────────────────────────
            'admin.dashboard' => 'access_admin_portal',
            'vc.dashboard' => 'access_vc_portal',
            'registrar.dashboard' => 'access_registrar_portal',
            'burser.dashboard' => 'access_bursary_portal',
            'ict.dashboard' => 'access_ict_portal',
            'lecturer.dean.dashboard' => 'access_dean_portal',
            'lecturer.dashboard' => 'access_lecturer_portal',
            'centre-director.dashboard' => 'access_center_director_portal',
            'programme-director.dashboard' => 'access_programme_director_portal',
            'pro.dashboard' => 'access_pro_portal',

            // ── Common Staff Routes ──────────────────────────────────────────
            'staff.admission.details' => 'manage_admission',

            // ── Agents ───────────────────────────────────────────────────────
            'admin.agent.applicants' => 'manage_agents',
            'admin.agent.application.update_status' => 'manage_agents',
        ];

        foreach ($mappings as $route => $permission) {
            RoutePermission::updateOrCreate(
                ['route_name' => $route],
                ['permission_identifier' => $permission]
            );
        }

        // Bust the middleware cache so changes take effect immediately
        Cache::forget('route_permissions_map');
    }
}

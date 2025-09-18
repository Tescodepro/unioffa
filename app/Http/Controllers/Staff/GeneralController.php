<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\UserApplications;
use Illuminate\Http\Request;
use App\Models\Campus;
use App\Models\User;
use App\Models\AdmissionList;
use App\Exports\AppplicantsExport;
use Maatwebsite\Excel\Facades\Excel;

class GeneralController extends Controller
{
    public function index_admin(Request $request)
    {    
        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');

        $selectedSession = $request->get('academic_session', $sessions->first());

        // Get all campuses & application types for filters
        $campuses = Campus::all();
        $applicationTypes = ApplicationSetting::all();

        // Read selected filters
        $selectedCampusId = $request->get('campus_id');
        $selectedApplicationId = $request->get('application_id');

        // Applicants per campus (with count)
        $campusApplicants = Campus::withCount([
            'users as applicant_count' => function ($q) {
                $q->whereHas('userType', fn($q2) => $q2->where('name', 'applicant'))
                ->whereHas('applications', fn($q3) => $q3->whereNotNull('submitted_by'));
            }
        ])->get();

        // Applicants per application type
        $applicationApplicants = ApplicationSetting::withCount(['userApplications as applicant_count' => function ($q) use ($selectedSession) {
            $q->where('academic_session', $selectedSession)
            ->whereNotNull('submitted_by');
        }])->get();

        // Admitted + not admitted stats
        $admittedCount = AdmissionList::where('admission_status', 'admitted')->count();
        $notAdmittedCount = AdmissionList::where('admission_status', '!=', 'admitted')
            ->orWhereNull('admission_status')
            ->count();

        // Query students with filters
        $students = User::whereHas('userType', fn($q) => $q->where('name', 'applicant'))
                ->with([
                    'applications.applicationSetting',
                    'transactions',
                    'admissionList',
                    'courseOfStudy.firstDepartment',   // <-- Add this
                    'courseOfStudy.secondDepartment',  // <-- And this
                ])
                ->when($selectedCampusId, fn($q) => $q->where('campus_id', $selectedCampusId))
                ->when($selectedApplicationId, function ($q) use ($selectedApplicationId) {
                    $q->whereHas('applications', fn($qa) => $qa->where('application_setting_id', $selectedApplicationId));
                })
                ->get()
                ->map(function ($user) {
                    return (object)[
                        'id' => $user->id,
                        'registration_no' => $user->registration_no,
                        'full_name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'application_type' => optional($user->applications->first()?->applicationSetting)->name,
                        'application_modules_enable' => optional($user->applications->first()?->applicationSetting)->modules_enable,
                        'application_id' => $user->applications->first()?->id,
                        'application_status' => $user->applications->first()?->submitted_by ? 'submitted' : 'not submitted',
                        'payment_status' => $user->transactions->where('payment_type', 'application')->first()->payment_status ?? 'unpaid',
                        'payment_ref' => $user->transactions->where('payment_type', 'application')->first()->refernce_number ?? null,
                        'admissionList' => $user->admissionList,
                        'first_choice' => $user->courseOfStudy?->firstDepartment?->department_name,
                        'second_choice' => $user->courseOfStudy?->secondDepartment?->department_name,
                    ];
                });

        $departments = Department::all();
        $faculties = Faculty::all();

        return view('staff.admin_dashboard', compact(
            'sessions',
            'selectedSession',
            'campuses',
            'applicationTypes',
            'applicationApplicants',
            'campusApplicants',
            'admittedCount',
            'notAdmittedCount',
            'students',
            'selectedCampusId',
            'selectedApplicationId',
            'departments',
            'faculties',
        ));
    }

    public function admitStudent($userId, Request $request)
    {
        $user_application_id = $request->application_id;
        $user_application = UserApplications::findOrFail($user_application_id);
        $user_application->is_approved = 1;
        $user_application->save();

        // Get or create admission record
        $admission = AdmissionList::firstOrNew(['user_id' => $userId]);
        $admission->admission_status = 'admitted';
        $admission->approved_department_id = $request->final_course; // optional, if you want to track
        $admission->save();

        return back()->with('success', 'Student admitted successfully.');
    }

    public function showApplicantDetails($userId, $applicationId)
    {
        $application = UserApplications::with([
            'applicationSetting',
            'profile',
            'olevels',
            'jambDetail',
            'documents',
            'educationHistories',
            'user.courseOfStudy.firstDepartment',
            'user.courseOfStudy.secondDepartment',
        ])
        ->where('id', $applicationId)
        ->where('user_id', $userId)
        ->firstOrFail();

        $modules = json_decode($application->applicationSetting->modules_enable, true);

        return view('staff.applicant_details', compact('application', 'modules'));
    }



}


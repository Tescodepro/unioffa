<?php

namespace App\Http\Controllers\Staff\CenterDirector;

use App\Http\Controllers\Controller;
use App\Models\AdmissionList;
use App\Models\ApplicationSetting;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\UserApplications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CenterDirectorController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $campusId = Auth::user()->campus_id;

        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');
        $latestSession = $sessions->first();

        $totalApplicants = User::whereHas('userType', fn($q) => $q->where('name', 'applicant'))
            ->where('campus_id', $campusId)
            ->whereHas('applications', fn($q) => $q
                ->where('academic_session', $latestSession)
                ->whereNotNull('submitted_by'))
            ->count();

        $totalAdmitted = AdmissionList::where('admission_status', 'admitted')
            ->where('session_admitted', $latestSession)
            ->whereHas('user', fn($q) => $q->where('campus_id', $campusId))
            ->count();

        $totalPending = max(0, $totalApplicants - $totalAdmitted);

        $campus = Auth::user()->campus;

        return view('staff.center-director.dashboard', compact(
            'sessions',
            'latestSession',
            'totalApplicants',
            'totalAdmitted',
            'totalPending',
            'campus'
        ));
    }

    // ─── Admission: Applicants (read-only, campus-locked) ────────────────────

    public function admissionApplicants(Request $request)
    {
        // The campus is always locked to the authenticated center-director's campus
        $campusId = Auth::user()->campus_id;

        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');
        $selectedSession = $request->get('academic_session', $sessions->first());

        $entryModes = ApplicationSetting::where('academic_session', $selectedSession)->get();
        $faculties = Faculty::with('departments')->get();
        $departments = Department::with('faculty')->get();

        $selectedEntryModeId = $request->get('entry_mode_id');
        $selectedDeptId = $request->get('department_id');
        $selectedStatus = $request->get('status');

        $campus = Auth::user()->campus;

        $applicants = User::whereHas('userType', fn($q) => $q->where('name', 'applicant'))
            ->where('campus_id', $campusId)   // ← always locked to this campus
            ->with([
                'applications.applicationSetting',
                'admissionList',
                'campus',
                'department',
                'courseOfStudy.firstDepartment',
                'courseOfStudy.secondDepartment',
            ])
            ->when($selectedEntryModeId, fn($q) => $q->whereHas(
                'applications',
                fn($q2) => $q2->where('application_setting_id', $selectedEntryModeId)
                    ->where('academic_session', $selectedSession)
            ))
            ->when($selectedDeptId, fn($q) => $q->whereHas(
                'courseOfStudy',
                fn($q2) => $q2->where('first_department_id', $selectedDeptId)
                    ->orWhere('second_department_id', $selectedDeptId)
            ))
            ->when($selectedStatus === 'admitted', fn($q) => $q->whereHas(
                'admissionList',
                fn($q2) => $q2->where('admission_status', 'admitted')
            ))
            ->when($selectedStatus === 'pending', fn($q) => $q->whereDoesntHave('admissionList'))
            ->whereHas('applications', fn($q) => $q
                ->where('academic_session', $selectedSession)
                ->whereNotNull('submitted_by'))
            ->get();

        return view('staff.center-director.admission.applicants', compact(
            'sessions',
            'selectedSession',
            'campus',
            'entryModes',
            'faculties',
            'departments',
            'applicants',
            'selectedEntryModeId',
            'selectedDeptId',
            'selectedStatus'
        ));
    }
}

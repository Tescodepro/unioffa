<?php

namespace App\Http\Controllers\Staff\CenterDirector;

use App\Http\Controllers\Controller;
use App\Models\AdmissionList;
use App\Models\ApplicationSetting;
use App\Models\Campus;
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

        $totalApplicants = User::whereHas('userType', fn ($q) => $q->where('name', 'applicant'))
            ->where('campus_id', $campusId)
            ->whereHas('applications', fn ($q) => $q
                ->where('academic_session', $latestSession)
                ->whereNotNull('submitted_by'))
            ->count();

        $totalAdmitted = AdmissionList::where('admission_status', 'admitted')
            ->where('session_admitted', $latestSession)
            ->whereHas('user', fn ($q) => $q->where('campus_id', $campusId))
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
        $user = Auth::user();
        $isProgDir = $user->hasRole('programme-director');
        $isCenterDir = $user->hasRole('center-director');

        $assignedTypeIds = $isProgDir
            ? $user->assignedApplicationTypes()->pluck('application_settings.id')->toArray()
            : [];

        // Center Director is locked to their campus
        $selectedCampusId = $isCenterDir ? $user->campus_id : $request->get('campus_id');

        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');
        $selectedSession = $request->get('academic_session', $sessions->first());

        // Entry modes (Application Settings) - restrict for PD
        $entryModesQuery = ApplicationSetting::where('academic_session', $selectedSession);
        if ($isProgDir) {
            $entryModesQuery->whereIn('id', $assignedTypeIds);
        }
        $entryModes = $entryModesQuery->get();

        $faculties = Faculty::with('departments')->get();
        $departments = Department::with('faculty')->get();
        $campuses = Campus::all();

        $selectedEntryModeId = $request->get('entry_mode_id');
        $selectedDeptId = $request->get('department_id');
        $selectedStatus = $request->get('status');

        $campus = $isCenterDir ? $user->campus : ($selectedCampusId ? Campus::find($selectedCampusId) : null);

        $applicants = User::whereHas('userType', fn ($q) => $q->whereIn('name', ['applicant', 'student']))
            ->with([
                'applications' => fn ($q) => $q->where('academic_session', $selectedSession),
                'applications.applicationSetting',
                'admissionList',
                'campus',
                'department',
                'courseOfStudy.firstDepartment',
                'courseOfStudy.secondDepartment',
            ])
            ->when($selectedCampusId, fn ($q) => $q->where('campus_id', $selectedCampusId))
            ->whereHas('applications', function ($q) use ($selectedSession, $isProgDir, $isCenterDir, $assignedTypeIds, $selectedEntryModeId) {
                $q->where('academic_session', $selectedSession);

                // If specialized entry mode selected, use it
                if ($selectedEntryModeId) {
                    $q->where('application_setting_id', $selectedEntryModeId);
                } elseif ($isProgDir && ! $isCenterDir) {
                    // Otherwise, if PD, restrict to their assigned types
                    $q->whereIn('application_setting_id', $assignedTypeIds);
                }
            })
            ->when($selectedDeptId, fn ($q) => $q->whereHas(
                'courseOfStudy',
                fn ($q2) => $q2->where('first_department_id', $selectedDeptId)
                    ->orWhere('second_department_id', $selectedDeptId)
            ))
            ->when($selectedStatus === 'admitted', fn ($q) => $q->whereHas(
                'admissionList',
                fn ($q2) => $q2->where('admission_status', 'admitted')
            ))
            ->when($selectedStatus === 'pending', fn ($q) => $q->whereDoesntHave('admissionList'))
            ->get();

        return view('staff.center-director.admission.applicants', compact(
            'sessions',
            'selectedSession',
            'campus',
            'campuses',
            'entryModes',
            'faculties',
            'departments',
            'applicants',
            'selectedEntryModeId',
            'selectedDeptId',
            'selectedStatus',
            'selectedCampusId',
            'isProgDir',
            'isCenterDir'
        ));
    }
}

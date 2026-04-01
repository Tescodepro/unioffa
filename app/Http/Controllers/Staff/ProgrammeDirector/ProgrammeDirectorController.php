<?php

namespace App\Http\Controllers\Staff\ProgrammeDirector;

use App\Http\Controllers\Controller;
use App\Models\AdmissionList;
use App\Models\UserApplications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgrammeDirectorController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $assignedTypeIds = $user->assignedApplicationTypes()->pluck('application_settings.id')->toArray();

        // If not assigned to any types, they see nothing
        if (empty($assignedTypeIds)) {
            $sessions = collect([]);
            $latestSession = null;
            $totalApplicants = 0;
            $totalAdmitted = 0;
            $totalPending = 0;
        } else {
            // Get sessions matching their assigned types
            $sessions = UserApplications::whereIn('application_setting_id', $assignedTypeIds)
                ->select('academic_session')
                ->distinct()
                ->pluck('academic_session');

            $latestSession = $request->session ?? $sessions->first();

            $totalApplicants = UserApplications::whereIn('application_setting_id', $assignedTypeIds)
                ->where('academic_session', $latestSession)
                ->whereNotNull('submitted_by')
                ->count();

            // Admitted count: join with user_applications to filter by assigned type
            $totalAdmitted = AdmissionList::whereHas('user.userApplications', function ($q) use ($latestSession, $assignedTypeIds) {
                $q->where('academic_session', $latestSession)
                    ->whereIn('application_setting_id', $assignedTypeIds);
            })
                ->where('session_admitted', $latestSession)
                ->where('admission_status', 'admitted')
                ->count();

            $totalPending = max(0, $totalApplicants - $totalAdmitted);
        }

        return view('staff.programme-director.dashboard', compact(
            'sessions',
            'latestSession',
            'totalApplicants',
            'totalAdmitted',
            'totalPending'
        ));
    }
}

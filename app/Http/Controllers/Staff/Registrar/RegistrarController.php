<?php

namespace App\Http\Controllers\Staff\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AdmissionList;
use App\Models\ApplicationSetting;
use App\Models\Campus;
use App\Models\User;
use App\Models\UserApplications;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');
        $latestSession = $sessions->first();

        $totalApplicants = User::whereHas('userType', fn($q) => $q->where('name', 'applicant'))
            ->whereHas('applications', fn($q) => $q->where('academic_session', $latestSession)
                ->whereNotNull('submitted_by'))->count();

        $totalAdmitted = AdmissionList::where('admission_status', 'admitted')
            ->where('session_admitted', $latestSession)->count();

        $totalPending = max(0, $totalApplicants - $totalAdmitted);
        $totalCentres = Campus::count();
        $totalEntryModes = ApplicationSetting::where('academic_session', $latestSession)->count();
        $pendingAdmissions = $totalPending;

        return view('staff.registrar.dashboard', compact(
            'sessions',
            'latestSession',
            'totalApplicants',
            'totalAdmitted',
            'totalPending',
            'totalCentres',
            'totalEntryModes',
            'pendingAdmissions'
        ));
    }
}

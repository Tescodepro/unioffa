<?php

namespace App\Http\Controllers\Staff\Vc;

use App\Http\Controllers\Controller;
use App\Models\AdmissionList;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\UserApplications;

class VcController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');
        $latestSession = $sessions->contains('2026/2027') ? '2026/2027' : $sessions->first();

        // ── Institutional Overview ─────────────────────────────────────────────
        $totalStudents = Student::count();
        $totalStaff = Staff::count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();

        // ── Admission Snapshot (current session) ──────────────────────────────
        $totalApplicants = UserApplications::where('academic_session', $latestSession)
            ->whereNotNull('submitted_by')
            ->distinct('user_id')
            ->count('user_id');

        $totalAdmitted = AdmissionList::where('admission_status', 'admitted')
            ->where('session_admitted', $latestSession)->count();

        $totalPending = max(0, $totalApplicants - $totalAdmitted);

        // ── Finance Snapshot (current session) ────────────────────────────────
        $totalRevenue = Transaction::where('payment_status', '1')
            ->where('session', $latestSession)
            ->sum('amount');

        return view('staff.vc.dashboard', compact(
            'sessions',
            'latestSession',
            'totalStudents',
            'totalStaff',
            'totalFaculties',
            'totalDepartments',
            'totalApplicants',
            'totalAdmitted',
            'totalPending',
            'totalRevenue',
        ));
    }

    // ─── Summer Requests ────────────────────────────────────────────────────────────

    public function summerRequests()
    {
        $requests = \App\Models\SummerRegistration::with(['student.student'])
            ->where('status', 'pending_vc_approval')
            ->get();

        return view('staff.vc.summer-requests', compact('requests'));
    }

    public function approveSummerRequest($id)
    {
        $registration = \App\Models\SummerRegistration::findOrFail($id);
        $registration->update(['status' => 'pending_payment']);

        return redirect()->back()->with('success', 'Summer registration request approved successfully. Student can now proceed to payment.');
    }

    public function rejectSummerRequest($id)
    {
        $registration = \App\Models\SummerRegistration::findOrFail($id);
        $registration->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Summer registration request rejected successfully.');
    }
}

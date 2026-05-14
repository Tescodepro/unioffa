<?php

namespace App\Http\Controllers\Staff\Bursary;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\Request;

class DebtReportController extends Controller
{
    /**
     * Display a listing of students with outstanding debts.
     */
    public function index(Request $request)
    {
        $departments = Department::orderBy('department_name')->get();
        $campuses = Campus::orderBy('name')->get();

        $query = Student::with(['user', 'department'])
            ->where('status', '!=', Student::STATUS_INACTIVE);

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }
        if ($request->campus_id) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->matric_no) {
            $query->where('matric_no', 'like', "%{$request->matric_no}%");
        }

        // We need to filter those with debt. Since getOutstandingDebt() is a method,
        // we can't easily do it in SQL without complex joins.
        // For now, we'll fetch and filter in PHP (limited to 500 records for performance).
        $allStudents = $query->limit(500)->get();

        $debtors = $allStudents->map(function ($student) {
            $debt = $student->getOutstandingDebt();
            if ($debt > 0) {
                return [
                    'student' => $student,
                    'debt_amount' => $debt,
                ];
            }
            return null;
        })->filter()->sortByDesc('debt_amount');

        return view('staff.bursary.reports.debt_report', compact('debtors', 'departments', 'campuses'));
    }
}

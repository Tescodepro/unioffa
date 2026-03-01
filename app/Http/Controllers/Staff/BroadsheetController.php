<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\AcademicSession;
use App\Models\AcademicSemester;
use App\Services\BroadsheetService;
use Illuminate\Support\Facades\Auth;

class BroadsheetController extends Controller
{
    protected $broadsheetService;

    public function __construct(BroadsheetService $broadsheetService)
    {
        $this->broadsheetService = $broadsheetService;
    }

    /**
     * Sessional Broadsheet — shows form and report below on same page.
     */
    public function indexSessional(Request $request)
    {
        return $this->renderPage($request, 'sessional');
    }

    /**
     * Semester Result — shows form and report below on same page.
     */
    public function indexSemester(Request $request)
    {
        return $this->renderPage($request, 'semester');
    }

    /**
     * Shared page logic: load form dropdowns, optionally generate the report
     * if filter query params are present in the GET request.
     */
    private function renderPage(Request $request, string $type)
    {
        $user = Auth::user();
        $departments = collect();

        if ($user->hasUserType('dean')) {
            if ($user->staff && $user->staff->faculty_id) {
                $departments = Department::where('faculty_id', $user->staff->faculty_id)->get();
            } else {
                session()->flash('error', 'Faculty not assigned to this Dean profile.');
            }
        } elseif ($user->hasUserType('hod')) {
            if ($user->staff && $user->staff->department_id) {
                $departments = Department::where('id', $user->staff->department_id)->get();
            } else {
                session()->flash('error', 'Department not assigned to this HOD profile.');
            }
        } elseif ($user->hasUserType('vice-chancellor')) {
            $departments = Department::all();
        } elseif ($user->hasUserType('registrar')) {
            $departments = Department::all();
        } elseif ($user->hasUserType('ict')) {
            $departments = Department::all();
        }

        $sessions = AcademicSession::orderBy('name', 'DESC')->get();
        $semesters = AcademicSemester::all();

        // Defaults — no report yet
        $broadsheetData = null;
        $department = null;
        $session = null;
        $semester = null;
        $level = null;
        $studentsData = null;
        $stats = null;

        // If the user has submitted the form, generate the report here
        if ($request->filled('department_id') && $request->filled('session_id') && $request->filled('level')) {

            // Security: HOD cannot request another department
            if ($user->hasUserType('hod')) {
                if (!$user->staff || $user->staff->department_id !== $request->department_id) {
                    session()->flash('error', 'Unauthorized access to this department.');
                    return view('staff.broadsheet.index', compact('departments', 'sessions', 'semesters', 'type'));
                }
            }

            // Security: Dean cannot request a department outside their faculty
            if ($user->hasUserType('dean')) {
                $dept = Department::find($request->department_id);
                if (!$dept || !$user->staff || $dept->faculty_id !== $user->staff->faculty_id) {
                    session()->flash('error', 'Unauthorized access to this department.');
                    return view('staff.broadsheet.index', compact('departments', 'sessions', 'semesters', 'type'));
                }
            }

            $semesterId = ($type === 'semester') ? $request->semester_id : null;
            // dd($semesterId);

            $data = $this->broadsheetService->generateBroadsheet(
                $request->department_id,
                $request->session_id,
                $request->level,
                $semesterId
            );

            $department = $data['department'];
            $session = $data['session'];
            $semester = $data['semester'];
            $level = $data['level'];
            $studentsData = $data['students_data'];
            $stats = $data['stats'];
        }

        return view('staff.broadsheet.index', compact(
            'departments',
            'sessions',
            'semesters',
            'type',
            'department',
            'session',
            'semester',
            'level',
            'studentsData',
            'stats'
        ));
    }
}

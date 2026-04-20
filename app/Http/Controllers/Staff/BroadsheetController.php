<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Services\BroadsheetService;
use Illuminate\Http\Request;
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

        if ($user->hasRole('dean')) {
            if ($user->staff && $user->staff->faculty_id) {
                $departments = Department::where('faculty_id', $user->staff->faculty_id)->get();
            } else {
                session()->flash('error', 'Faculty not assigned to this Dean profile.');
            }
        } elseif ($user->hasRole('hod')) {
            if ($user->staff && $user->staff->department_id) {
                $departments = Department::where('id', $user->staff->department_id)->get();
            } else {
                session()->flash('error', 'Department not assigned to this HOD profile.');
            }
        } elseif ($user->hasRole('vice-chancellor')) {
            $departments = Department::all();
        } elseif ($user->hasRole('registrar')) {
            $departments = Department::all();
        } elseif ($user->hasRole('ict')) {
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
        $studentsData = [];
        $stats = null;
        $course_codes = [];
        $courses_info = [];

        // If the user has submitted the form, generate the report here
        if ($request->filled('department_id') && $request->filled('session_id') && $request->filled('level')) {

            // Security: HOD cannot request another department
            if ($user->hasRole('hod')) {
                if (! $user->staff || $user->staff->department_id !== $request->department_id) {
                    session()->flash('error', 'Unauthorized access to this department.');

                    return view('staff.broadsheet.index', compact('departments', 'sessions', 'semesters', 'type'));
                }
            }

            // Security: Dean cannot request a department outside their faculty
            if ($user->hasRole('dean')) {
                $dept = Department::find($request->department_id);
                if (! $dept || ! $user->staff || $dept->faculty_id !== $user->staff->faculty_id) {
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
            $course_codes = $data['course_codes'];
            $courses_info = $data['courses_info'];
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
            'stats',
            'course_codes',
            'courses_info'
        ));
    }

    /**
     * Print the Official Standard Broadsheet.
     */
    public function printOfficial(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'session_id' => 'required',
            'level' => 'required',
        ]);

        $type = $request->input('type', 'sessional');
        $semesterId = ($type === 'semester') ? $request->input('semester_id') : null;

        $data = $this->broadsheetService->generateBroadsheet(
            $request->department_id,
            $request->session_id,
            $request->level,
            $semesterId
        );

        return view('staff.broadsheet.print-official', $data);
    }
}

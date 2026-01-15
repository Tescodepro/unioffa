<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Department;
use App\Models\Campus;
use App\Models\AcademicSession;
use App\Exports\AdmittedStudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmittedStudentsDownloadController extends Controller
{
    /**
     * Show the list of admitted students with filters
     */
    public function index(Request $request)
    {
        // Check authorization
        $user = Auth::user();
        if (!$user->hasAnyRole(['vice-chancellor', 'ict', 'registrar', 'administrator'])) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Fetch filter options
        $sessions = AcademicSession::select('name')->distinct()->orderBy('name', 'desc')->pluck('name');
        $departments = Department::select('id', 'department_name')->orderBy('department_name')->get();
        $campuses = Campus::select('id', 'name')->orderBy('name')->get();
        $entryModes = Student::select('entry_mode')->distinct()->whereNotNull('entry_mode')->pluck('entry_mode');

        // Build query with filters
        $query = Student::with(['user:id,first_name,middle_name,last_name,email', 'department:id,department_name,department_code', 'campus:id,name']);

        // Apply filters from request
        if ($request->filled('session')) {
            $query->where('admission_session', $request->input('session'));
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('entry_mode')) {
            $query->where('entry_mode', $request->entry_mode);
        }

        // Get total count before pagination
        $totalFiltered = $query->count();

        // Get students with pagination
        $students = $query->orderBy('admission_session', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString(); // Keep filter params in pagination links

        // Get total count of all students (unfiltered)
        $totalStudents = Student::count();

        // Check if any filters are applied
        $hasFilters = $request->filled('session') || $request->filled('department_id') ||
            $request->filled('campus_id') || $request->filled('entry_mode');

        return view('application.admitted-students-download', [
            'sessions' => $sessions,
            'departments' => $departments,
            'campuses' => $campuses,
            'entryModes' => $entryModes,
            'students' => $students,
            'totalFiltered' => $totalFiltered,
            'totalStudents' => $totalStudents,
            'hasFilters' => $hasFilters,
            'filters' => $request->only(['session', 'department_id', 'campus_id', 'entry_mode']),
        ]);
    }

    /**
     * Download the admitted students list
     */
    public function download(Request $request)
    {
        // Check authorization
        $user = Auth::user();
        if (!$user->hasAnyRole(['vice-chancellor', 'ict', 'registrar', 'administrator'])) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Validate filters
        $validated = $request->validate([
            'session' => 'nullable|string',
            'department_id' => 'nullable|uuid|exists:departments,id',
            'campus_id' => 'nullable|uuid|exists:campuses,id',
            'entry_mode' => 'nullable|string',
        ]);

        // Build query
        $query = Student::with('user:id,first_name,middle_name,last_name,email', 'department:id,department_name,department_code', 'campus:id,name');

        // Apply filters
        if ($request->filled('session')) {
            $query->where('admission_session', $validated['session']);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $validated['department_id']);
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $validated['campus_id']);
        }

        if ($request->filled('entry_mode')) {
            $query->where('entry_mode', $validated['entry_mode']);
        }

        $students = $query->orderBy('admission_session', 'desc')
            ->orderBy('user_id')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('warning', 'No students found matching the selected filters');
        }

        // Generate filename with filters
        $filterNames = [];
        if ($request->filled('session')) {
            $filterNames[] = 'Session-' . str_replace('/', '-', $validated['session']);
        }
        if ($request->filled('department_id')) {
            $dept = Department::find($validated['department_id']);
            $filterNames[] = 'Department-' . ($dept ? str_replace(' ', '-', $dept->department_name) : '');
        }
        if ($request->filled('entry_mode')) {
            $filterNames[] = 'EntryMode-' . $validated['entry_mode'];
        }

        $filename = 'AdmittedStudents_' . (count($filterNames) > 0 ? implode('_', $filterNames) : 'all') . '_' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new AdmittedStudentsExport($students, $validated),
            $filename
        );
    }
}

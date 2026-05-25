<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentLevelManagementController extends Controller
{
    /**
     * Display the student level management page.
     */
    public function index(Request $request)
    {
        $departments = Department::with('faculty')->orderBy('department_name')->get();
        $campuses = Campus::orderBy('name')->get();
        $entryModes = EntryMode::orderBy('name')->get();

        // Get unique admission sessions and programmes from students table for filters
        $admissionSessions = Student::select('admission_session')
            ->whereNotNull('admission_session')
            ->distinct()
            ->orderBy('admission_session', 'desc')
            ->pluck('admission_session');

        $programmes = Student::select('programme')
            ->whereNotNull('programme')
            ->distinct()
            ->orderBy('programme')
            ->pluck('programme');

        // If searching for a specific student
        $specificStudent = null;
        if ($request->has('search_matric')) {
            $specificStudent = Student::with('user', 'department')
                ->where('matric_no', $request->search_matric)
                ->first();
        }

        return view('staff.ict.students.change_level', compact(
            'departments',
            'campuses',
            'entryModes',
            'admissionSessions',
            'programmes',
            'specificStudent'
        ));
    }

    /**
     * Update level for a specific student.
     */
    public function updateSpecific(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'new_level' => 'required|integer|in:100,200,300,400,500,600,700',
        ]);

        $student = Student::findOrFail($request->student_id);
        $oldLevel = $student->level;
        $student->update(['level' => $request->new_level]);

        return back()->with('success', "Level updated for student {$student->matric_no} from {$oldLevel} to {$request->new_level}.");
    }

    /**
     * Update levels in bulk based on categories.
     */
    public function updateBulk(Request $request)
    {
        $request->validate([
            'target_level' => 'required|integer|in:100,200,300,400,500,600,700',
            'new_level' => 'required|integer|in:100,200,300,400,500,600,700',
            'department_id' => 'nullable|exists:departments,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'admission_session' => 'nullable|string',
            'programme' => 'nullable|string',
            'entry_mode' => 'nullable|string|exists:entry_modes,code',
        ]);

        $query = Student::query()->where('level', $request->target_level);

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->campus_id) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->admission_session) {
            $query->where('admission_session', $request->admission_session);
        }
        if ($request->programme) {
            $query->where('programme', $request->programme);
        }
        if ($request->entry_mode) {
            $query->where('entry_mode', $request->entry_mode);
        }

        $count = $query->count();

        if ($count === 0) {
            return back()->with('error', 'No students found matching the selected criteria.');
        }

        $query->update(['level' => $request->new_level]);

        return back()->with('success', "Successfully updated level from {$request->target_level} to {$request->new_level} for {$count} student(s).");
    }
}

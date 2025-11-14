<?php

namespace App\Http\Controllers\Staff\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index_course()
    {
        $courses = Course::with('department')->orderBy('course_code')->get();
        $departments = Department::all();

        return view('staff.courses.index', compact('courses', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses,course_code',
            'course_unit' => 'required|integer|min:1|max:6',
            'course_status' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'other_departments' => 'nullable|array',
            'other_departments.*' => 'exists:departments,id',
            'level' => 'required|integer',
            'semester' => 'required|in:1st,2nd,3rd,4th,5th,6th',
            'active_for_register' => 'required|boolean',
        ]);

        $course = new Course;
        $course->id = Str::uuid()->toString();
        $course->course_title = $validated['course_title'];
        $course->course_code = $validated['course_code'];
        $course->course_unit = $validated['course_unit'];
        $course->course_status = $validated['course_status'] ?? null;
        $course->department_id = $validated['department_id'];

        // Ensure it's always a clean array for JSON storage
        $course->other_departments = $validated['other_departments'] ?? [];

        $course->level = $validated['level'];
        $course->semester = $validated['semester'];
        $course->active_for_register = $validated['active_for_register'];

        $course->save();

        return redirect()->back()->with('success', 'Course added successfully.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses,course_code,'.$id,
            'course_unit' => 'required|integer|min:1|max:6',
            'course_status' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'other_departments' => 'nullable|array',
            'level' => 'required|integer',
            'semester' => 'required|string',
            'active_for_register' => 'required|boolean',
        ]);

        $validated['other_departments'] = $request->other_departments ?? [];

        $course->update($validated);

        return redirect()->back()->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->back()->with('success', 'Course deleted successfully.');
    }
}

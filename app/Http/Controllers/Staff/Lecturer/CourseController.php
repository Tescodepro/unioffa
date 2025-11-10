<?php

namespace App\Http\Controllers\Staff\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
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
            'level' => 'required|integer',
            'semester' => 'required|string',
            'active_for_register' => 'required|boolean',
        ]);

        $validated['id'] = (string) Str::uuid();
        $validated['other_departments'] = $request->other_departments ?? [];

        Course::create($validated);

        return redirect()->back()->with('success', 'Course added successfully.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_title' => 'required|string|max:255',
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $id,
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

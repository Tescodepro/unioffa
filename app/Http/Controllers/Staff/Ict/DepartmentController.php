<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['faculty', 'students'])->orderBy('department_name')->get();
        $faculties = Faculty::orderBy('faculty_name')->get();

        return view('staff.ict.academic-setup.departments', compact('departments', 'faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'department_name' => 'required|string|max:255|unique:departments',
            'department_code' => 'required|string|max:50|unique:departments',
            'qualification' => 'required|string|max:50',
            'department_description' => 'nullable|string',
        ]);

        Department::create($validated);

        return back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'department_name' => 'required|string|max:255|unique:departments,department_name,'.$department->id,
            'department_code' => 'required|string|max:50|unique:departments,department_code,'.$department->id,
            'qualification' => 'required|string|max:50',
            'department_description' => 'nullable|string',
        ]);

        $department->update($validated);

        return back()->with('success', 'Department updated successfully.');
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);

        if ($department->students()->exists()) {
            return back()->with('error', 'Cannot delete a department that has associated students.');
        }

        if ($department->paymentSettings()->exists()) {
            return back()->with('error', 'Cannot delete a department that has associated payment configurations.');
        }

        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }
}

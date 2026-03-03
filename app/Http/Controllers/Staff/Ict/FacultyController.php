<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faculty;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::with(['departments', 'students'])->orderBy('faculty_name')->get();
        return view('staff.ict.academic-setup.faculties', compact('faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_name' => 'required|string|max:255|unique:faculties',
            'faculty_code' => 'required|string|max:50|unique:faculties',
            'description' => 'nullable|string',
        ]);

        Faculty::create($validated);

        return back()->with('success', 'Faculty created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $faculty = Faculty::findOrFail($id);

        $validated = $request->validate([
            'faculty_name' => 'required|string|max:255|unique:faculties,faculty_name,' . $faculty->id,
            'faculty_code' => 'required|string|max:50|unique:faculties,faculty_code,' . $faculty->id,
            'description' => 'nullable|string',
        ]);

        $faculty->update($validated);

        return back()->with('success', 'Faculty updated successfully.');
    }

    public function destroy(string $id)
    {
        $faculty = Faculty::findOrFail($id);

        if ($faculty->departments()->exists()) {
            return back()->with('error', 'Cannot delete a faculty that has associated departments.');
        }

        if ($faculty->students()->exists()) {
            return back()->with('error', 'Cannot delete a faculty that has associated students.');
        }

        $faculty->delete();

        return back()->with('success', 'Faculty deleted successfully.');
    }
}

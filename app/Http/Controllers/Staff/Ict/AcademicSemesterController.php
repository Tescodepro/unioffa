<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AcademicSemester;

class AcademicSemesterController extends Controller
{
    public function index()
    {
        $semesters = AcademicSemester::orderBy('name', 'asc')->get();
        return view('staff.ict.academic-setup.semesters', compact('semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_semesters',
            'code' => 'required|string|max:10|unique:academic_semesters',
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
        ]);

        if ($validated['status'] == '1') {
            AcademicSemester::where('status', '1')->update(['status' => '0']);
        }

        AcademicSemester::create($validated);

        return back()->with('success', 'Semester created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $semester = AcademicSemester::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_semesters,name,' . $semester->id,
            'code' => 'required|string|max:10|unique:academic_semesters,code,' . $semester->id,
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
        ]);

        if ($validated['status'] == '1' && $semester->status != '1') {
            AcademicSemester::where('id', '!=', $semester->id)
                ->where('status', '1')
                ->update(['status' => '0']);
        }

        $semester->update($validated);

        return back()->with('success', 'Semester updated successfully.');
    }

    public function destroy(string $id)
    {
        $semester = AcademicSemester::findOrFail($id);

        if ($semester->status == '1') {
            return back()->with('error', 'Cannot delete the currently active semester.');
        }

        $semester->delete();

        return back()->with('success', 'Semester deleted successfully.');
    }
}

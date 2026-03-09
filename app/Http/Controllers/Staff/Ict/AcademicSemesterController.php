<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use Illuminate\Http\Request;

class AcademicSemesterController extends Controller
{
    public function index()
    {
        $semesters = AcademicSemester::orderBy('name', 'asc')->get();
        $campuses = \App\Models\Campus::all();

        return view('staff.ict.academic-setup.semesters', compact('semesters', 'campuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_semesters',
            'code' => 'required|string|max:10|unique:academic_semesters',
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
            'stream' => 'nullable|string|max:50',
            'campus_id' => 'nullable|uuid',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        if ($validated['status'] == '1') {
            // Unset active status only for semesters with the EXACT same scope
            $query = AcademicSemester::where('status', '1');

            if (! empty($validated['stream'])) {
                $query->where('stream', $validated['stream']);
            } else {
                $query->whereNull('stream');
            }

            if (! empty($validated['campus_id'])) {
                $query->where('campus_id', $validated['campus_id']);
            } else {
                $query->whereNull('campus_id');
            }

            $query->update(['status' => '0']);
        }

        AcademicSemester::create($validated);

        return back()->with('success', 'Semester created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $semester = AcademicSemester::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_semesters,name,'.$semester->id,
            'code' => 'required|string|max:10|unique:academic_semesters,code,'.$semester->id,
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
            'stream' => 'nullable|string|max:50',
            'campus_id' => 'nullable|uuid',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        if ($validated['status'] == '1' && $semester->status != '1') {
            $query = AcademicSemester::where('id', '!=', $semester->id)->where('status', '1');

            if (! empty($validated['stream'])) {
                $query->where('stream', $validated['stream']);
            } else {
                $query->whereNull('stream');
            }

            if (! empty($validated['campus_id'])) {
                $query->where('campus_id', $validated['campus_id']);
            } else {
                $query->whereNull('campus_id');
            }

            $query->update(['status' => '0']);
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

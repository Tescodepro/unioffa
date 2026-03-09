<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        $campuses = \App\Models\Campus::all();

        return view('staff.ict.academic-setup.sessions', compact('sessions', 'campuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions',
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
            'stream' => 'nullable|array',
            'stream.*' => 'string|max:50',
            'campus_id' => 'nullable|array',
            'campus_id.*' => 'uuid',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        if ($validated['status'] == '1') {
            // Unset active status only for sessions with the EXACT same scope
            $query = AcademicSession::where('status', '1');

            if (! empty($validated['stream'])) {
                $query->whereJsonContains('stream', $validated['stream']);
            } else {
                $query->whereNull('stream');
            }

            if (! empty($validated['campus_id'])) {
                $query->whereJsonContains('campus_id', $validated['campus_id']);
            } else {
                $query->whereNull('campus_id');
            }

            // Note: Exact JSON matching for students/lecturers is tricky and edge-casey.
            // Usually, these overrides are stream/campus level. If they are specific students,
            // we'll assume they don't broadly conflict unless they share the same scope context.

            $query->update(['status' => '0']);
        }

        AcademicSession::create($validated);

        return back()->with('success', 'Session created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $session = AcademicSession::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions,name,'.$session->id,
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
            'stream' => 'nullable|array',
            'stream.*' => 'string|max:50',
            'campus_id' => 'nullable|array',
            'campus_id.*' => 'uuid',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        if ($validated['status'] == '1' && $session->status != '1') {
            $query = AcademicSession::where('id', '!=', $session->id)->where('status', '1');

            if (! empty($validated['stream'])) {
                $query->whereJsonContains('stream', $validated['stream']);
            } else {
                $query->whereNull('stream');
            }

            if (! empty($validated['campus_id'])) {
                $query->whereJsonContains('campus_id', $validated['campus_id']);
            } else {
                $query->whereNull('campus_id');
            }

            $query->update(['status' => '0']);
        }

        $session->update($validated);

        return back()->with('success', 'Session updated successfully.');
    }

    public function destroy(string $id)
    {
        $session = AcademicSession::findOrFail($id);

        if ($session->status == '1') {
            return back()->with('error', 'Cannot delete the currently active session.');
        }

        $session->delete();

        return back()->with('success', 'Session deleted successfully.');
    }
}

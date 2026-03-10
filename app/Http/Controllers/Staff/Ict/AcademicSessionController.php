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
        $entryModes = \App\Models\EntryMode::orderBy('name')->get();

        return view('staff.ict.academic-setup.sessions', compact('sessions', 'campuses', 'entryModes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions',
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
            'stream' => 'nullable|array',
            'stream.*' => 'string|max:10',
            'campus_id' => 'nullable|array',
            'campus_id.*' => 'uuid',
            'programme' => 'nullable|array',
            'programme.*' => 'string|max:100',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        // Browser omits empty multi-selects — force null so old values are cleared
        $validated['stream'] = $request->input('stream', []) ?: null;
        $validated['campus_id'] = $request->input('campus_id', []) ?: null;
        $validated['programme'] = $request->input('programme', []) ?: null;
        $validated['students_ids'] = $request->input('students_ids', []) ?: null;
        $validated['lecturar_ids'] = $request->input('lecturar_ids', []) ?: null;
        if ($validated['status'] == '1') {
            $query = AcademicSession::where('status', '1');

            if (! empty($validated['stream'])) {
                $query->whereJsonContains('stream', $validated['stream']);
            } else {
                $query->where(fn ($q) => $q->whereNull('stream')->orWhereJsonLength('stream', 0));
            }

            if (! empty($validated['campus_id'])) {
                $query->whereJsonContains('campus_id', $validated['campus_id']);
            } else {
                $query->where(fn ($q) => $q->whereNull('campus_id')->orWhere('campus_id', ''));
            }

            if (! empty($validated['programme'])) {
                $query->whereJsonContains('programme', $validated['programme']);
            } else {
                $query->where(fn ($q) => $q->whereNull('programme')->orWhereJsonLength('programme', 0));
            }

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
            'stream.*' => 'string|max:10',
            'campus_id' => 'nullable|array',
            'campus_id.*' => 'uuid',
            'programme' => 'nullable|array',
            'programme.*' => 'string|max:100',
            'students_ids' => 'nullable|array',
            'lecturar_ids' => 'nullable|array',
        ]);

        // Browser omits empty multi-selects — force null so old values are cleared
        $validated['stream'] = $request->input('stream', []) ?: null;
        $validated['campus_id'] = $request->input('campus_id', []) ?: null;
        $validated['programme'] = $request->input('programme', []) ?: null;
        $validated['students_ids'] = $request->input('students_ids', []) ?: null;
        $validated['lecturar_ids'] = $request->input('lecturar_ids', []) ?: null;
        if ($validated['status'] == '1' && $session->status != '1') {
            $query = AcademicSession::where('id', '!=', $session->id)->where('status', '1');

            if (! empty($validated['stream'])) {
                $query->whereJsonContains('stream', $validated['stream']);
            } else {
                $query->where(fn ($q) => $q->whereNull('stream')->orWhereJsonLength('stream', 0));
            }

            if (! empty($validated['campus_id'])) {
                $query->whereJsonContains('campus_id', $validated['campus_id']);
            } else {
                $query->where(fn ($q) => $q->whereNull('campus_id')->orWhere('campus_id', ''));
            }

            if (! empty($validated['programme'])) {
                $query->whereJsonContains('programme', $validated['programme']);
            } else {
                $query->where(fn ($q) => $q->whereNull('programme')->orWhereJsonLength('programme', 0));
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

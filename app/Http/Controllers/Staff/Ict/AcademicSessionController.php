<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AcademicSession;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        return view('staff.ict.academic-setup.sessions', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions',
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
        ]);

        if ($validated['status'] == '1') {
            AcademicSession::where('status', '1')->update(['status' => '0']);
        }

        AcademicSession::create($validated);

        return back()->with('success', 'Session created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $session = AcademicSession::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_sessions,name,' . $session->id,
            'status' => 'required|in:0,1',
            'status_upload_result' => 'required|in:0,1',
        ]);

        if ($validated['status'] == '1' && $session->status != '1') {
            AcademicSession::where('id', '!=', $session->id)
                ->where('status', '1')
                ->update(['status' => '0']);
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

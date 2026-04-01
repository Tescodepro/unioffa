<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\CourseRegistrationSetting;
use App\Models\EntryMode;
use Illuminate\Http\Request;

class CourseRegistrationSettingController extends Controller
{
    public function index()
    {
        $settings = CourseRegistrationSetting::with('campus')->orderBy('created_at', 'desc')->get();

        return view('staff.ict.course-registration-settings.index', compact('settings'));
    }

    public function create()
    {
        $campuses = Campus::all();
        $entryModes = EntryMode::all();
        $sessions = \App\Models\AcademicSession::all();
        $semesters = \App\Models\AcademicSemester::all();

        return view('staff.ict.course-registration-settings.create', compact('campuses', 'entryModes', 'sessions', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'entry_mode' => 'nullable|array',
            'semester' => 'nullable|string',
            'session' => 'nullable|string',
            'closing_date' => 'required|date',
            'late_registration_fee' => 'required|numeric|min:0',
        ]);

        CourseRegistrationSetting::create([
            'campus_id' => $request->campus_id,
            'entry_mode' => $request->entry_mode,
            'semester' => $request->semester,
            'session' => $request->session,
            'closing_date' => $request->closing_date,
            'late_registration_fee' => $request->late_registration_fee,
        ]);

        return redirect()->route('ict.course-registration-settings.index')->with('success', 'Course registration setting created successfully.');
    }

    public function edit(string $id)
    {
        $setting = CourseRegistrationSetting::findOrFail($id);
        $campuses = Campus::all();
        $entryModes = EntryMode::all();
        $sessions = \App\Models\AcademicSession::all();
        $semesters = \App\Models\AcademicSemester::all();

        return view('staff.ict.course-registration-settings.edit', compact('setting', 'campuses', 'entryModes', 'sessions', 'semesters'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'entry_mode' => 'nullable|array',
            'semester' => 'nullable|string',
            'session' => 'nullable|string',
            'closing_date' => 'required|date',
            'late_registration_fee' => 'required|numeric|min:0',
        ]);

        $setting = CourseRegistrationSetting::findOrFail($id);
        $setting->update([
            'campus_id' => $request->campus_id,
            'entry_mode' => $request->entry_mode,
            'semester' => $request->semester,
            'session' => $request->session,
            'closing_date' => $request->closing_date,
            'late_registration_fee' => $request->late_registration_fee,
        ]);

        return redirect()->route('ict.course-registration-settings.index')->with('success', 'Course registration setting updated successfully.');
    }

    public function destroy(string $id)
    {
        $setting = CourseRegistrationSetting::findOrFail($id);
        $setting->delete();

        return redirect()->route('ict.course-registration-settings.index')->with('success', 'Course registration setting deleted successfully.');
    }
}

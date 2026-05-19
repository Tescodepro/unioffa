<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = \App\Models\Student::where('user_id', $user->id)->first();

        if (! $student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        $jambDetail = \App\Models\JambDetail::where('user_id', $user->id)->first();
        $jambScore = $jambDetail ? $jambDetail->score : 0;

        // Find applicable scholarship settings
        $settings = \App\Models\ScholarshipSetting::where('is_active', true)
            ->where('academic_session', $student->admission_session)
            ->where(function ($query) use ($student) {
                $query->where('application_type', 'all')
                    ->orWhere('application_type', strtolower(str_replace(' ', '_', $student->entry_mode)));
            })
            ->where('min_jamb_score', '<=', $jambScore)
            ->get();

        // Check if student already applied
        $existingApplication = \App\Models\ScholarshipApplication::where('user_id', $user->id)->first();

        return view('students.scholarship.index', compact('student', 'jambScore', 'settings', 'existingApplication'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scholarship_setting_id' => 'required|exists:scholarship_settings,id',
            'requested_percentage' => 'required|integer|min:0|max:100',
            'form_data' => 'nullable|array',
        ]);

        $user = auth()->user();

        // Check if already applied
        if (\App\Models\ScholarshipApplication::where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You have already applied for a scholarship.');
        }

        \App\Models\ScholarshipApplication::create([
            'user_id' => $user->id,
            'scholarship_setting_id' => $request->scholarship_setting_id,
            'requested_percentage' => $request->requested_percentage,
            'form_data' => $request->form_data,
            'status' => 'pending',
        ]);

        return redirect()->route('student.scholarship.index')->with('success', 'Scholarship application submitted successfully.');
    }
}

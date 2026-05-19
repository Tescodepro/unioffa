<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScholarshipSettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\ScholarshipSetting::latest()->get();

        return view('staff.scholarship_settings.index', compact('settings'));
    }

    public function create()
    {
        return view('staff.scholarship_settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_session' => 'required|string',
            'application_type' => 'required|string',
            'min_jamb_score' => 'required|integer|min:0|max:400',
            'form_fields' => 'nullable|array',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|string|in:text,textarea,number',
            'is_active' => 'boolean',
        ]);

        \App\Models\ScholarshipSetting::create($request->all());

        return redirect()->route('scholarship-settings.index')->with('success', 'Scholarship setting created successfully.');
    }

    public function edit(\App\Models\ScholarshipSetting $scholarshipSetting)
    {
        return view('staff.scholarship_settings.edit', compact('scholarshipSetting'));
    }

    public function update(Request $request, \App\Models\ScholarshipSetting $scholarshipSetting)
    {
        $request->validate([
            'academic_session' => 'required|string',
            'application_type' => 'required|string',
            'min_jamb_score' => 'required|integer|min:0|max:400',
            'form_fields' => 'nullable|array',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|string|in:text,textarea,number',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['form_fields'] = $request->input('form_fields', []);

        $scholarshipSetting->update($data);

        return redirect()->route('scholarship-settings.index')->with('success', 'Scholarship setting updated successfully.');
    }

    public function destroy(\App\Models\ScholarshipSetting $scholarshipSetting)
    {
        $scholarshipSetting->delete();

        return redirect()->route('scholarship-settings.index')->with('success', 'Scholarship setting deleted successfully.');
    }

    public function applications()
    {
        $applications = \App\Models\ScholarshipApplication::with(['user', 'setting'])->latest()->get();

        return view('staff.scholarship_settings.applications', compact('applications'));
    }
}

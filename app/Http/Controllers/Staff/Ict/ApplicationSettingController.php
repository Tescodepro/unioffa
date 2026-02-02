<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use Illuminate\Http\Request;

class ApplicationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = ApplicationSetting::orderBy('name')->get();
        return view('staff.ict.application-settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.ict.application-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'application_code' => 'required|string|max:50|unique:application_settings,application_code',
            'academic_session' => 'required|string',
            'application_fee' => 'required|numeric|min:0',
            'acceptance_fee' => 'required|numeric|min:0',
            'admission_duration' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'enabled' => 'required|boolean',
            'modules_enable' => 'array',
            'modules_enable.documents' => 'nullable|array',
        ]);

        // Build modules array
        $modules = [
            'profile' => $request->has('modules_enable.profile'),
            'olevel' => $request->has('modules_enable.olevel'),
            'alevel' => $request->has('modules_enable.alevel'),
            'jamb_detail' => $request->has('modules_enable.jamb_detail'),
            'course_of_study' => $request->has('modules_enable.course_of_study'),
            'documents' => $request->input('modules_enable.documents', [])
        ];

        ApplicationSetting::create([
            'name' => $validated['name'],
            'application_code' => $validated['application_code'],
            'academic_session' => $validated['academic_session'],
            'application_fee' => $validated['application_fee'],
            'acceptance_fee' => $validated['acceptance_fee'],
            'admission_duration' => $validated['admission_duration'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'enabled' => $validated['enabled'],
            'modules_enable' => $modules
        ]);

        return redirect()->route('ict.application_settings.index')
            ->with('success', 'Application setting created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $setting = ApplicationSetting::findOrFail($id);

        if (is_string($setting->modules_enable)) {
            $setting->modules_enable = json_decode($setting->modules_enable, true) ?? [];
        }

        return view('staff.ict.application-settings.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $setting = ApplicationSetting::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'application_code' => 'required|string|max:50|unique:application_settings,application_code,' . $id,
            'academic_session' => 'required|string',
            'application_fee' => 'required|numeric|min:0',
            'acceptance_fee' => 'required|numeric|min:0',
            'admission_duration' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'enabled' => 'required|boolean',
            'modules_enable' => 'array',
            'modules_enable.documents' => 'nullable|array',
        ]);

        // Build modules array
        $modules = [
            'profile' => $request->has('modules_enable.profile'),
            'olevel' => $request->has('modules_enable.olevel'),
            'alevel' => $request->has('modules_enable.alevel'),
            'jamb_detail' => $request->has('modules_enable.jamb_detail'),
            'course_of_study' => $request->has('modules_enable.course_of_study'),
            'documents' => $request->input('modules_enable.documents', [])
        ];

        $setting->update([
            'name' => $validated['name'],
            'application_code' => $validated['application_code'],
            'academic_session' => $validated['academic_session'],
            'application_fee' => $validated['application_fee'],
            'acceptance_fee' => $validated['acceptance_fee'],
            'admission_duration' => $validated['admission_duration'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'enabled' => $validated['enabled'],
            'modules_enable' => $modules
        ]);

        return redirect()->route('ict.application_settings.index')
            ->with('success', 'Application setting updated successfully.');
    }
}

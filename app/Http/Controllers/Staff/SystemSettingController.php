<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\GradingSystem;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');
        $gradingSystem = GradingSystem::orderBy('min_score', 'desc')->get();

        return view('staff.settings.index', compact('settings', 'gradingSystem'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', 'grading']);

        foreach ($inputs as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();

            if ($setting) {
                if ($request->hasFile($key)) {
                    // Handle File Upload
                    $file = $request->file($key);
                    $path = $file->storeAs('uploads/settings', $key . '.' . $file->getClientOriginalExtension(), 'public');
                    // We need to make sure the value stored is accessible via asset() or public_path() depending on usage.
                    // For now, let's store 'storage/uploads/settings/...' which is common for symlinked storage.
                    // But existing code uses direct public_path('assets/...').
                    // We will just store the relative path 'storage/...' or just the filename? 
                    // Let's store 'storage/uploads/settings/filename.ext'
                    $setting->value = 'storage/' . $path;
                } else {
                    $setting->value = $value;
                }
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function updateGrading(Request $request)
    {
        $grades = $request->input('grades');

        // Simple approach: Delete all and recreate, or update existing.
        // Let's update existing or create new.

        foreach ($grades as $id => $data) {
            if ($id === 'new') {
                foreach ($data as $newGrade) {
                    if (!empty($newGrade['grade'])) {
                        GradingSystem::create($newGrade);
                    }
                }
            } else {
                $grade = GradingSystem::find($id);
                if ($grade) {
                    $grade->update($data);
                }
            }
        }

        return redirect()->back()->with('success', 'Grading system updated.');
    }
}

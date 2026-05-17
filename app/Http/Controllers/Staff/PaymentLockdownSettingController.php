<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Faculty;
use App\Models\PaymentLockdownSetting;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentLockdownSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentLockdownSetting::orderBy('deadline', 'asc');

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $lockdowns = $query->paginate(20);
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');

        return view('staff.bursary.payment_lockdown_settings.index', compact('lockdowns', 'paymentTypes'));
    }

    public function create()
    {
        $campuses = Campus::all();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $entryModes = EntryMode::orderBy('name')->get();
        $programmes = DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $levels = [100, 200, 300, 400, 500];
        $genders = ['Male', 'Female'];
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');

        return view('staff.bursary.payment_lockdown_settings.create', compact(
            'campuses', 'faculties', 'departments', 'sessions', 'entryModes', 'programmes', 'levels', 'genders', 'paymentTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'payment_type' => 'nullable|string',
            'deadline' => 'required|date',
            'campus_ids' => 'nullable|array',
            'faculty_ids' => 'nullable|array',
            'department_ids' => 'nullable|array',
            'levels' => 'nullable|array',
            'admission_sessions' => 'nullable|array',
            'genders' => 'nullable|array',
            'entry_modes' => 'nullable|array',
            'programmes' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        PaymentLockdownSetting::create([
            'title' => $validated['title'],
            'payment_type' => $validated['payment_type'] ?? null,
            'deadline' => $validated['deadline'],
            'campus_ids' => $request->input('campus_ids', []),
            'faculty_ids' => $request->input('faculty_ids', []),
            'department_ids' => $request->input('department_ids', []),
            'levels' => $request->input('levels', []),
            'admission_sessions' => $request->input('admission_sessions', []),
            'genders' => $request->input('genders', []),
            'entry_modes' => $request->input('entry_modes', []),
            'programmes' => $request->input('programmes', []),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('bursary.payment-lockdown-settings.index')
            ->with('success', 'Payment lockdown setting created successfully.');
    }

    public function edit(PaymentLockdownSetting $paymentLockdownSetting)
    {
        $campuses = Campus::all();
        $faculties = Faculty::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $entryModes = EntryMode::orderBy('name')->get();
        $programmes = DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $levels = [100, 200, 300, 400, 500];
        $genders = ['Male', 'Female'];
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');

        return view('staff.bursary.payment_lockdown_settings.edit', [
            'lockdown' => $paymentLockdownSetting,
            'campuses' => $campuses,
            'faculties' => $faculties,
            'departments' => $departments,
            'sessions' => $sessions,
            'entryModes' => $entryModes,
            'programmes' => $programmes,
            'levels' => $levels,
            'genders' => $genders,
            'paymentTypes' => $paymentTypes,
        ]);
    }

    public function update(Request $request, PaymentLockdownSetting $paymentLockdownSetting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'payment_type' => 'nullable|string',
            'deadline' => 'required|date',
            'campus_ids' => 'nullable|array',
            'faculty_ids' => 'nullable|array',
            'department_ids' => 'nullable|array',
            'levels' => 'nullable|array',
            'admission_sessions' => 'nullable|array',
            'genders' => 'nullable|array',
            'entry_modes' => 'nullable|array',
            'programmes' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $paymentLockdownSetting->update([
            'title' => $validated['title'],
            'payment_type' => $validated['payment_type'] ?? null,
            'deadline' => $validated['deadline'],
            'campus_ids' => $request->input('campus_ids', []),
            'faculty_ids' => $request->input('faculty_ids', []),
            'department_ids' => $request->input('department_ids', []),
            'levels' => $request->input('levels', []),
            'admission_sessions' => $request->input('admission_sessions', []),
            'genders' => $request->input('genders', []),
            'entry_modes' => $request->input('entry_modes', []),
            'programmes' => $request->input('programmes', []),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('bursary.payment-lockdown-settings.index')
            ->with('success', 'Payment lockdown setting updated successfully.');
    }

    public function destroy(PaymentLockdownSetting $paymentLockdownSetting)
    {
        $paymentLockdownSetting->delete();

        return back()->with('success', 'Payment lockdown setting deleted successfully.');
    }
}

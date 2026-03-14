<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentSetting::with(['faculty', 'department'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->input('payment_type'));
        }

        if ($request->filled('session')) {
            $query->where('session', $request->input('session'));
        }

        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->input('faculty_id'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('installmental_allow_status')) {
            $query->where('installmental_allow_status', $request->input('installmental_allow_status'));
        }
        if ($request->filled('matric_number')) {
            $query->where('matric_number', $request->input('matric_number'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        $settings = $query->paginate(20);

        // Optional: load for filters
        $faculties = Faculty::all();
        $departments = Department::all();
        $sessions = PaymentSetting::select('session')->distinct()->pluck('session');
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');

        return view('staff.bursary.payment_settings.index', compact(
            'settings',
            'faculties',
            'departments',
            'sessions',
            'paymentTypes'
        ));
    }

    public function create()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $entryModes = \App\Models\EntryMode::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $semesters = AcademicSemester::orderBy('name')->get(['name', 'code']);

        return view('staff.bursary.payment_settings.create', compact('faculties', 'departments', 'entryModes', 'sessions', 'semesters'));
    }

    public function store(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'nullable|array',
            'payment_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string|max:255',
            'semester' => 'nullable|string|max:255',
            'student_type' => 'nullable|array',
            'entry_mode' => 'nullable|array',
            'matric_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'installmental_allow_status' => 'required|boolean',
            'number_of_instalment' => 'required_if:installmental_allow_status,1|nullable|integer|min:1|max:9',
            'list_instalment_percentage' => 'required_if:installmental_allow_status,1|nullable|array',
        ]);

        // 2. Prepare 'level' data (Convert strings like ["100"] to integers [100])
        if (isset($validated['level']) && is_array($validated['level'])) {
            $validated['level'] = array_map('intval', $validated['level']);
        } else {
            $validated['level'] = null;
        }

        // 3. Prepare 'list_instalment_percentage'
        // (Optional: You can uncomment sum validation here if needed)
        $instalmentData = null;
        if ($validated['installmental_allow_status'] && isset($validated['list_instalment_percentage'])) {
            $instalmentData = $validated['list_instalment_percentage'];
        }

        // 4. Create the Record
        // We use json_encode() on array fields to fix the "Array to string conversion" error
        PaymentSetting::create([
            'faculty_id' => $validated['faculty_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            // Fix: Encode array to JSON string
            'level' => $validated['level'] ? $validated['level'] : [],
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'session' => $validated['session'],
            'semester' => $validated['semester'] ?? null,
            'student_type' => $validated['student_type'] ?? [],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'matric_number' => $validated['matric_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'installmental_allow_status' => $validated['installmental_allow_status'],
            'number_of_instalment' => $validated['number_of_instalment'] ?? 1,
            'list_instalment_percentage' => $instalmentData,
        ]);

        return redirect()
            ->route('bursary.payment-settings.index')
            ->with('success', 'Payment setting created successfully.');
    }

    public function edit(PaymentSetting $paymentSetting)
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $entryModes = \App\Models\EntryMode::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $semesters = AcademicSemester::orderBy('name')->get(['name', 'code']);

        return view('staff.bursary.payment_settings.edit', compact('paymentSetting', 'faculties', 'departments', 'entryModes', 'sessions', 'semesters'));
    }

    public function update(Request $request, PaymentSetting $paymentSetting)
    {
        $validated = $request->validate([
            'faculty_id' => 'nullable|uuid',
            'department_id' => 'nullable|uuid',
            'level' => 'nullable|array',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string',
            'semester' => 'nullable|string|max:255',
            'student_type' => 'nullable|array',
            'entry_mode' => 'nullable|array',
            'description' => 'nullable|string',
            'installmental_allow_status' => 'required|boolean',
            'number_of_instalment' => 'required_if:installmental_allow_status,1|nullable|integer|min:1|max:9',
            'list_instalment_percentage' => 'required_if:installmental_allow_status,1|nullable|array',
            'matric_number' => 'nullable|string|max:20',
        ]);

        // ✅ Convert all level values to integers
        if (isset($validated['level']) && is_array($validated['level'])) {
            $validated['level'] = array_map('intval', $validated['level']);
        } else {
            $validated['level'] = null;
        }

        // Total percentage validation removed as per existing code comment pattern
        $listInstalmentPercentage = $validated['installmental_allow_status']
            ? $validated['list_instalment_percentage']
            : null;

        // ✅ Save updated data
        $paymentSetting->update([
            'faculty_id' => $validated['faculty_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'level' => $validated['level'] ? $validated['level'] : [], // fixed here
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'session' => $validated['session'],
            'semester' => $validated['semester'] ?? null,
            'student_type' => $validated['student_type'] ?? [],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'description' => $validated['description'],
            'installmental_allow_status' => $validated['installmental_allow_status'],
            'number_of_instalment' => $validated['number_of_instalment'] ?? 1,
            'list_instalment_percentage' => $listInstalmentPercentage,
            'matric_number' => $validated['matric_number'] ?? null,
        ]);

        return redirect()
            ->route('bursary.payment-settings.index')
            ->with('success', 'Payment setting updated successfully.');
    }

    public function destroy(PaymentSetting $paymentSetting)
    {
        $paymentSetting->delete();

        return back()->with('success', 'Payment setting deleted successfully.');
    }
}

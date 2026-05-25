<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Faculty;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentSetting::orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->input('payment_type'));
        }

        if ($request->filled('session')) {
            $query->where('session', $request->input('session'));
        }

        if ($request->filled('faculty_id')) {
            $query->whereJsonContains('faculty_ids', $request->input('faculty_id'));
        }
        if ($request->filled('department_id')) {
            $query->whereJsonContains('department_ids', $request->department_id);
        }
        if ($request->filled('student_type')) {
            $query->whereJsonContains('student_type', $request->student_type);
        }
        if ($request->filled('entry_mode')) {
            $query->whereJsonContains('entry_mode', $request->entry_mode);
        }
        if ($request->filled('level')) {
            $query->whereJsonContains('level', $request->level);
        }
        if ($request->filled('installmental_allow_status')) {
            $query->where('installmental_allow_status', $request->installmental_allow_status);
        }
        if ($request->filled('matric_number')) {
            $query->whereJsonContains('matric_numbers', $request->input('matric_number'));
        }

        if ($request->filled('semester')) {
            $query->whereJsonContains('semesters', $request->input('semester'));
        }

        if ($request->filled('admission_session')) {
            $query->whereJsonContains('admission_session', $request->input('admission_session'));
        }

        $settings = $query->paginate(20);

        // Optional: load for filters
        $faculties = Faculty::all();
        $departments = Department::all();
        $sessions = PaymentSetting::select('session')->distinct()->pluck('session');
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');
        $programmes = \DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $entryModes = EntryMode::orderBy('name')->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->pluck('name');

        return view('staff.bursary.payment_settings.index', compact(
            'settings',
            'faculties',
            'departments',
            'sessions',
            'paymentTypes',
            'programmes',
            'entryModes',
            'academicSessions'
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
            'faculty_ids' => 'nullable|array',
            'faculty_ids.*' => 'exists:faculties,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'level' => 'nullable|array',
            'payment_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string|max:255',
            'semesters' => 'nullable|array',
            'student_type' => 'nullable|array',
            'entry_mode' => 'nullable|array',
            'sexes' => 'nullable|array',
            'matric_numbers' => 'nullable|array',
            'description' => 'nullable|string',
            'installmental_allow_status' => 'required|boolean',
            'number_of_instalment' => 'required_if:installmental_allow_status,1|nullable|integer|min:1|max:9',
            'list_instalment_percentage' => 'required_if:installmental_allow_status,1|nullable|array',
            'admission_session' => 'nullable|array',
            'is_compulsory' => 'nullable|boolean',
        ]);

        // 2. Prepare 'level' data (Convert strings like ["100"] to integers [100])
        if (isset($validated['level']) && is_array($validated['level'])) {
            $validated['level'] = array_map('intval', $validated['level']);
        } else {
            $validated['level'] = [];
        }

        // Prepare 'matric_numbers' (Parse from textarea)
        if (isset($validated['matric_numbers']) && is_array($validated['matric_numbers'])) {
            $raw = $validated['matric_numbers'][0] ?? '';
            $validated['matric_numbers'] = array_values(array_filter(array_map('trim', explode(',', str_replace(["\r", "\n"], ',', $raw)))));
        } else {
            $validated['matric_numbers'] = [];
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
            'faculty_ids' => $validated['faculty_ids'] ?? [],
            'department_ids' => $validated['department_ids'] ?? [],
            'level' => $validated['level'] ?? [],
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'session' => $validated['session'],
            'semesters' => $validated['semesters'] ?? [],
            'student_type' => $validated['student_type'] ?? [],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'sexes' => $validated['sexes'] ?? [],
            'matric_numbers' => $validated['matric_numbers'] ?? [],
            'description' => $validated['description'] ?? null,
            'installmental_allow_status' => $validated['installmental_allow_status'],
            'number_of_instalment' => $validated['number_of_instalment'] ?? 1,
            'list_instalment_percentage' => $instalmentData,
            'admission_session' => $validated['admission_session'] ?? [],
            'is_compulsory' => $validated['is_compulsory'] ?? false,
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
            'faculty_ids' => 'nullable|array',
            'faculty_ids.*' => 'exists:faculties,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'level' => 'nullable|array',
            'payment_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string|max:255',
            'semesters' => 'nullable|array',
            'student_type' => 'nullable|array',
            'entry_mode' => 'nullable|array',
            'sexes' => 'nullable|array',
            'matric_numbers' => 'nullable|array',
            'description' => 'nullable|string',
            'installmental_allow_status' => 'required',
            'number_of_instalment' => 'nullable|integer|min:1|max:9',
            'list_instalment_percentage' => 'nullable|array',
            'admission_session' => 'nullable|array',
            'is_compulsory' => 'nullable|boolean',
        ]);

        // ✅ Convert all level values to integers
        $level = isset($validated['level']) ? array_map('intval', (array) $validated['level']) : [];

        // Prepare 'matric_numbers' (Parse from textarea if it comes as an array with one string)
        $matricNumbers = [];
        if (isset($validated['matric_numbers']) && is_array($validated['matric_numbers'])) {
            $raw = $validated['matric_numbers'][0] ?? '';
            $matricNumbers = array_values(array_filter(array_map('trim', explode(',', str_replace(["\r", "\n"], ',', $raw)))));
        }

        // Handle Installments logic explicitly
        $allowInstallments = (bool) $validated['installmental_allow_status'];
        $instalmentData = $allowInstallments ? ($validated['list_instalment_percentage'] ?? []) : null;
        $numInstalments = $allowInstallments ? ($validated['number_of_instalment'] ?? 2) : 1;

        // ✅ Save updated data
        $paymentSetting->update([
            'faculty_ids' => $validated['faculty_ids'] ?? [],
            'department_ids' => $validated['department_ids'] ?? [],
            'level' => $level,
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'session' => $validated['session'],
            'semesters' => $validated['semesters'] ?? [],
            'student_type' => $validated['student_type'] ?? [],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'sexes' => $validated['sexes'] ?? [],
            'matric_numbers' => $matricNumbers,
            'description' => $validated['description'] ?? null,
            'installmental_allow_status' => $allowInstallments,
            'number_of_instalment' => $numInstalments,
            'list_instalment_percentage' => $instalmentData,
            'admission_session' => $validated['admission_session'] ?? [],
            'is_compulsory' => $validated['is_compulsory'] ?? false,
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

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PaymentSettingsExport($request),
            'payment_settings_'.now()->format('Y_m_d_His').'.xlsx'
        );
    }
}

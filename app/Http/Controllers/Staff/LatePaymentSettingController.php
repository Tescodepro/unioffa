<?php

namespace App\Http\Controllers\Staff;

use App\Exports\LatePaymentSettingsExport;
use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\EntryMode;
use App\Models\LatePaymentSetting;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LatePaymentSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = LatePaymentSetting::with('campus')->orderBy('created_at', 'desc');

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->filled('session')) {
            $query->where('session', $request->input('session'));
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }
        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->campus_id);
        }
        if ($request->filled('student_type')) {
            $query->whereJsonContains('student_type', $request->student_type);
        }
        if ($request->filled('level')) {
            $query->whereJsonContains('level', $request->level);
        }
        if ($request->filled('entry_mode')) {
            $query->whereJsonContains('entry_mode', $request->entry_mode);
        }
        if ($request->filled('admission_session')) {
            $query->whereJsonContains('admission_session', $request->admission_session);
        }

        $settings = $query->paginate(20);
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $semesters = AcademicSemester::orderBy('name')->get(['name', 'code']);
        $campuses = Campus::all();
        $programmes = \DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $entryModes = EntryMode::orderBy('name')->get();
        $levels = [100, 200, 300, 400, 500];

        return view('staff.bursary.late_payment_settings.index', compact('settings', 'paymentTypes', 'sessions', 'semesters', 'campuses', 'programmes', 'entryModes', 'levels'));
    }

    public function create()
    {
        $campuses = Campus::all();
        $entryModes = EntryMode::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $semesters = AcademicSemester::orderBy('name')->get(['name', 'code']);
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');
        $programmes = \DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $levels = [100, 200, 300, 400, 500];

        return view('staff.bursary.late_payment_settings.create', compact('campuses', 'entryModes', 'sessions', 'semesters', 'paymentTypes', 'programmes', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_type' => 'required|string',
            'campus_id' => 'required|uuid|exists:campuses,id',
            'entry_mode' => 'nullable|array',
            'semester' => 'nullable|string',
            'session' => 'nullable|string',
            'closing_date' => 'required|date',
            'late_fee_amount' => 'required|numeric|min:0',
            'increment_amount' => 'nullable|numeric|min:0',
            'increment_date' => 'nullable|date|after:closing_date',
            'excluded_matric_numbers' => 'nullable|string',
            'student_type' => 'nullable|array',
            'level' => 'nullable|array',
            'admission_session' => 'nullable|array',
        ]);

        LatePaymentSetting::create([
            'payment_type' => $validated['payment_type'],
            'campus_id' => $validated['campus_id'],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'semester' => $validated['semester'] ?? null,
            'session' => $validated['session'] ?? null,
            'closing_date' => $validated['closing_date'],
            'late_fee_amount' => $validated['late_fee_amount'],
            'increment_amount' => $validated['increment_amount'] ?? null,
            'increment_date' => $validated['increment_date'] ?? null,
            'excluded_matric_numbers' => $request->filled('excluded_matric_numbers')
                ? array_map('trim', explode(',', $request->excluded_matric_numbers))
                : null,
            'student_type' => $request->input('student_type', []),
            'level' => $request->input('level', []),
            'admission_session' => $request->input('admission_session', []),
        ]);

        return redirect()->route('bursary.late-payment-settings.index')
            ->with('success', 'Late payment setting created successfully.');
    }

    public function edit(LatePaymentSetting $latePaymentSetting)
    {
        $campuses = Campus::all();
        $entryModes = EntryMode::orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->pluck('name');
        $semesters = AcademicSemester::orderBy('name')->get(['name', 'code']);
        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');
        $programmes = \DB::table('students')->distinct()->pluck('programme')->filter()->values();
        $levels = [100, 200, 300, 400, 500];

        return view('staff.bursary.late_payment_settings.edit', compact('latePaymentSetting', 'campuses', 'entryModes', 'sessions', 'semesters', 'paymentTypes', 'programmes', 'levels'));
    }

    public function update(Request $request, LatePaymentSetting $latePaymentSetting)
    {
        $validated = $request->validate([
            'payment_type' => 'required|string',
            'campus_id' => 'required|uuid|exists:campuses,id',
            'entry_mode' => 'nullable|array',
            'semester' => 'nullable|string',
            'session' => 'nullable|string',
            'closing_date' => 'required|date',
            'late_fee_amount' => 'required|numeric|min:0',
            'increment_amount' => 'nullable|numeric|min:0',
            'increment_date' => 'nullable|date|after:closing_date',
            'excluded_matric_numbers' => 'nullable|string',
            'student_type' => 'nullable|array',
            'level' => 'nullable|array',
            'admission_session' => 'nullable|array',
        ]);

        $latePaymentSetting->update([
            'payment_type' => $validated['payment_type'],
            'campus_id' => $validated['campus_id'],
            'entry_mode' => $validated['entry_mode'] ?? [],
            'semester' => $validated['semester'] ?? null,
            'session' => $validated['session'] ?? null,
            'closing_date' => $validated['closing_date'],
            'late_fee_amount' => $validated['late_fee_amount'],
            'increment_amount' => $validated['increment_amount'] ?? null,
            'increment_date' => $validated['increment_date'] ?? null,
            'excluded_matric_numbers' => $request->filled('excluded_matric_numbers')
                ? array_map('trim', explode(',', $request->excluded_matric_numbers))
                : null,
            'student_type' => $request->input('student_type', []),
            'level' => $request->input('level', []),
            'admission_session' => $request->input('admission_session', []),
        ]);

        return redirect()->route('bursary.late-payment-settings.index')
            ->with('success', 'Late payment setting updated successfully.');
    }

    public function destroy(LatePaymentSetting $latePaymentSetting)
    {
        $latePaymentSetting->delete();

        return back()->with('success', 'Late payment setting deleted successfully.');
    }

    public function export(Request $request)
    {
        $filters = $request->only(['payment_type', 'session', 'semester', 'campus_id', 'admission_session']);

        return Excel::download(new LatePaymentSettingsExport($filters), 'late_payment_settings_'.now()->format('Y_m_d_His').'.xlsx');
    }
}

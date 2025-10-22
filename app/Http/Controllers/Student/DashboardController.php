<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use App\Models\{Hostel, User, Student};
use App\Models\PaymentSetting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\{UniqueIdService, PaymentVerificationService, StudentMigrationService, MatricNumberGenerationService};
use App\Services\HostelAssignmentService;
use Illuminate\Support\Facades\Log;



class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('student.department.faculty');
        $recentTransactions = Transaction::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        // Initialize services ONCE outside loop
        $verifier = new PaymentVerificationService();
        $studentMigration = new StudentMigrationService();
        $matricService = new MatricNumberGenerationService();
        foreach ($recentTransactions as $txn) {
            try {
                // 1. VERIFY PAYMENT (only if not verified)
                if ($txn->payment_status != 1) {
                    $verifyResponse = $verifier->verify($txn->refernce_number); // ✅ FIXED TYPO

                    // Update status if successful
                    if (isset($verifyResponse['status']) && $verifyResponse['status'] === 'success') {
                        $txn->update(['payment_status' => 1]);
                    }
                    $txn->refresh();
                }

                // 2. ACCEPTANCE PAYMENT - CREATE STUDENT RECORD
                if ($txn->payment_type === 'acceptance' && !$user->student) {
                    $user = User::find($txn->user_id);
                    if (!$user->student) {
                        $newStudent = $studentMigration->migrate($txn->user_id);
                        $user->load('student.department.faculty'); // ✅ RELOAD RELATIONSHIPS
                    }
                }
                // 3. TUITION PAYMENT - GENERATE MATRIC NUMBER
                if ($txn->payment_type === 'tuition' && !Student::hasMatricNumber()) {
                    $student = $user->student;
$year = Carbon::parse(now())->format('Y');
                    $newMatricNo = Student::generateMatricNo($student->department->department_code, $year, $student->entry_mode);
                    $student->update(['matric_no' => $newMatricNo]);
                    $student->user->update(['username' => $newMatricNo]);
                }
            } catch (\Exception $e) {
                Log::error("Transaction {$txn->id} processing failed: " . $e->getMessage());
            }
        }

        // ✅ FINAL RELOAD - Ensure dashboard has latest data
        $user->load('student.department.faculty');

        return view('student.dashboard', compact('user'));
    }

    // public function loadPayment()
    // {
    //     $user = Auth::user()->load('student.department.faculty');
    //     $currentSession = activeSession()->name ?? null;

    //     if (! $user->student) {
    //         return redirect()->back()->with('error', 'Student profile not found.');
    //     }

    //     if (! $currentSession) {
    //         return redirect()->back()->with('error', 'No active session found.');
    //     }

    //     $student = $user->student;

    //     // Step 1: Fetch required payment settings
    //     $paymentSettings = PaymentSetting::query()
    //         ->where('student_type', $student->programme)
    //         ->whereJsonContains('level', (int) $student->level)
    //         ->where('session', $currentSession)
    //         ->when($student->department?->faculty_id, function ($q) use ($student) {
    //             $q->where(function ($sub) use ($student) {
    //                 $sub->whereNull('faculty_id')
    //                     ->orWhere('faculty_id', $student->department->faculty_id);
    //             });
    //         })
    //         ->when($student->department_id, function ($q) use ($student) {
    //             $q->where(function ($sub) use ($student) {
    //                 $sub->whereNull('department_id')
    //                     ->orWhere('department_id', $student->department_id);
    //             });
    //         })
    //         ->where(function ($q) use ($student) {
    //             $q->whereNull('sex')
    //                 ->orWhere('sex', $student->sex);
    //         })
    //         ->where(function ($q) use ($student) {
    //             $q->whereNull('matric_number')
    //                 ->orWhere('matric_number', $student->matric_number);
    //         })
    //         ->get();

    //     if ($paymentSettings->isEmpty()) {
    //         return redirect()->back()->with('error', 'No payment settings found for your profile.');
    //     }

    //     // Step 2: Fetch all transactions for this student + session once (avoid N+1)
    //     $transactions = Transaction::query()
    //         ->where('user_id', $user->id)
    //         ->where('session', $currentSession)
    //         ->where('payment_status', 1)
    //         ->get()
    //         ->groupBy('payment_type');

    //     // Step 3: Attach transaction details + installment rules
    //     $paymentSettings = $paymentSettings->map(function ($payment) use ($transactions, $student) {
    //         $txns = $transactions->get($payment->payment_type, collect());
    //         $amountPaid = $txns->sum('amount');
    //         $installmentCount = $txns->count();

    //         $payment->amount_paid = $amountPaid;
    //         $payment->balance = max($payment->amount - $amountPaid, 0);
    //         $payment->installment_count = $installmentCount;
    //         $payment->installment_scheme = [];
    //         $payment->max_installments = 1;

    //         // --- Tuition Installment Rules ---
    //         if ($payment->payment_type === 'tuition') {
    //             if ($student->programme === 'REGULAR') {
    //                 $payment->max_installments = 2;
    //                 if ($installmentCount === 0) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount * 0.6), // 60%
    //                         round($payment->amount),       // 100%
    //                     ];
    //                 } elseif ($installmentCount === 1 && $amountPaid == round($payment->amount * 0.6)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount * 0.4), // 40%
    //                     ];
    //                 } elseif ($installmentCount === 1 && $amountPaid != round($payment->amount * 0.6)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount - $amountPaid), // 40%
    //                     ];
    //                 }
    //             } else {
    //                 $payment->max_installments = 3;

    //                 if ($installmentCount === 0) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),       // ~33%
    //                         round($payment->amount * 2 / 3),   // ~66%
    //                         round($payment->amount),           // full
    //                     ];
    //                 } elseif ($installmentCount === 1 && $amountPaid == round($payment->amount / 3)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),
    //                         round($payment->amount * 2 / 3),
    //                         round($payment->amount),
    //                     ];
    //                 } elseif ($installmentCount === 2 && $amountPaid >= round($payment->amount * 2 / 3)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),
    //                         round($payment->amount),
    //                     ];
    //                 }
    //             }
    //         }

    //         // --- Administrative Installment Rules ---
    //         if ($payment->payment_type === 'administrative') {
    //             if ($student->programme === 'REGULAR') {
    //                 $payment->max_installments = 3;
    //                 $payment->installment_scheme = [round($payment->amount)];
    //             } else {
    //                 $payment->max_installments = 2; // Assume 50/50 split
    //                 $payment->installment_scheme = [
    //                     round($payment->amount * 0.5),
    //                     round($payment->amount),
    //                 ];
    //             }
    //         }

    //         if ($payment->payment_type === 'technical') {
    //             if ($student->programme !== 'REGULAR') {
    //                 if ($installmentCount === 0) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),       // ~33%
    //                         round($payment->amount * 2 / 3),   // ~66%
    //                         round($payment->amount),           // full
    //                     ];
    //                 } elseif ($installmentCount === 1 && $amountPaid == round($payment->amount / 3)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),
    //                         round($payment->amount * 2 / 3),
    //                         round($payment->amount),
    //                     ];
    //                 } elseif ($installmentCount === 2 && $amountPaid >= round($payment->amount * 2 / 3)) {
    //                     $payment->installment_scheme = [
    //                         round($payment->amount / 3),
    //                         round($payment->amount),
    //                     ];
    //                 }
    //             }
    //         }


    //         return $payment;
    //     });

    //     return view('student.payment', compact('paymentSettings', 'currentSession'));
    // }


    public function loadPayment()
    {
        $user = Auth::user()->load('student.department.faculty');
        $currentSession = activeSession()->name ?? null;
        if (! $user->student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        if (! $currentSession) {
            return redirect()->back()->with('error', 'No active session found.');
        }
        $student = $user->student;
        // ✅ 1. Fetch payment settings dynamically
        $paymentSettings = PaymentSetting::query()
            ->where('session', $currentSession) // session must always match
            ->when($student->programme, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('student_type')
                        ->orWhere('student_type', $student->programme);
                });
            }, function ($q) {
                // If student type is null, only accept settings where student_type is null
                $q->whereNull('student_type');
            })
            ->when($student->level, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('level')
                        ->orWhereJsonContains('level', (int) $student->level);
                });
            }, function ($q) {
                // If student's level is null, only accept settings where level is null
                $q->whereNull('level');
            })
            ->when($student->department?->faculty_id, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('faculty_id')
                        ->orWhere('faculty_id', $student->department->faculty_id);
                });
            })
            ->when($student->department_id, function ($q) use ($student) {
                $q->where(function ($sub) use ($student) {
                    $sub->whereNull('department_id')
                        ->orWhere('department_id', $student->department_id);
                });
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('sex')
                    ->orWhere('sex', $student->sex);
            })
            ->where(function ($q) use ($student) {
                $q->whereNull('matric_number')
                    ->orWhere('matric_number', $student->matric_no);
            })
            ->get();

        if ($paymentSettings->isEmpty()) {
            return redirect()->back()->with('error', 'No payment settings found for your profile.');
        }

        // ✅ 2. Fetch student transactions
        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('session', $currentSession)
            ->where('payment_status', 1)
            ->get()
            ->groupBy('payment_type');

        // ✅ 3. Dynamic computation using DB installment fields
        $paymentSettings = $paymentSettings->map(function ($payment) use ($transactions) {
            $txns = $transactions->get($payment->payment_type, collect());
            $amountPaid = $txns->sum('amount');
            $installmentCount = $txns->count();

            $payment->amount_paid = $amountPaid;
            $payment->balance = max($payment->amount - $amountPaid, 0);
            $payment->installment_count = $installmentCount;
            $payment->installment_scheme = [];
            $payment->max_installments = 1;

            // ✅ Use DB installment settings if enabled
            if ($payment->installmental_allow_status) {
                $percentages = json_decode($payment->list_instalment_percentage, true) ?? [];
                $payment->max_installments = $payment->number_of_instalment ?? count($percentages);

                // Convert cumulative percentages (e.g. [60,100], [33,66,100]) into actual amounts
                $installmentAmounts = collect($percentages)->map(fn($percent) => round($payment->amount * ($percent / 100)));

                // Find remaining payments (any stage above what’s already paid)
                $remaining = $installmentAmounts->filter(fn($amt) => $amt > $amountPaid)->values();

                $payment->installment_scheme = $remaining->toArray();
            }

            return $payment;
        });

        return view('student.payment', compact('paymentSettings', 'currentSession'));
    }

    public function paymentHistory()
    {
        $user = Auth::user()->load('student.department.faculty');
        $currentSession = activeSession()->name ?? null;
        // Load all transactions for the user
        $transactions = Transaction::where('user_id', $user->id)
            ->where('payment_status', '1')
            ->latest()
            ->get();

        return view('student.payment-history', compact('transactions', 'currentSession'));
    }

    public function downloadAdmissionLetter()
    {
        $user = Auth::user();

        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $department = $student->department;
        if ($student->programme == 'IDEL') {
            $student->programme = 'IDELDE';
        }

        $applicationSetting = ApplicationSetting::where('application_code', $student->entry_mode)->first();

        $data = [
            'student' => $student,
            'session' => $student->admission_session,
            'department' => $department,
            'duration' => $applicationSetting,
            'date' => Carbon::now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('student.admission-letter', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download('Admission_Letter_' . $student->full_name . '.pdf');
    }

    // ==================== Profile ================================
    public function profile()
    {
        $user = Auth::user()->load('student.department.faculty');

        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        // ✅ Validate request
        $request->validate([
            // User table
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'date_of_birth' => 'nullable|date',
            'state_of_origin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',

            // Student table
            'sex' => 'required|in:male,female',
            'address' => 'nullable|string|max:500',
        ]);

        // ✅ Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profile_pictures', $filename, 'public');
            $user->profile_picture = 'storage/profile_pictures/' . $filename;
        }

        // ✅ Update user details
        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'state_of_origin' => $request->state_of_origin,
            'lga' => $request->lga,
            'nationality' => $request->nationality,
            'religion' => $request->religion,
            'profile_picture' => $user->profile_picture,
        ]);

        // ✅ Update student details
        if ($student) {
            $student->update([
                'sex' => $request->sex,
                'address' => $request->address,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (! \Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function logoutAction()
    {
        Auth::logout();

        return redirect()->route('student.login')->with('success', 'Logged out successfully.');
    }

    // ==================== Hostel ===============================
    public function hostelIndex()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Check if student already has an assigned hostel
        $assignment = $student->hostelAssignment()->with('room.hostel')->first();

        return view('student.hostel', [
            'assignment' => $assignment,
        ]);
    }

    public function hostelApply(HostelAssignmentService $hostelService)
    {
        $student = Auth::user()->student;

        if (! $student) {
            return back()->with('error', 'Student profile not found.');
        }

        $result = $hostelService->autoAssign($student);

        return back()->with('success', $result['message'] ?? 'Hostel application processed.');
    }
}

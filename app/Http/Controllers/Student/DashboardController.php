<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ApplicationSetting;
use App\Models\Hostel;
use App\Models\PaymentSetting;
use App\Models\Result;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use App\Services\HostelAssignmentService;
use App\Services\MatricNumberGenerationService;
use App\Services\PaymentVerificationService;
use App\Services\StudentMigrationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('student.department.faculty');
        $recentTransactions = Transaction::where('user_id', Auth::id())
            ->latest()
            ->take(35)
            ->get();

        // Initialize services ONCE outside loop
        $verifier = new PaymentVerificationService;
        $studentMigration = new StudentMigrationService;
        $matricService = new MatricNumberGenerationService;
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

                // 3. TUITION PAYMENT - GENERATE MATRIC NUMBER IF VERIFIED AND STUDENT HAS NO VALID MATRIC
                if ($txn->payment_type === 'tuition' && $txn->payment_status === 1) {
                    $student = $user->student;
                    if ($student) {
                        $generated = $matricService->generateIfNeeded($student);
                        if ($generated) {
                            Log::info("Matric number generated for student {$student->id} from dashboard verification");
                            // Reload student to get updated matric number
                            $student->refresh();
                            $user->load('student.department.faculty');
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Transaction {$txn->id} processing failed: " . $e->getMessage());
            }
        }

        // ✅ FINAL RELOAD - Ensure dashboard has latest data
        $user->load('student.department.faculty');
        return view('student.dashboard', compact('user'));
    }

    public function loadPayment()
    {
        $user = Auth::user()->load('student.department.faculty');
        $currentSession = activeSession()->name ?? null;
        if (!$user->student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        if (!$currentSession) {
            return redirect()->back()->with('error', 'No active session found.');
        }
        $student = $user->student;
        if (($student->entry_mode == 'DE' or $student->entry_mode == 'TRANSFER') and ($student->level == 200 or $student->level == 300) and $student->admission_session == $currentSession) {
            $student->level = 100;
        }
        // ✅ 1. Fetch payment settings dynamically
        $paymentSettings = PaymentSetting::query()
            ->where('session', $currentSession) // session must always match
            ->when($student->entry_mode === 'TRANSFER', function ($query) {
                $query->where('payment_type', '!=', 'matriculation');
            })
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
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                    ->orWhere('entry_mode', $student->entry_mode);
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

                // If percentages are empty, fallback to default logic or disable installment
                if (empty($percentages)) {
                    // Fallback: treat as single payment if no percentages defined
                    return $payment;
                }

                $payment->max_installments = $payment->number_of_instalment ?? count($percentages);

                // 1. Convert splits (e.g. [60, 40]) to Cumulative Milestones (e.g. [60, 100])
                $cumulativePercentages = [];
                $runningTotal = 0;
                foreach ($percentages as $p) {
                    $runningTotal += $p;
                    $cumulativePercentages[] = $runningTotal;
                }

                // Ensure the last one is exactly 100 to avoid floating point issues
                if (!empty($cumulativePercentages) && end($cumulativePercentages) !== 100) {
                    // Normalize if they don't add up to 100, or just trust the admin? 
                    // Let's force the last milestone relative to total amount in the next step.
                }

                // 2. Convert Cumulative Percentages to Cumulative Monetary Milestones
                $milestones = collect($cumulativePercentages)->map(function ($percent) use ($payment) {
                    return round($payment->amount * ($percent / 100));
                });

                // Fix: Ensure the last milestone is exactly the total amount
                if ($milestones->isNotEmpty()) {
                    $milestones->pop();
                    $milestones->push($payment->amount);
                }

                // 3. Calculate "To Pay" options based on Amount Paid
                // We only show milestones that are GREATER than what has been paid.
                $options = $milestones->filter(function ($milestone) use ($amountPaid) {
                    return $milestone > $amountPaid;
                })->map(function ($milestone) use ($amountPaid) {
                    return $milestone - $amountPaid;
                })->values();

                $payment->installment_scheme = $options->toArray();
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

        if (!$student) {
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

        if (!\Hash::check($request->current_password, $user->password)) {
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

        if (!$student) {
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

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $result = $hostelService->autoAssign($student);

        return back()->with('success', $result['message'] ?? 'Hostel application processed.');
    }

    // ==================== Results & Transcript ===============================

    /**
     * Display student results with semester filtering
     */
    public function viewResults(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Get available sessions/semesters for filter
        $sessions = Result::where('matric_no', $student->matric_no)
            ->where('status', 'published')
            ->select('session')
            ->distinct()
            ->orderBy('session', 'desc')
            ->pluck('session');

        $semesters = ['1st', '2nd'];

        // Filter logic
        $query = Result::where('matric_no', $student->matric_no)
            ->where('status', 'published');

        if ($request->filled('session')) {
            $query->where('session', $request->input('session'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $results = $query->orderBy('session', 'desc')
            ->orderBy('semester')
            ->get();

        // Calculate semester GPA if filtered
        $semesterStats = $this->calculateGPA($results);

        return view('student.results', compact('results', 'sessions', 'semesters', 'semesterStats'));
    }

    /**
     * View full transcript
     */
    public function viewTranscript()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $results = Result::where('matric_no', $student->matric_no)
            ->where('status', 'published')
            ->orderBy('session')
            ->orderBy('semester')
            ->get();

        $resultsBySession = $results->groupBy('session');
        $cgpa = $this->calculateCGPA($results);

        return view('student.transcript', compact('student', 'resultsBySession', 'cgpa'));
    }

    /**
     * Download transcript as PDF
     */
    public function downloadTranscript()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $results = Result::where('matric_no', $student->matric_no)
            ->where('status', 'published')
            ->orderBy('session')
            ->orderBy('semester')
            ->get();

        $resultsBySession = $results->groupBy('session');
        $cgpa = $this->calculateCGPA($results);

        $pdf = Pdf::loadView('student.transcript-pdf', compact('student', 'resultsBySession', 'cgpa'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Transcript_' . $student->first_name . '_' . $student->last_name . '.pdf');
    }

    /**
     * Calculate GPA for a set of results
     */
    private function calculateGPA($results)
    {
        $totalUnits = 0;
        $totalGradePoints = 0;
        $unitsPassed = 0;

        foreach ($results as $result) {
            $unit = (int) $result->course_unit;
            $score = (float) $result->total;

            $totalUnits += $unit;

            // 5.0 grading scale
            $points = match (true) {
                $score >= 70 => 5,
                $score >= 60 => 4,
                $score >= 50 => 3,
                $score >= 45 => 2,
                $score >= 40 => 1,
                default => 0
            };

            $totalGradePoints += ($unit * $points);

            if ($score >= 40) {
                $unitsPassed += $unit;
            }
        }

        $gpa = $totalUnits > 0 ? $totalGradePoints / $totalUnits : 0;

        return [
            'total_units' => $totalUnits,
            'units_passed' => $unitsPassed,
            'gpa' => round($gpa, 2),
        ];
    }

    /**
     * Calculate CGPA for all results
     */
    private function calculateCGPA($results)
    {
        return $this->calculateGPA($results)['gpa'];
    }
}

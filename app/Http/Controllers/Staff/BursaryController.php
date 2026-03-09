<?php

namespace App\Http\Controllers\Staff;

use App\Exports\GenericExport;
use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PaymentSetting;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaymentVerificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BursaryController extends Controller
{
    public function dashboard(Request $request)
    {
        $title = 'Bursary Dashboard';

        // Fetch all sessions for the filter dropdown
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->pluck('name');

        // Determine the selected session (default to the current active session)
        $selectedSession = $request->query('session') ?: (activeSession()->name ?? null);

        // Basic payment stats
        $stats = [
            'total_collected' => Transaction::where('payment_status', 1)->where('session', $selectedSession)->sum('amount'),
            'pending_payments' => Transaction::where('payment_status', 0)->where('session', $selectedSession)->count(),
            'failed_payments' => Transaction::where('payment_status', 2)->where('session', $selectedSession)->count(),
            'total_transactions' => Transaction::where('payment_status', 1)->where('session', $selectedSession)->count(),
        ];

        // Group transactions by payment_type
        $paymentsByType = Transaction::select(
            'payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('payment_status', '1')
            ->where('session', $selectedSession)
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('payment_type', 'technical')
                            ->whereRaw("created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            })
            ->groupBy('payment_type')
            ->get();

        // Per-campus breakdown: each campus → each payment type → total amount + count
        $campusBreakdownRaw = Transaction::select(
            'campuses.id as campus_id',
            'campuses.name as campus_name',
            'transactions.payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(transactions.amount) as total_amount')
        )
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->join('campuses', 'users.campus_id', '=', 'campuses.id')
            ->where('transactions.payment_status', 1)
            ->where('transactions.session', $selectedSession)
            ->where(function ($q) {
                $q->where('transactions.payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('transactions.payment_type', 'technical')
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            })
            ->groupBy('campuses.id', 'campuses.name', 'transactions.payment_type')
            ->orderBy('campuses.name')
            ->orderBy('transactions.payment_type')
            ->get();

        // Pivot: [ campus_name => [ payment_type => ['total' => x, 'amount' => y], ...], ... ]
        // Also collect all payment types seen across all campuses
        $campusBreakdown = [];
        $allPaymentTypes = [];

        foreach ($campusBreakdownRaw as $row) {
            if (! isset($campusBreakdown[$row->campus_name])) {
                $campusBreakdown[$row->campus_name] = [
                    'campus_id' => $row->campus_id,
                    'types' => [],
                ];
            }
            $campusBreakdown[$row->campus_name]['types'][$row->payment_type] = [
                'total' => $row->total,
                'amount' => $row->total_amount,
            ];
            if (! in_array($row->payment_type, $allPaymentTypes)) {
                $allPaymentTypes[] = $row->payment_type;
            }
        }
        sort($allPaymentTypes);

        // Transactions with no campus assigned (user.campus_id IS NULL)
        $unassignedRaw = Transaction::select(
            'transactions.payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(transactions.amount) as total_amount')
        )
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->whereNull('users.campus_id')
            ->where('transactions.payment_status', 1)
            ->where('transactions.session', $selectedSession)
            ->where(function ($q) {
                $q->where('transactions.payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('transactions.payment_type', 'technical')
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            })
            ->groupBy('transactions.payment_type')
            ->orderBy('transactions.payment_type')
            ->get();

        $unassignedBreakdown = [];
        foreach ($unassignedRaw as $row) {
            $unassignedBreakdown[$row->payment_type] = [
                'total' => $row->total,
                'amount' => $row->total_amount,
            ];
        }

        // Manual transactions breakdown by payment type
        $manualRaw = Transaction::select(
            'payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('payment_method', 'manual')
            ->where('session', $selectedSession)
            ->groupBy('payment_type')
            ->orderBy('payment_type')
            ->get();

        $manualBreakdown = [];
        foreach ($manualRaw as $row) {
            $manualBreakdown[$row->payment_type] = [
                'total' => $row->total,
                'amount' => $row->total_amount,
            ];
        }

        $recentTransactions = Transaction::with('user')
            ->where('session', $selectedSession)
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('payment_type', 'technical')
                            ->whereRaw("created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('staff.bursary.dashboard', compact(
            'title',
            'stats',
            'paymentsByType',
            'campusBreakdown',
            'allPaymentTypes',
            'unassignedBreakdown',
            'manualBreakdown',
            'recentTransactions',
            'sessions',
            'selectedSession'
        ));
    }

    public function searchStudentHistory(Request $request)
    {
        $title = 'Student Payment History';
        $student = null;
        $transactions = collect();

        $matricQuery = $request->query('matric_number');

        if ($matricQuery) {
            $student = Student::with([
                'user.transactions' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                },
                'user.campus',
                'department.faculty',
            ])
                ->where('matric_no', $matricQuery)
                ->first();

            if ($student && $student->user) {
                $transactions = $student->user->transactions;
            } else {
                return back()->with('error', 'No student found with the provided matric number.');
            }
        }

        return view('staff.bursary.student_history', compact('title', 'student', 'transactions', 'matricQuery'));
    }

    public function downloadReceipt(Request $request, $reference)
    {
        $transaction = Transaction::with('user')
            ->where('refernce_number', $reference)
            ->first();

        if (! $transaction || $transaction->payment_status != 1) {
            return back()->with('error', 'Transaction not found or not successful.');
        }

        $data = [
            'user' => $transaction->user,
            'transaction' => $transaction,
            'date' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('general-payment-receipt', $data)->setPaper('A4', 'portrait');

        return $pdf->download('Payment_Receipt_'.($transaction->refernce_number ?? $transaction->reference).'.pdf');
    }

    public function transactions(Request $request)
    {
        $query = Transaction::query()
            ->with(['user.campus'])
            ->where('payment_status', 1)
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('payment_type', 'technical')
                            ->whereRaw("created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            });

        if ($request->filled('reference')) {
            $query->where('refernce_number', 'like', "%{$request->reference}%");
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->name}%")
                    ->orWhere('last_name', 'like', "%{$request->name}%");
            });
        }

        if ($request->filled('username')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', 'like', "%{$request->username}%");
            });
        }

        if ($request->filled('campus_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('campus_id', $request->campus_id);
            });
        }

        $transactions = $query->latest()->paginate(100);

        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');
        $campuses = Campus::orderBy('name')->get();

        return view('staff.bursary.transactions', compact('transactions', 'paymentTypes', 'campuses'));
    }

    public function exportTransactions(Request $request, $format)
    {
        $query = Transaction::query()
            ->with(['user.campus'])
            ->where('payment_status', 1)
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('payment_type', 'technical')
                            ->whereRaw("created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            });

        if ($request->filled('reference')) {
            $query->where('refernce_number', 'like', "%{$request->reference}%");
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->name}%")
                    ->orWhere('last_name', 'like', "%{$request->name}%");
            });
        }

        if ($request->filled('campus_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('campus_id', $request->campus_id);
            });
        }

        $transactions = $query->latest()->get();

        if ($format === 'excel') {
            return Excel::download(new TransactionsExport($transactions), 'transactions.xlsx');
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.bursary.reports.transactions-pdf', compact('transactions'));

            return $pdf->download('transactions.pdf');
        }

        return back()->with('error', 'Invalid export format.');
    }

    /**
     * Show verify payment page — enter reference number manually.
     */
    public function verifyPaymentForm()
    {
        return view('staff.bursary.verify');
    }

    /**
     * Process verification from form input.
     */
    public function verifyPaymentAction(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $reference = trim($request->reference);
        $transaction = Transaction::where('refernce_number', $reference)->first();

        if (! $transaction) {
            return back()->with('error', 'No transaction found for that reference number.');
        }

        // Always verify from Paystack gateway
        try {
            $paymentService = new \App\Services\PaymentService('paystack');
            $verifyResponse = $paymentService->verifyPayment($reference);
        } catch (\Exception $e) {
            return back()->with('error', 'Verification failed: '.$e->getMessage());
        }

        // Get raw data from Paystack response
        $rawData = $verifyResponse['raw']['data'] ?? [];

        // Update transaction based on response
        if ($verifyResponse['success'] ?? false) {
            $transaction->update([
                'payment_status' => 1,
                'payment_method' => 'paystack',
            ]);

            return back()->with([
                'success' => 'Payment verified successfully!',
                'verifyData' => [
                    'payer_name' => $rawData['customer']['first_name'] ?? ($transaction->user->first_name.' '.$transaction->user->last_name ?? 'N/A'),
                    'payer_email' => $rawData['customer']['email'] ?? ($transaction->user->email ?? 'N/A'),
                    'amount' => ($rawData['amount'] ?? 0) / 100, // Convert from kobo
                    'reference' => $rawData['reference'] ?? $reference,
                    'status' => 'success',
                    'gateway_response' => $rawData['gateway_response'] ?? 'Approved',
                    'paid_at' => $rawData['paid_at'] ?? 'N/A',
                    'channel' => $rawData['channel'] ?? 'N/A',
                ],
            ]);
        } else {
            // Payment not successful
            return back()->with([
                'error' => 'Payment verification failed.',
                'verifyData' => [
                    'payer_name' => $rawData['customer']['first_name'] ?? ($transaction->user->first_name.' '.$transaction->user->last_name ?? 'N/A'),
                    'payer_email' => $rawData['customer']['email'] ?? ($transaction->user->email ?? 'N/A'),
                    'amount' => $transaction->amount ?? '0.00',
                    'reference' => $reference,
                    'status' => 'failed',
                    'gateway_response' => $rawData['gateway_response'] ?? ($verifyResponse['message'] ?? 'Payment not confirmed'),
                    'paid_at' => 'N/A',
                    'channel' => $rawData['channel'] ?? 'N/A',
                ],
            ]);
        }
    }

    /**
     * Verify directly from transaction table button.
     */
    public function verifySingle($id)
    {
        $txn = Transaction::findOrFail($id);

        if ($txn->payment_status == 1) {
            return back()->with('info', 'This transaction is already marked successful.');
        }

        $verifier = new PaymentVerificationService;
        $verifyResponse = $verifier->verify($txn->refernce_number);

        $txn->refresh();
        $txn->verification_message = $verifyResponse['message'] ?? 'Verification completed.';
        $txn->save();

        return back()->with('success', 'Transaction verified successfully.');
    }

    //  REPORT BY FACULTY
    public function reportByFaculty(Request $request)
    {
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->pluck('name');
        $selectedSession = $request->query('session') ?: (activeSession()->name ?? null);
        $excludedTypes = ['accommodation', 'application', 'acceptance', 'maintenance', 'Accommodation', 'Application', 'Acceptance', 'Maintenance'];

        $faculties = Faculty::with([
            'departments.students.user.campus',
            'departments.students.user.transactions' => function ($query) use ($selectedSession, $excludedTypes) {
                if ($selectedSession) {
                    $query->where('session', $selectedSession);
                }
                $query->whereNotIn('payment_type', $excludedTypes);
            },
        ])->get();

        $groupedData = [];

        foreach ($faculties as $faculty) {
            $facultyName = $faculty->faculty_code ?? $faculty->name;

            foreach ($faculty->departments as $department) {
                foreach ($department->students as $student) {
                    // Determine the center
                    $center = $student->user?->campus?->name ?? 'Main Campus';

                    // Initialize the array structure for this center and faculty if not exists
                    if (! isset($groupedData[$center])) {
                        $groupedData[$center] = [];
                    }
                    if (! isset($groupedData[$center][$facultyName])) {
                        $groupedData[$center][$facultyName] = [
                            'faculty' => $facultyName,
                            'total_students' => 0,
                            'total_transactions' => 0,
                            'expected' => 0,
                            'received' => 0,
                            'outstanding' => 0,
                        ];
                    }

                    // Count the student
                    $groupedData[$center][$facultyName]['total_students']++;

                    // Expected
                    $expectedForStudent = PaymentSetting::getFeesForStudent($student, $selectedSession)
                        ->whereNotIn('payment_type', $excludedTypes)
                        ->sum('amount');
                    $groupedData[$center][$facultyName]['expected'] += $expectedForStudent;

                    // Received & Transactions
                    if ($student->user && $student->user->transactions) {
                        $studentTransactions = $student->user->transactions;
                        $groupedData[$center][$facultyName]['total_transactions'] += $studentTransactions->count();
                        $groupedData[$center][$facultyName]['received'] += $studentTransactions->where('payment_status', 1)->sum('amount');
                    }
                }
            }
        }

        // Calculate outstanding and ensure proper array formatting
        foreach ($groupedData as $center => &$facultyList) {
            foreach ($facultyList as &$stats) {
                $stats['outstanding'] = $stats['expected'] - $stats['received'];
            }
            $facultyList = array_values($facultyList); // convert associative array to indexed arrays for blade loops
        }

        // Sort centers alphabetically so Main Campus isn't random
        ksort($groupedData);

        // Alias to $data for the view
        $data = $groupedData;

        return view('staff.bursary.reports.by_faculty', compact('data', 'sessions', 'selectedSession'));
    }

    //  REPORT BY DEPARTMENT
    public function reportByDepartment(Request $request)
    {
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->pluck('name');
        $selectedSession = $request->query('session') ?: (activeSession()->name ?? null);
        $excludedTypes = ['accommodation', 'application', 'acceptance', 'maintenance', 'Accommodation', 'Application', 'Acceptance', 'Maintenance'];

        $departments = Department::with([
            'faculty',
            'students.user.campus',
            'students.user.transactions' => function ($query) use ($selectedSession, $excludedTypes) {
                if ($selectedSession) {
                    $query->where('session', $selectedSession);
                }
                $query->whereNotIn('payment_type', $excludedTypes);
            },
        ])->get();

        $groupedData = [];

        foreach ($departments as $dept) {
            $facultyName = $dept->faculty?->faculty_code ?? 'N/A';
            $departmentName = $dept->department_code ?? $dept->name;

            foreach ($dept->students as $student) {
                // Determine the center
                $center = $student->user?->campus?->name ?? 'Main Campus';

                // Initialize the array structure for this center and department if not exists
                if (! isset($groupedData[$center])) {
                    $groupedData[$center] = [];
                }
                if (! isset($groupedData[$center][$departmentName])) {
                    $groupedData[$center][$departmentName] = [
                        'faculty' => $facultyName,
                        'department' => $departmentName,
                        'total_students' => 0,
                        'total_transactions' => 0,
                        'expected' => 0,
                        'received' => 0,
                        'outstanding' => 0,
                    ];
                }

                // Count the student
                $groupedData[$center][$departmentName]['total_students']++;

                // Expected
                $expectedForStudent = PaymentSetting::getFeesForStudent($student, $selectedSession)
                    ->whereNotIn('payment_type', $excludedTypes)
                    ->sum('amount');
                $groupedData[$center][$departmentName]['expected'] += $expectedForStudent;

                // Received & Transactions
                if ($student->user && $student->user->transactions) {
                    $studentTransactions = $student->user->transactions;
                    $groupedData[$center][$departmentName]['total_transactions'] += $studentTransactions->count();
                    $groupedData[$center][$departmentName]['received'] += $studentTransactions->where('payment_status', 1)->sum('amount');
                }
            }
        }

        // Calculate outstanding and ensure proper array formatting
        foreach ($groupedData as $center => &$departmentList) {
            foreach ($departmentList as &$stats) {
                $stats['outstanding'] = $stats['expected'] - $stats['received'];
            }
            $departmentList = array_values($departmentList); // convert associative array to indexed arrays for blade loops
        }

        // Sort centers alphabetically so Main Campus isn't random
        ksort($groupedData);

        // Alias to $data for the view
        $data = $groupedData;

        return view('staff.bursary.reports.by_department', compact('data', 'sessions', 'selectedSession'));
    }

    //  REPORT BY LEVEL
    public function reportByLevel(Request $request)
    {
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->pluck('name');
        $selectedSession = $request->query('session') ?: (activeSession()->name ?? null);
        $excludedTypes = ['accommodation', 'application', 'acceptance', 'maintenance', 'Accommodation', 'Application', 'Acceptance', 'Maintenance'];

        $levels = PaymentSetting::select('level')->distinct()->pluck('level');
        $groupedData = [];

        foreach ($levels as $levelData) {
            // Handle both array (from model casting) and JSON string
            if (is_array($levelData)) {
                $levelsArray = $levelData;
            } elseif (is_string($levelData)) {
                $levelsArray = json_decode($levelData, true);
            } else {
                continue;
            }

            if (! is_array($levelsArray)) {
                continue;
            }

            foreach ($levelsArray as $level) {
                // Skip if we've already processed this level
                if (in_array($level, $processedLevels)) {
                    continue;
                }
                $processedLevels[] = $level;

                $studentsInLevel = \App\Models\Student::with([
                    'user.campus',
                    'user.transactions' => function ($query) use ($selectedSession, $excludedTypes) {
                        if ($selectedSession) {
                            $query->where('session', $selectedSession);
                        }
                        $query->whereNotIn('payment_type', $excludedTypes);
                    },
                ])->where('level', $level)->get();

                foreach ($studentsInLevel as $student) {
                    // Determine the center
                    $center = $student->user?->campus?->name ?? 'Main Campus';

                    if (! isset($groupedData[$center])) {
                        $groupedData[$center] = [];
                    }

                    if (! isset($groupedData[$center][$level])) {
                        $groupedData[$center][$level] = [
                            'level' => $level,
                            'total_students' => 0,
                            'expected' => 0,
                            'received' => 0,
                            'outstanding' => 0,
                        ];
                    }

                    // Count the student
                    $groupedData[$center][$level]['total_students']++;

                    // Expected
                    $expectedForStudent = PaymentSetting::getFeesForStudent($student, $selectedSession)
                        ->whereNotIn('payment_type', $excludedTypes)
                        ->sum('amount');
                    $groupedData[$center][$level]['expected'] += $expectedForStudent;

                    // Received
                    if ($student->user && $student->user->transactions) {
                        $groupedData[$center][$level]['received'] += $student->user->transactions->where('payment_status', 1)->sum('amount');
                    }
                }
            }
        }

        // Calculate outstanding and flatten array for blades
        foreach ($groupedData as $center => &$levelList) {
            foreach ($levelList as &$stats) {
                $stats['outstanding'] = $stats['expected'] - $stats['received'];
            }
            // Optional: Sort levels numerically/alphabetically within the center
            ksort($levelList);
            $levelList = array_values($levelList);
        }

        // Sort centers
        ksort($groupedData);
        $data = $groupedData;

        return view('staff.bursary.reports.by_level', compact('data', 'sessions', 'selectedSession'));
    }

    //  REPORT BY STUDENT
    public function reportByStudent(Request $request)
    {
        $sessions = \App\Models\AcademicSession::orderBy('name', 'desc')->pluck('name');
        $selectedSession = $request->query('session') ?: (activeSession()->name ?? null);
        $excludedTypes = ['accommodation', 'application', 'acceptance', 'maintenance', 'Accommodation', 'Application', 'Acceptance', 'Maintenance'];

        $students = \App\Models\Student::with([
            'user.transactions' => function ($query) use ($selectedSession, $excludedTypes) {
                if ($selectedSession) {
                    $query->where('session', $selectedSession);
                }
                $query->whereNotIn('payment_type', $excludedTypes);
            },
            'user.campus',
            'department.faculty',
        ])->get();

        $data = $students->map(function ($student) use ($selectedSession, $excludedTypes) {
            $expected = PaymentSetting::getFeesForStudent($student, $selectedSession)
                ->whereNotIn('payment_type', $excludedTypes)
                ->sum('amount');

            $received = 0;
            if ($student->user && $student->user->transactions) {
                $received = $student->user->transactions->where('payment_status', 1)->sum('amount');
            }

            return [
                'student_name' => $student->user?->full_name ?? 'N/A',
                'matric_number' => $student->matric_number ?? $student->matric_no ?? 'N/A',
                'level' => $student->level ?? 'N/A',
                'entry_mode' => $student->entry_mode ?? 'N/A',
                'center' => $student->user?->campus?->name ?? 'Main Campus',
                'faculty' => $student->department?->faculty?->faculty_code ?? 'N/A',
                'department' => $student->department?->department_code ?? 'N/A',
                'expected' => $expected,
                'received' => $received,
                'outstanding' => $expected - $received,
            ];
        })->filter(function ($item) {
            // Optional: only show students who actually have fees expected or paid
            return $item['expected'] > 0 || $item['received'] > 0;
        })->values();

        return view('staff.bursary.reports.by_student', compact('data', 'sessions', 'selectedSession'));
    }

    //  EXPORT HANDLER
    public function export(Request $request, $type, $format)
    {
        $fileName = "report_{$type}.".$format;

        if ($format === 'pdf') {
            // First, dynamically get the data by calling the appropriate report method
            $data = collect();
            switch ($type) {
                case 'faculty':
                    $data = $this->reportByFaculty($request)->getData()['data'] ?? collect();
                    break;
                case 'department':
                    $data = $this->reportByDepartment($request)->getData()['data'] ?? collect();
                    break;
                case 'level':
                    $data = $this->reportByLevel($request)->getData()['data'] ?? collect();
                    break;
                case 'student':
                    $data = $this->reportByStudent($request)->getData()['data'] ?? collect();
                    break;
            }

            // Pass the generated data to the PDF view
            $pdf = PDF::loadView("staff.bursary.reports.exports.{$type}", compact('data'));

            return $pdf->download($fileName);
        }

        if ($format === 'xlsx') {
            return Excel::download(new GenericExport($type), $fileName);
        }

        abort(404);
    }

    public function createManual()
    {
        $paymentTypes = PaymentSetting::getPaymentTypes();
        $startYear = 2022;
        $currentYear = now()->year;

        $sessions = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $next = $year + 1;
            $sessions[] = "{$year}/{$next}";
        }

        // Fetch all manual transactions
        $manualTransactions = Transaction::with('user')
            ->where('payment_method', 'manual')
            ->latest()
            ->paginate(10);

        return view('staff.bursary.create-transaction', compact('paymentTypes', 'sessions', 'manualTransactions'));
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string',
        ]);

        // Try to locate the user by username, email, or matric_no
        $user = User::where('username', $validated['identifier'])
            ->orWhere('email', $validated['identifier'])
            ->first();
        if (! $user) {
            return back()->with('error', 'The student with that email or username or matric number does not exist in our record.')->withInput();
        }

        // Create new transaction
        $transaction = new Transaction;
        $transaction->refernce_number = strtolower($validated['payment_type']).'_'.Transaction::generateReferenceNumber();
        $transaction->user_id = $user->id;
        $transaction->payment_type = $validated['payment_type'];
        $transaction->amount = $validated['amount'];
        $transaction->payment_status = 0;
        $transaction->payment_method = 'manual';
        $transaction->session = $validated['session'];
        $transaction->description = 'manual upload by bursary'; // optional column if you want to track
        $transaction->save();

        return redirect()
            ->route('bursary.transactions')
            ->with('success', 'Manual transaction recorded successfully.');
    }

    public function updateManual(Request $request, Transaction $transaction)
    {
        if ($transaction->payment_method !== 'manual') {
            return back()->with('error', 'Only manual transactions can be updated.');
        }

        $validated = $request->validate([
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'session' => 'required|string',
            'payment_status' => 'required|in:0,1,2',
        ]);

        $transaction->update($validated);

        return back()->with('success', 'Manual transaction updated successfully.');
    }

    public function destroyManual(Transaction $transaction)
    {
        if ($transaction->payment_method !== 'manual') {
            return back()->with('error', 'Only manual transactions can be deleted.');
        }
        $transaction->delete();

        return back()->with('success', 'Manual transaction deleted successfully.');
    }
}

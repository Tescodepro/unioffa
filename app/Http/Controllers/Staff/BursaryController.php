<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Transaction, PaymentSetting, Student, Faculty, Department, User, Campus};
use App\Services\PaymentVerificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{TransactionsExport, GenericExport};

class BursaryController extends Controller
{
    public function dashboard()
    {
        $title = 'Bursary Dashboard';

        // Basic payment stats
        $stats = [
            'total_collected' => Transaction::where('payment_status', 1)->sum('amount'),
            'pending_payments' => Transaction::where('payment_status', 0)->count(),
            'failed_payments' => Transaction::where('payment_status', 2)->count(),
            'total_transactions' => Transaction::where('payment_status', 1)->count(),
        ];

        // Group transactions by payment_type
        $paymentsByType = Transaction::select(
            'payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('payment_status', '1')
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
            'campuses.name as campus_name',
            'transactions.payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(transactions.amount) as total_amount')
        )
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->join('campuses', 'users.campus_id', '=', 'campuses.id')
            ->where('transactions.payment_status', 1)
            ->where(function ($q) {
                $q->where('transactions.payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('transactions.payment_type', 'technical')
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-01-15' AND '2026-01-17'")
                            ->whereRaw("transactions.created_at NOT BETWEEN '2026-02-06' AND '2026-02-09'");
                    });
            })
            ->groupBy('campuses.name', 'transactions.payment_type')
            ->orderBy('campuses.name')
            ->orderBy('transactions.payment_type')
            ->get();

        // Pivot: [ campus_name => [ payment_type => ['total' => x, 'amount' => y], ...], ... ]
        // Also collect all payment types seen across all campuses
        $campusBreakdown = [];
        $allPaymentTypes = [];

        foreach ($campusBreakdownRaw as $row) {
            $campusBreakdown[$row->campus_name][$row->payment_type] = [
                'total' => $row->total,
                'amount' => $row->total_amount,
            ];
            if (!in_array($row->payment_type, $allPaymentTypes)) {
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
            'recentTransactions'
        ));
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

        if ($request->filled('campus_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('campus_id', $request->campus_id);
            });
        }

        $transactions = $query->latest()->paginate(20);

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

        if (!$transaction) {
            return back()->with('error', 'No transaction found for that reference number.');
        }

        // Always verify from Paystack gateway
        try {
            $paymentService = new \App\Services\PaymentService('paystack');
            $verifyResponse = $paymentService->verifyPayment($reference);
        } catch (\Exception $e) {
            return back()->with('error', 'Verification failed: ' . $e->getMessage());
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
                    'payer_name' => $rawData['customer']['first_name'] ?? ($transaction->user->first_name . ' ' . $transaction->user->last_name ?? 'N/A'),
                    'payer_email' => $rawData['customer']['email'] ?? ($transaction->user->email ?? 'N/A'),
                    'amount' => ($rawData['amount'] ?? 0) / 100, // Convert from kobo
                    'reference' => $rawData['reference'] ?? $reference,
                    'status' => 'success',
                    'gateway_response' => $rawData['gateway_response'] ?? 'Approved',
                    'paid_at' => $rawData['paid_at'] ?? 'N/A',
                    'channel' => $rawData['channel'] ?? 'N/A',
                ]
            ]);
        } else {
            // Payment not successful
            return back()->with([
                'error' => 'Payment verification failed.',
                'verifyData' => [
                    'payer_name' => $rawData['customer']['first_name'] ?? ($transaction->user->first_name . ' ' . $transaction->user->last_name ?? 'N/A'),
                    'payer_email' => $rawData['customer']['email'] ?? ($transaction->user->email ?? 'N/A'),
                    'amount' => $transaction->amount ?? '0.00',
                    'reference' => $reference,
                    'status' => 'failed',
                    'gateway_response' => $rawData['gateway_response'] ?? ($verifyResponse['message'] ?? 'Payment not confirmed'),
                    'paid_at' => 'N/A',
                    'channel' => $rawData['channel'] ?? 'N/A',
                ]
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

        $verifier = new PaymentVerificationService();
        $verifyResponse = $verifier->verify($txn->refernce_number);

        $txn->refresh();
        $txn->verification_message = $verifyResponse['message'] ?? 'Verification completed.';
        $txn->save();

        return back()->with('success', 'Transaction verified successfully.');
    }
    //  REPORT BY FACULTY
    public function reportByFaculty()
    {
        $faculties = Faculty::with(['departments.students.user.transactions'])->get();

        $data = $faculties->map(function ($faculty) {
            $transactions = collect();
            $expected = 0;

            foreach ($faculty->departments as $department) {
                foreach ($department->students as $student) {
                    if ($student->user && $student->user->transactions) {
                        $transactions = $transactions->merge($student->user->transactions);
                    }

                    // Calculate expected fees specifically for this student
                    $expected += PaymentSetting::getFeesForStudent($student)->sum('amount');
                }
            }

            $totalReceived = $transactions->where('payment_status', 1)->sum('amount');
            $totalTransactions = $transactions->count();

            return [
                'faculty' => $faculty->faculty_code ?? $faculty->name,
                'total_transactions' => $totalTransactions,
                'expected' => $expected,
                'received' => $totalReceived,
                'outstanding' => $expected - $totalReceived,
            ];
        });

        return view('staff.bursary.reports.by_faculty', compact('data'));
    }
    //  REPORT BY DEPARTMENT
    public function reportByDepartment()
    {
        $departments = Department::with(['students.user.transactions'])->get();

        $data = $departments->map(function ($dept) {
            $transactions = collect();
            $expected = 0;

            foreach ($dept->students as $student) {
                if ($student->user && $student->user->transactions) {
                    $transactions = $transactions->merge($student->user->transactions);
                }

                // Calculate expected fees specifically for this student
                $expected += PaymentSetting::getFeesForStudent($student)->sum('amount');
            }

            $totalReceived = $transactions->where('payment_status', 1)->sum('amount');
            $totalTransactions = $transactions->count();

            return [
                'faculty' => $dept->faculty?->faculty_code ?? 'N/A',
                'department' => $dept->department_code ?? $dept->name,
                'total_transactions' => $totalTransactions,
                'expected' => $expected,
                'received' => $totalReceived,
                'outstanding' => $expected - $totalReceived,
            ];
        });

        return view('staff.bursary.reports.by_department', compact('data'));
    }

    //  REPORT BY LEVEL
    public function reportByLevel()
    {
        $levels = PaymentSetting::select('level')->distinct()->pluck('level');
        $data = [];
        $processedLevels = []; // Track processed levels to avoid duplicates

        foreach ($levels as $levelData) {
            // Handle both array (from model casting) and JSON string
            if (is_array($levelData)) {
                $levelsArray = $levelData;
            } elseif (is_string($levelData)) {
                $levelsArray = json_decode($levelData, true);
            } else {
                continue;
            }

            if (!is_array($levelsArray)) {
                continue;
            }

            foreach ($levelsArray as $level) {
                // Skip if we've already processed this level
                if (in_array($level, $processedLevels)) {
                    continue;
                }
                $processedLevels[] = $level;

                $studentsInLevel = \App\Models\Student::with('user.transactions')->where('level', $level)->get();

                $expected = 0;
                $received = 0;

                foreach ($studentsInLevel as $student) {
                    $expected += PaymentSetting::getFeesForStudent($student)->sum('amount');

                    if ($student->user && $student->user->transactions) {
                        $received += $student->user->transactions->where('payment_status', 1)->sum('amount');
                    }
                }

                $data[] = [
                    'level' => $level,
                    'expected' => $expected,
                    'received' => $received,
                    'outstanding' => $expected - $received,
                ];
            }
        }

        return view('staff.bursary.reports.by_level', compact('data'));
    }

    //  REPORT BY STUDENT
    public function reportByStudent()
    {
        $students = \App\Models\Student::with(['user.transactions', 'department.faculty'])->get();

        $data = $students->map(function ($student) {
            $expected = PaymentSetting::getFeesForStudent($student)->sum('amount');

            $received = 0;
            if ($student->user && $student->user->transactions) {
                $received = $student->user->transactions->where('payment_status', 1)->sum('amount');
            }

            return [
                'student_name' => $student->user?->full_name ?? 'N/A',
                'matric_number' => $student->matric_no ?? 'N/A',
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

        return view('staff.bursary.reports.by_student', compact('data'));
    }

    //  EXPORT HANDLER
    public function export($type, $format)
    {
        $fileName = "report_{$type}." . $format;

        if ($format === 'pdf') {
            $pdf = PDF::loadView("staff.bursary.reports.exports.{$type}");
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
        if (!$user) {
            return back()->with('error', 'The student with that email or username or matric number does not exist in our record.')->withInput();
        }

        // Create new transaction
        $transaction = new Transaction();
        $transaction->refernce_number = strtolower($validated['payment_type']) . '_' . Transaction::generateReferenceNumber();
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

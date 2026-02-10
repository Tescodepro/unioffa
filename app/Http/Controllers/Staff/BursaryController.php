<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Transaction, PaymentSetting, Student, Faculty, Department, User};
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


        return view('staff.bursary.dashboard', compact('title', 'stats', 'paymentsByType', 'recentTransactions'));
    }
    public function transactions(Request $request)
    {
        $query = Transaction::query()
            ->with('user')
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

        $transactions = $query->latest()->paginate(20);

        $paymentTypes = PaymentSetting::select('payment_type')->distinct()->pluck('payment_type');


        return view('staff.bursary.transactions', compact('transactions', 'paymentTypes'));
    }
    public function exportTransactions(Request $request, $format)
    {
        $query = Transaction::query()
            ->with('user')
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

            foreach ($faculty->departments as $department) {
                foreach ($department->students as $student) {
                    if ($student->user && $student->user->transactions) {
                        $transactions = $transactions->merge($student->user->transactions);
                    }
                }
            }

            $totalReceived = $transactions->where('payment_status', 1)->sum('amount');
            $totalTransactions = $transactions->count();
            $expected = PaymentSetting::where('faculty_id', $faculty->id)->sum('amount');

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

            foreach ($dept->students as $student) {
                if ($student->user && $student->user->transactions) {
                    $transactions = $transactions->merge($student->user->transactions);
                }
            }

            $totalReceived = $transactions->where('payment_status', 1)->sum('amount');
            $totalTransactions = $transactions->count();
            $expected = PaymentSetting::where('department_id', $dept->id)->sum('amount');

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

                $expected = PaymentSetting::whereJsonContains('level', $level)->sum('amount');

                // Get received amount by joining transactions with users and students
                $received = Transaction::join('users', 'transactions.user_id', '=', 'users.id')
                    ->join('students', 'students.user_id', '=', 'users.id')
                    ->where('students.level', $level)
                    ->where('transactions.payment_status', 1)
                    ->sum('transactions.amount');

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
        $transactions = Transaction::with(['user.student.department.faculty'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $transactions->map(function ($txn) {
            return [
                'student_name' => $txn->user?->full_name ?? 'N/A',
                'matric_number' => $txn->user?->student?->matric_number ?? 'N/A',
                'faculty' => $txn->user?->student?->department?->faculty?->faculty_code ?? 'N/A',
                'department' => $txn->user?->student?->department?->department_code ?? 'N/A',
                'amount' => $txn->amount,
                'status' => ucfirst($txn->status ?? 'unknown'),
                'reference' => $txn->refernce_number,
                'date' => $txn->created_at?->format('Y-m-d') ?? 'N/A',
            ];
        });

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

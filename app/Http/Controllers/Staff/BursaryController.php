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
                            ->whereNotBetween('created_at', ['2025-11-28', '2025-12-05'])
                            ->whereNotBetween('created_at', ['2025-12-10', '2025-12-20']);
                    });
            })
            ->groupBy('payment_type')
            ->get();


        $recentTransactions = Transaction::with('user')
            ->where(function ($q) {
                $q->where('payment_type', '!=', 'technical')
                    ->orWhere(function ($tq) {
                        $tq->where('payment_type', 'technical')
                            ->whereNotBetween('created_at', ['2025-11-28', '2025-12-05'])
                            ->whereNotBetween('created_at', ['2025-12-10', '2025-12-20']);
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
                        ->whereNotBetween('created_at', ['2025-11-28', '2025-12-05'])
                        ->whereNotBetween('created_at', ['2025-12-10', '2025-12-20']);
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
                        ->whereNotBetween('created_at', ['2025-11-28', '2025-12-05'])
                        ->whereNotBetween('created_at', ['2025-12-10', '2025-12-20']);
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

        $transaction = Transaction::where('refernce_number', $request->reference)->first();

        if (!$transaction) {
            return back()->with('error', 'No transaction found for that reference number.');
        }

        $verifier = new PaymentVerificationService();
        $verifyResponse = $verifier->verify($transaction->refernce_number);
        // dd($verifyResponse);

        // Update transaction info
        $transaction->refresh();
        $transaction->verification_message = $verifyResponse['message'] ?? 'Verification completed.';
        $transaction->save();

        // Pass verification data to view
        return back()->with([
            'success' => 'Payment verification completed successfully.',
            'verifyData' => [
                'payer_name' => $transaction->payer_name ?? 'N/A',
                'payer_email' => $transaction->payer_email ?? 'N/A',
                'amount' => $transaction->amount ?? '0.00',
                'reference' => $transaction->refernce_number,
                'status' => $verifyResponse['data']['status'] ?? 'unknown',
                'gateway_response' => $verifyResponse['data']['gateway_response'] ?? 'No response',
                'paid_at' => $verifyResponse['data']['paid_at'] ?? 'N/A',
                'channel' => $verifyResponse['data']['channel'] ?? 'N/A',
            ]
        ]);
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

            $totalReceived = $transactions->where('status', 'success')->sum('amount');
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

            $totalReceived = $transactions->where('status', 'success')->sum('amount');
            $totalTransactions = $transactions->count();
            $expected = PaymentSetting::where('department_id', $dept->id)->sum('amount');

            return [
                'faculty' => $dept->faculty->faculty_code ?? '',
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

        foreach ($levels as $levelsJson) {
            $levelsArray = json_decode($levelsJson, true);
            if (!is_array($levelsArray))
                continue;

            foreach ($levelsArray as $level) {
                $expected = PaymentSetting::whereJsonContains('level', $level)->sum('amount');
                $received = Transaction::where('level', $level)
                    ->where('status', 'success')
                    ->sum('amount');

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
                'student_name' => $txn->user->name ?? 'N/A',
                'matric_number' => $txn->user->student->matric_number ?? 'N/A',
                'faculty' => $txn->user->student->department->faculty->faculty_code ?? 'N/A',
                'department' => $txn->user->student->department->department_code ?? 'N/A',
                'amount' => $txn->amount,
                'status' => ucfirst($txn->status),
                'reference' => $txn->reference_number,
                'date' => $txn->created_at->format('Y-m-d'),
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
        $transaction->description = 'manual upload by burser'; // optional column if you want to track
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

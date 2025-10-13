<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Transaction, PaymentSetting, Student};
use App\Services\PaymentVerificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

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
            'total_transactions' => Transaction::count(),
        ];

        // Group transactions by payment_type
        $paymentsByType = Transaction::select(
            'payment_type',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy('payment_type')
            ->get();

        // Latest 5 transactions
        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('staff.bursary.dashboard', compact('title', 'stats', 'paymentsByType', 'recentTransactions'));
    }
    public function transactions(Request $request)
    {
        $query = Transaction::query()->with('user');

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


        return view('staff.bursary.transactions', compact('transactions'));
    }

    public function exportTransactions(Request $request, $format)
    {
        $query = Transaction::with('user');

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

        if (! $transaction) {
            return back()->with('error', 'No transaction found for that reference number.');
        }

        $verifier = new PaymentVerificationService();
        $verifyResponse = $verifier->verify($transaction->refernce_number);

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

    public function summary()
    {
        $summary = PaymentSetting::select('payment_type', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_type')
            ->get();

        $studentsCount = Student::count();
        $expectedTotals = $summary->map(function ($item) use ($studentsCount) {
            $item->expected_total = $studentsCount * $item->total_amount;
            return $item;
        });

        return view('staff.bursary.summary', compact('expectedTotals'));
    }

    public function report()
    {
        return view('staff.bursary.report');
    }

    public function settings()
    {
        $settings = PaymentSetting::latest()->get();
        return view('staff.bursary.settings', compact('settings'));
    }

    public function storeSetting(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|string',
            'amount' => 'required|numeric',
            'session' => 'required|string',
        ]);

        PaymentSetting::updateOrCreate(
            [
                'payment_type' => $request->payment_type,
                'session' => $request->session,
            ],
            $request->only('faculty_id', 'department_id', 'level', 'sex', 'amount', 'student_type', 'description')
        );

        return back()->with('success', 'Payment setting updated successfully.');
    }
}

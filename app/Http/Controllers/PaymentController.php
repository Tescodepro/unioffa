<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Str;
use App\Models\Transaction;

class PaymentController extends Controller
{
    /**
     * Initiate a payment
     */
    public function initiatePayment(Request $request)
    {
        $gateway = $request->input('gateway', 'oneapp'); // default to oneapp
        $paymentService = new PaymentService($gateway);

        $user = $request->user();

        // Generate unique reference number
        $reference = $this->generateReference($request->fee_type);

        // Log transaction as pending
        $transaction = Transaction::create([
            'id'              => Str::uuid(),
            'user_id'         => $user->id,
            'description'     => $request->fee_type ?? 'Payment',
            'refernce_number' => $reference,
            'amount'          => $request->amount,
            'payment_status'  => 0, // pending
            'payment_type'    => $request->fee_type ?? 'tuition',
            'payment_method'  => $gateway,
            'session'         => activeSession()->name ?? '---',
            'meta_data'       => json_encode([
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]),
        ]);

        // Prepare gateway data
        $data = [
            'amount'       => $request->amount,
            'email'        => $user->email,
            'name'         => $user->name,
            'reference'    => $reference,
            'callback_url' => route('payment.callback'),
            'metadata'     => [
                'user_id'        => $user->id,
                'fee_type'       => $request->fee_type,
                'transaction_id' => $transaction->id,
            ],
        ];

        // Generate payment link
        $response = $paymentService->generatePaymentLink($data);
        // dd($response);

        if ($response['status'] && !empty($response['checkout_url'])) {
            return redirect()->away($response['checkout_url']);
        }

        return back()->with('error', $response['message'] ?? 'Unable to start payment');
    }

    /**
     * Handle gateway callback
     */
    public function handleCallback(Request $request)
    {
        $gateway = $request->input('gateway', 'oneapp'); // default
        $paymentService = new PaymentService($gateway);

        if ($request->has('reference')) {
            $reference = $request->input('reference');
        } elseif ($request->has('transref')) {
            $reference = $request->input('transref');
        } else {
            return redirect()->route('payment.status.page')->with('error', 'Invalied payment: reference is missing');
        }
        // Verify payment
        $response = $paymentService->verifyPayment($reference);

        // Find the transaction
        $transaction = Transaction::where('refernce_number', $reference)->first();

        // get payment type
        $paymentType = $transaction->payment_type;
        // decide redirect route
        if (in_array($paymentType, ['application', 'acceptance'])) {
            $backRoute = route('application.dashboard');
        } else {
            $backRoute = route('students.dashboard');
        }

        if ($response['success']) {
            
            if ($transaction) {
                $transaction->update(['payment_status' => 1]); // success
            }
            return view('payment-status-page', compact('paymentType', 'transaction', 'backRoute'))->with('success', 'Payment successful');
        }

        if ($transaction) {
            $transaction->update(['payment_status' => 2]); // failed
        }

        return view('payment-status-page', compact('paymentType', 'transaction', 'backRoute'))->with('error', 'Payment failed or canceled');
    }

    /**
     * Show payment status message
     */
    public function paymentStatusPage()
    {
        return view('payment-status-page');
    }

    private function generateReference($payment_type): string
    {
        $reference = $payment_type . '-' . date('YmdHis') . '-' . Str::uuid()->toString();
        return $reference;
    } 
}

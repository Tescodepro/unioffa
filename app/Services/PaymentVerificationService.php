<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationService
{
    protected string $baseUrl;
    protected string $secret;

    public function __construct()
    {
        $this->baseUrl = env('PAYSTACK_BASE_URL', 'https://api.paystack.co');
        $this->secret = env('PAYSTACK_AUTH_KEY');
    }

    /**
     * Verify a Paystack transaction by reference and sync to local DB
     */
    public function verify(string $reference): array
    {
        try {
            $response = Http::withToken($this->secret)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}")
                ->json();

            // Basic safety checks
            if (!isset($response['status'])) {
                return [
                    'payment_status' => 0,
                    'message' => 'Invalid response from Paystack server.',
                ];
            }

            $isSuccess = $response['status'] === true
                && isset($response['data']['status'])
                && $response['data']['status'] === 'success';

            $transaction = Transaction::where('refernce_number', $reference)->first();

            // If no record found locally
            if (!$transaction) {
                return [
                    'payment_status' => 0,
                    'message' => 'Transaction not found in the system.',
                    'payment_purpose' => null,
                    'reference' => $reference,
                ];
            }

            // Prevent duplicate confirmation
            if ($transaction->payment_status == 1) {
                return [
                    'payment_status' => 1,
                    'message' => 'Payment already verified previously.',
                    'payment_purpose' => $transaction->payment_type,
                    'date_initiated' => $transaction->created_at->toDateTimeString(),
                ];
            }

            // Update local transaction
            if ($isSuccess) {
                $transaction->update([
                    'payment_status' => 1,
                    'payment_method' => 'paystack',
                ]);

                return [
                    'payment_status' => 1,
                    'message' => 'Payment verified successfully.',
                    'payment_purpose' => $transaction->payment_type,
                    'date_initiated' => $transaction->created_at->toDateTimeString(),
                    'amount' => $transaction->amount,
                    'reference' => $reference,
                ];
            } else {
                $transaction->update(['payment_status' => 2]);
                return [
                    'payment_status' => 2,
                    'message' => 'Payment not successful.',
                    'payment_purpose' => $transaction->payment_type,
                    'date_initiated' => $transaction->created_at->toDateTimeString(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Paystack verification failed: ' . $e->getMessage());

            return [
                'payment_status' => 0,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ];
        }
    }
}

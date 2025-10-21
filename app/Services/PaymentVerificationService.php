<?php

namespace App\Services;

use App\Models\{Transaction, Student};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

                // Get related student and department
                $student = $transaction->student; // Assuming you have a student relationship
                $department = $student->department; // Assuming department relationship
                $paymentType = $transaction->payment_type;

                // Handle tuition payment - Generate matric number if needed
                if ($paymentType == 'tuition' && !Student::hasMatricNumber()) {
                    // Extract admission year as integer
                    $year = (int) \Carbon\Carbon::parse($student->admission_date)->year;
                    // Generate new matric number
                    $newMatricNo = Student::generateMatricNo(
                        $department->department_code,
                        $year,
                        $student->entry_mode
                    );

                    // Wrap in DB transaction for safety
                    DB::transaction(function () use ($student, $newMatricNo) {
                        // Update student's matric number
                        $student->update([
                            'matric_no' => $newMatricNo,
                        ]);
                        // Update related user's username (or any field that stores the matric)
                        $student->user->update([
                            'username' => $newMatricNo,
                        ]);
                    });
                    // Optional: Log or notify
                    Log::info("Matric number generated for student {$student->id}: {$newMatricNo}");
                }

                // Handle acceptance payment - Migrate student
                if ($paymentType == 'acceptance') {
                    $studentMigrationService = new StudentMigrationService();
                    $studentMigrationService->migrate($transaction->user_id); // ✅ CORRECT
                }

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

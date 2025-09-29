<?php

namespace App\Services;

use App\Models\PaymentSetting;
use App\Models\Transaction;

class PaymentStatusService
{
    /**
     * Get payment status for a student grouped by payment_type.
     *
     * @param  \App\Models\Student  $student
     * @param  string  $session
     * @return array
     */
    public function getStatus($student, string $session): array
    {
        $paymentSettings = PaymentSetting::query()
            ->where('student_type', $student->programme)
            ->whereJsonContains('level', (int) $student->level)
            ->where('session', $session)
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
                    ->orWhere('matric_number', $student->matric_number);
            })
            ->get();

        if ($paymentSettings->isEmpty()) {
            return [];
        }

        $transactions = Transaction::query()
            ->where('user_id', $student->user_id)
            ->where('session', $session)
            ->where('payment_status', 1)
            ->get()
            ->groupBy('payment_type');

        $result = [];

        foreach ($paymentSettings as $payment) {
            $txns = $transactions->get($payment->payment_type, collect());
            $amountPaid = $txns->sum('amount');
            $balance = max($payment->amount - $amountPaid, 0);

            $data = [
                'payment_type'   => $payment->payment_type,
                'description'    => $payment->description,
                'amount'         => (int) $payment->amount,
                'amount_paid'    => (int) $amountPaid,
                'balance'        => (int) $balance,
                'status'         => $balance <= 0 ? 'PAID' : 'PENDING',
            ];

            if ($payment->payment_type === 'tuition') {
                $data['percentage_paid'] = $payment->amount > 0
                    ? round(($amountPaid / $payment->amount) * 100, 2)
                    : 0;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * Check if student has cleared all assigned payments.
     */
    public function hasClearedAll($student, string $session): bool
    {
        $status = $this->getStatus($student, $session);
        return collect($status)->every(fn($p) => $p['balance'] <= 0);
    }

    /**
     * Get total outstanding balance across all payments.
     */
    public function getTotalOutstanding($student, string $session): int
    {
        $status = $this->getStatus($student, $session);
        return collect($status)->sum('balance');
    }
}

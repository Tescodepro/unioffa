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
     */
    public function getStatus($student, string $session): array
    {
        if (($student->entry_mode == 'DE' or $student->entry_mode == 'TRANSFER') and ($student->level == 200 or $student->level == 300) and $student->admission_session == $session) {
            $level_payment = 100;
        } else {
            $level_payment = $student->level;
        }

        $activeSemester = activeSemester();
        $currentSemester = $activeSemester?->code;
        $studentIsSemesterAffected = $student->isSemesterAffected($activeSemester);

        // 1. Fetch payment settings using the centralized model logic
        $allSettings = PaymentSetting::getFeesForStudent($student, $session);

        // 2. Apply semester filtering
        $paymentSettings = $allSettings->filter(function ($payment) use ($studentIsSemesterAffected, $currentSemester) {
            if ($studentIsSemesterAffected) {
                // If student matches a specific semester override → ONLY that semester's fees
                return ! empty($payment->semesters) && in_array($currentSemester, $payment->semesters);
            } else {
                // If student is NOT semester-affected → ONLY session-wide fees (empty semesters)
                return empty($payment->semesters);
            }
        });

        // 3. Exclude accommodation and maintenance (handled separately)
        $paymentSettings = $paymentSettings->whereNotIn('payment_type', ['accommodation', 'maintenance']);

        if ($paymentSettings->isEmpty()) {
            return [];
        }

        // Filter transactions by semester when student is semester-affected
        $transactions = Transaction::query()
            ->where('user_id', $student->user_id)
            ->where('session', $session)
            ->when($studentIsSemesterAffected, function ($q) use ($activeSemester) {
                $q->where('semester', $activeSemester->code);
            })
            ->where('payment_status', 1)
            ->get()
            ->groupBy('payment_type');

        $result = [];

        foreach ($paymentSettings as $payment) {
            $txns = $transactions->get($payment->payment_type, collect());
            $amountPaid = $txns->sum('amount');
            $balance = max($payment->amount - $amountPaid, 0);

            $percentage = $payment->amount > 0 ? ($amountPaid / $payment->amount) * 100 : 0;
            $isRegularOrDiploma = in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA']);

            // Skip tuition if paid up to or beyond threshold
            if ($payment->payment_type === 'tuition' && $payment->amount > 0) {
                $threshold = 100; // Requirement is 100% for all
                if ($percentage >= $threshold) {
                    continue;
                }
            }

            $data = [
                'payment_type' => $payment->payment_type,
                'description' => $payment->description,
                'amount' => (int) $payment->amount,
                'amount_paid' => (int) $amountPaid,
                'balance' => (int) $balance,
                'status' => $balance <= 0 ? 'PAID' : 'PENDING',
                'is_compulsory' => (bool) $payment->is_compulsory,
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

        return collect($status)->every(fn ($p) => $p['balance'] <= 0);
    }

    /**
     * Check if student has cleared all COMPULSORY assigned payments.
     */
    public function hasClearedCompulsory($student, string $session): bool
    {
        $status = $this->getStatus($student, $session);

        return collect($status)
            ->filter(fn ($p) => $p['is_compulsory'])
            ->every(fn ($p) => $p['balance'] <= 0);
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

<?php

namespace App\Services;

use App\Models\LatePaymentSetting;
use App\Models\Student;
use App\Models\Transaction;

class LatePaymentService
{
    /**
     * Checks if a specific payment type for a student is currently blocked
     * by an outstanding late penalty.
     *
     * @return array [
     *               'has_penalty' => bool,
     *               'penalty_amount' => numeric,
     *               'is_cleared' => bool,
     *               'setting_id' => string|null,
     *               'late_fee_type' => string|null
     *               ]
     */
    public function checkPenalty(Student $student, string $paymentType, string $session, string $semester)
    {
        // 1. Get the applicable late payment setting for this fee
        $setting = LatePaymentSetting::getActiveForStudent($student, $session, $semester, $paymentType);

        // 2. If no setting exists, or the deadline hasn't passed yet, there's no penalty
        if (! $setting || now()->lte($setting->closing_date)) {
            return [
                'has_penalty' => false,
                'penalty_amount' => 0,
                'is_cleared' => true,
                'setting_id' => null,
                'late_fee_type' => null,
                'increment_date' => null,
                'increment_amount' => null,
            ];
        }

        // 3. The deadline has passed. Check if the user has paid the penalty charge yet.
        $lateFeeType = $paymentType.'_late_payment';

        $activePenaltyAmount = $setting->late_fee_amount;
        if ($setting->increment_date && now()->gt($setting->increment_date) && $setting->increment_amount > 0) {
            $activePenaltyAmount = $setting->increment_amount;
        }

        $hasPaidLateFee = Transaction::where('user_id', $student->user_id)
            ->where('payment_type', $lateFeeType)
            ->where('payment_status', 1) // 1 is successful in this system
            ->where('session', $session)
            // Sometimes late fees cover a whole session, so checking semester might overly restrict,
            // but for safety we'll bind it strictly to what the setting defined
            ->when($setting->semester, function ($q) use ($setting) {
                $q->where('semester', $setting->semester);
            })
            ->exists();

        return [
            'has_penalty' => true,
            'penalty_amount' => $activePenaltyAmount,
            'is_cleared' => $hasPaidLateFee,
            'setting_id' => $setting->id,
            'late_fee_type' => $lateFeeType,
            'increment_date' => $setting->increment_date ? $setting->increment_date->toIso8601String() : null,
            'increment_amount' => $setting->increment_amount,
        ];
    }
}

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

        // Determine if student is semester-affected based on the active semester overrides
        $activeSemester = activeSemester();
        $currentSemester = $activeSemester?->code;

        $isSpecificOverride = false;
        $studentIsSemesterAffected = false;
        if ($currentSemester && $activeSemester) {
            $semesterStreams = $activeSemester->stream ?? [];
            $semesterCampuses = $activeSemester->campus_id ?? [];
            $semesterProgrammes = $activeSemester->programme ?? [];

            $isSpecificOverride = ! empty($semesterStreams) || ! empty($semesterCampuses) || ! empty($semesterProgrammes);

            $matchesStream = empty($semesterStreams) || in_array((string) $student->stream, $semesterStreams);
            $matchesCampus = empty($semesterCampuses) || in_array($student->campus_id, $semesterCampuses);
            $matchesProgramme = empty($semesterProgrammes) || in_array($student->programme, $semesterProgrammes);

            $studentIsSemesterAffected = $isSpecificOverride && $matchesStream && $matchesCampus && $matchesProgramme;
        }

        $paymentSettings = PaymentSetting::query()
            ->where(function ($q) use ($student) {
                $q->whereNull('student_type')
                    ->orWhereJsonContains('student_type', $student->programme);
            })
            ->when(strtoupper($student->entry_mode) === 'TRANSFER', function ($query) {
                $query->where('payment_type', '!=', 'matriculation');
            })
            ->whereJsonContains('level', (int) $level_payment)
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
            ->where(function ($q) use ($student) {
                $q->whereNull('entry_mode')
                    ->orWhereJsonContains('entry_mode', $student->entry_mode);
            })
            ->where(function ($q) use ($studentIsSemesterAffected, $currentSemester) {
                if ($studentIsSemesterAffected) {
                    // Student matched a specific semester override → ONLY that semester's fees
                    $q->where('semester', $currentSemester);
                } else {
                    // Student is on global semester → session-wide fees only
                    $q->whereNull('semester');
                }
            })
            ->whereNotIn('payment_type', ['accommodation', 'maintenance']) // 🚫 exclude these
            ->get();

        if ($paymentSettings->isEmpty()) {
            return [];
        }

        // Filter transactions by semester when student is semester-affected
        $transactions = Transaction::query()
            ->where('user_id', $student->user_id)
            ->where('session', $session)
            ->when($studentIsSemesterAffected, function ($q) use ($activeSemester) {
                $q->where('semester', $activeSemester->name);
            })
            ->where('payment_status', 1)
            ->get()
            ->groupBy('payment_type');

        $result = [];

        foreach ($paymentSettings as $payment) {
            $txns = $transactions->get($payment->payment_type, collect());
            $amountPaid = $txns->sum('amount');
            $balance = max($payment->amount - $amountPaid, 0);

            // Skip tuition if paid up to or beyond 56%
            if (
                $payment->payment_type === 'tuition' &&
                $payment->amount > 0 &&
                (($amountPaid / $payment->amount) * 100) >= 56
            ) {
                continue;
            }

            $data = [
                'payment_type' => $payment->payment_type,
                'description' => $payment->description,
                'amount' => (int) $payment->amount,
                'amount_paid' => (int) $amountPaid,
                'balance' => (int) $balance,
                'status' => $balance <= 0 ? 'PAID' : 'PENDING',
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
     * Get total outstanding balance across all payments.
     */
    public function getTotalOutstanding($student, string $session): int
    {
        $status = $this->getStatus($student, $session);

        return collect($status)->sum('balance');
    }
}

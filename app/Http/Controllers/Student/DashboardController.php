<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{AcademicSemester, PaymentSetting, Transaction};

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('student.department.faculty');

        return view('student.dashboard', compact('user'));
    }

    public function loadPayment()
    {
        $user = Auth::user()->load('student.department.faculty');
        $currentSession = activeSession()->name ?? null;

        // Check if student profile and session exist
        if (!$user->student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        if (!$currentSession) {
            return redirect()->back()->with('error', 'No active session found.');
        }

        // Load required payments
        $paymentSettings = PaymentSetting::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('faculty_id')
                  ->orWhere('faculty_id', $user->faculty_id);
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('department_id')
                  ->orWhere('department_id', $user->student->department_id);
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('level')
                  ->orWhere('level', $user->student->level);
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('sex')
                  ->orWhere('sex', $user->student->sex);
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('matric_number')
                  ->orWhere('matric_number', $user->username);
            })
            ->get()
            ->map(function ($payment) use ($user, $currentSession) {
                // Calculate amount paid for current session and completed status
                $payment->amount_paid = Transaction::where('user_id', $user->id)
                    ->where('payment_type', $payment->payment_type)
                    ->where('payment_status', '1')
                    ->where('session', $currentSession)
                    ->sum('amount');
                $payment->balance = $payment->amount - $payment->amount_paid;

                // Count completed tuition installments for this payment
                if ($payment->payment_type === 'tuition') {
                    $payment->installment_count = Transaction::where('user_id', $user->id)
                        ->where('payment_type', 'tuition')
                        ->where('payment_status', '1')
                        ->where('session', $currentSession)
                        ->count();
                } else {
                    $payment->installment_count = 0;
                }

                return $payment;
            });

        // Load transactions for current session and completed status
        $transactions = Transaction::where('user_id', $user->id)
            ->where('payment_status', '1')
            ->where('session', $currentSession)
            ->latest()
            ->get();

        return view('student.payment', compact('paymentSettings', 'transactions', 'currentSession'));
    }

}

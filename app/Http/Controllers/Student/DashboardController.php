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

        // 1. Load required payments (admin-defined rules)
        $paymentSettings = PaymentSetting::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('faculty_id')
                ->orWhere('faculty_id', $user->faculty_id);
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('department_id')
                ->orWhere('department_id', $user->department_id);
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
            ->get();

        // 2. Load student's payment transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->get();

        // 3. Send data to Blade view
        return view('student.payment', compact('paymentSettings', 'transactions'));
    }

}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\{PaymentSetting, ApplicationSetting};
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
        if (! $user->student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }
        if (! $currentSession) {
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

    public function paymentHistory()
    {
        $user = Auth::user()->load('student.department.faculty');

        // Load all transactions for the user
        $transactions = Transaction::where('user_id', $user->id)
            ->where('payment_status', '1')
            ->latest()
            ->get();

        return view('student.payment-history', compact('transactions'));
    }

    public function downloadAdmissionLetter()
    {
        $user = Auth::user();

        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $department = $student->department;
        if($student->programme == 'IDEL'){
            $student->programme = 'IDELDE';
        }
        $applicationSetting = ApplicationSetting::where('application_code', $student->programme)->first();

        $data = [
            'student' => $student,
            'session' => $student->admission_session,
            'department' => $department,
            'duration' => $applicationSetting,
            'date' => Carbon::now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('student.admission-letter', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download('Admission_Letter_'.$student->full_name.'.pdf');
    }

    public function profile()
    {
        $user = Auth::user()->load('student.department.faculty');

        return view('student.profile', compact('user'));
    }

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (! \Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function logoutAction()
    {
        Auth::logout();

        return redirect()->route('student.login')->with('success', 'Logged out successfully.');
    }


}

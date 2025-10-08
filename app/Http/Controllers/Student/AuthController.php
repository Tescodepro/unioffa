<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\{AcademicSemester, User};

class AuthController extends Controller
{
    public function login()
    {
        return view('student.auth.login');
    }

    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'email_matric_no' => 'required|string',
            'password' => 'required|string',
        ]);

        // Decide which column to use
        $fieldType = filter_var($credentials['email_matric_no'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Build proper credentials array
        $authCredentials = [
            $fieldType => $credentials['email_matric_no'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();

            $to = Auth::user()->email;
            $subject = 'Login Notification';
            $content = [
                'title' => Auth::user()->full_name.',',
                'body' => 'We noticed a login to your Offa University account.<br><br>

            Details:<br>  
            - Date: '.now()->format('Y-m-d H:i:s').'<br>  
            - IP Address: '.request()->ip().' <br><br>

            If this was you, no action is required. If not, please reset your password immediately.',
                'footer' => 'Stay safe,  
            Offa University Security Team',
            ];

            // Mail::to($to)->send(new GeneralMail($subject, $content, false));

            return redirect()->intended(route('students.dashboard'))->with('success', 'You must be logged in.'); // or your home route
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }

    public function forgetPasswordIndex()
    {
        return view('student.auth.forget-password');
    }

    public function forgetPasswordAction(Request $request)
    {
        $validated = $request->validate([
            'matric_number' => 'required|string'
        ]);

        $user = User::where('username', $request->matric_number)->first();
        
        if (!$user) {
            return back()->with('error', 'No account found with this matric number.');
        }

        // Generate OTP
        $otp = $this->generateOtp();
        
        // Store OTP in session
        session(['reset_otp' => $otp]);
        session(['reset_user_id' => $user->id]);
        
        // Send OTP via email
        $to = $user->email;
        $subject = 'Password Reset OTP';
        $content = [
            'title' => $user->full_name.',',
            'body' => "Your OTP for password reset is: {$otp}<br><br>
                      This OTP will expire in 10 minutes.<br>
                      If you didn't request this, please ignore this email.",
            'footer' => 'Best regards,
                        Offa University'
        ];

        Mail::to($to)->send(new GeneralMail($subject, $content, false));

        return redirect()->route('students.auth.change-password')->with('success', 'OTP has been sent to your email.');
    }

    public function verifyOtpIndex()
    {
        if (!session('reset_otp')) {
            return redirect()->route('students.forget-password')->with('error', 'Please request OTP first.');
        }
        return view('student.auth.change-password');
    }

    public function verifyOtpAction(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed'
        ]);


        if (!session('reset_otp') || !session('reset_user_id')) {
            return redirect()->route('students.auth.forget-password')
                ->with('error', 'OTP session has expired. Please request a new one.');
        }

        if ($request->otp != session('reset_otp')) {
            return back()->with('error', 'Invalid OTP entered.');
        }

        // Update user's password
        $user = User::find(session('reset_user_id'));
        $user->password = bcrypt($request->password);
        $user->save();

        // Clear all reset-related session data
        session()->forget(['reset_otp', 'reset_user_id']);

        return redirect()->route('student.login')
            ->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    private function generateOtp()
    {
        return rand(100000, 999999);
    }
}

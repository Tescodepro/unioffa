<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        return view('student.auth.login');
    }

    public function loginAction(Request $request)
    {
        // Validate login input
        $credentials = $request->validate([
            'email_matric_no' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine whether user is logging in with email or matric/username
        $fieldType = filter_var($credentials['email_matric_no'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $authCredentials = [
            $fieldType => $credentials['email_matric_no'],
            'password' => $credentials['password'],
        ];

        // Attempt Login
        if (! Auth::attempt($authCredentials)) {
            return back()->with('error', 'The provided credentials do not match our records.');
        }

        // Retrieve authenticated user
        $user = Auth::user();

        // Ensure student relationship exists
        if (! $user->student) {
            Auth::logout();

            return back()->with('error', 'Account is not linked to a student profile.');
        }

        // Blocked campus slugs
        $blockedSlugs = [
            'ilorin-campus',
            'ogun-campus',
            'igbeti-campus',
        ];

        // Fetch student's campus
        $campus = Campus::find($user->student->center_id);

        if ($campus && in_array($campus->slug, $blockedSlugs)) {
            Auth::logout(); // Security: logout immediately

            return back()->with('error', 'Kindly check back in 7 days time');
        }

        // Regenerate session to protect from fixation
        $request->session()->regenerate();

        // OPTIONAL: Send login notification email
        /*
        $to = $user->email;
        $subject = 'Login Notification';

        $content = [
            'title' => $user->full_name . ',',
            'body' => '
                We noticed a login to your Offa University account.<br><br>
                <strong>Details:</strong><br>
                - Date: ' . now()->format('Y-m-d H:i:s') . '<br>
                - IP Address: ' . request()->ip() . '<br><br>
                If this was you, no action is required.
                If not, please reset your password immediately.
            ',
            'footer' => 'Stay safe,<br>Offa University Security Team',
        ];

        Mail::to($to)->send(new GeneralMail($subject, $content, false));
        */

        return redirect()
            ->intended(route('students.dashboard'))
            ->with('success', 'Login successful.');
    }

    public function forgetPasswordIndex()
    {
        return view('student.auth.forget-password');
    }

    public function forgetPasswordAction(Request $request)
    {
        $validated = $request->validate([
            'matric_number' => 'required|string',
        ]);

        $user = User::where('username', $request->matric_number)->first();

        if (! $user) {
            return back()->with('error', 'No account found with this matric number.');
        }

        // Generate OTP
        $otp = $this->generateOtp();

        // Save OTP in user's record
        $user->otp = $otp;
        $user->otp_expires_at = now(); // optional if you have this column
        $user->save();

        // Send OTP via email
        $to = $user->email;
        $subject = 'Password Reset OTP';
        $content = [
            'title' => $user->full_name.',',
            'body' => "Your OTP for password reset is: {$otp}<br><br>
                  This OTP will expire in 10 minutes.<br>
                  If you didn't request this, please ignore this email.",
            'footer' => 'Best regards,<br>Offa University',
        ];

        Mail::to($to)->send(new GeneralMail($subject, $content, false));

        return redirect()->route('students.auth.change-password')
            ->with('success', "An OTP has been sent to your email address. If you did not receive the email, kindly use this OTP:  $otp .");
    }

    public function verifyOtpIndex()
    {
        return view('student.auth.change-password');
    }

    public function verifyOtpAction(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find user by OTP
        $user = User::where('otp', $request->otp)->first();

        if (! $user) {
            return back()->with('error', 'Invalid or expired OTP.');
        }

        // Optional: check OTP expiration if you store timestamps
        if (isset($user->otp_expires_at) && now()->diffInMinutes($user->otp_expires_at) > 10) {
            return redirect()->route('students.auth.forget-password')
                ->with('error', 'OTP has expired. Please request a new one.');
        }

        // Update user's password
        $user->password = Hash::make($request->password);
        $user->otp = null; // clear OTP after use
        $user->save();

        return redirect()->route('student.login')
            ->with('success', 'Password has been reset successfully. Please login with your new password.');
    }

    private function generateOtp()
    {
        return rand(100000, 999999);
    }
}

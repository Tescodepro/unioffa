<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\{ApplicantRegisteredMail, GeneralMail};
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(){
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
            $subject = "Login Notification";
             $content = [
                'title' => Auth::user()->full_name . ",",
                'body'  => "We noticed a login to your Offa University account.<br><br>

            Details:<br>  
            - Date: " . now()->format('Y-m-d H:i:s') . "<br>  
            - IP Address: " . request()->ip() . " <br><br>

            If this was you, no action is required. If not, please reset your password immediately.",
            'footer'=> "Stay safe,  
            Offa University Security Team"
            ]; 

            Mail::to($to)->send(new GeneralMail($subject, $content, false));

            return redirect()->intended(route('students.dashboard'))->with('success', 'You must be logged in.'); // or your home route
        }

        return back()->with( 'error', 'The provided credentials do not match our records.' );
    }
}

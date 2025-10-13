<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        return view('staff.auth.login');
    }

    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $name = $user->full_name;

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

            Mail::to($to)->send(new GeneralMail($subject, $content, false));

            // 🔹 Check user type and redirect to correct dashboard
            switch ($user->userType->name) {
                case 'administrator':
                    return redirect()->route('admin.dashboard')->with('success', "Welcome $name");
                case 'vice-chancellor':
                    return redirect()->route('admin.dashboard')->with('success', "Welcome $name");
                case 'registrar':
                    return redirect()->route('admin.dashboard')->with('success', "Welcome $name");
                case 'bursary':
                    return redirect()->route('burser.dashboard')->with('success', "Welcome $name");
                default:
                    Auth::logout();
                    return redirect()->route('staff.login')->with('error', 'Unauthorized access.');
            }

        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('staff.auth.login');
    }

    public function loginAction(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = $request->input('email');
        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $identifier,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            \Log::info('Staff Login Success', ['user' => Auth::user()->email]);

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
            Offa University Team',
            ];

            $brevo = new BrevoMailService;
            $brevo->sendView($to, Auth::user()->first_name, $subject, 'emails.general', ['content' => $content]);

            // 🔹 Redirect based on the user type's configured dashboard route
            $route = $user->userType->dashboard_route ?? null;

            if (! $route || ! \Illuminate\Support\Facades\Route::has($route)) {
                Auth::logout();

                return redirect()->route('staff.login')->with('error', 'Unauthorized access.');
            }

            return redirect()->route($route)->with('success', "Welcome $name");
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }

    public function logoutAction()
    {
        Auth::logout();

        return redirect()->route('staff.login')->with('success', 'Logged out successfully.');
    }

    public function showChangePasswordForm()
    {
        return view('staff.auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        $route = $user->userType->dashboard_route ?? 'staff.login';

        return redirect()->route($route)->with('success', 'Password updated successfully. Welcome back!');
    }
}

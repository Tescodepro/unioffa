<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
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

            // 🔹 Check user type and redirect to correct dashboard
            switch ($user->userType->name) {
                case 'administrator':
                    return redirect()->route('admin.dashboard')->with('success', "Welcome $name");

                default:
                    Auth::logout();
                    return redirect()->route('home')->with('error', 'Unauthorized access.');
            }

        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }

}

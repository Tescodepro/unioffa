<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\AgentApplication;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\News;



class GeneralController extends Controller
{
    public function home()
    {
        $title = 'Home';
        $events = [];
        $news = News::latest()->take(3)->get();

        return view('website.home', compact('title', 'events', 'news'));
    }

    public function contact()
    {
        $title = 'Contact Us';

        return view('website.contact', compact('title'));
    }

    public function agentApplication()
    {
        $title = 'Agent Application Form';
        $states = State::orderBy('name', 'asc')->get();
        $lgas = Lga::all();

        try {
            // Fetch from Paystack API
            $response = Http::withToken(env('PAYSTACK_AUTH_KEY'))
                ->get('https://api.paystack.co/bank', [
                    'country' => 'nigeria', // optional filter
                ]);
            // Extract only bank name & code if response is successful
            $banks = $response->successful()
                ? collect($response->json('data'))->map(fn($bank) => [
                    'name' => $bank['name'],
                    'code' => $bank['code'],
                ])
                : collect(); // fallback to empty collection

        } catch (\Exception $e) {
            // Handle network/API failure gracefully
            $banks = collect();
        }
        return view('website.application-form', compact('title', 'states', 'lgas', 'banks'));
    }
    public function submitAgentApplication(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:agent_applications,email',
            'phone' => 'required|string|max:20|unique:agent_applications,phone',
            'state' => 'required|exists:states,id',
            'lga' => 'required|exists:lgas,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|digits:10',
            'account_name' => 'required|string|max:255',
            'agree' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // 2. Prevent duplicates
            if (AgentApplication::where('email', $request->email)->orWhere('phone', $request->phone)->exists()) {
                return redirect()->back()->with('error', 'An account with this email or phone number already exists.');
            }

            // 3. Extract clean bank name and code
            // We use a robust regex to split "Bank Name (Code)"
            // The regex looks for the LAST set of parentheses to identify the code
            $bankName = $request->bank_name;
            preg_match('/(.*)\s*\((.*)\)$/', $bankName, $matches);

            $cleanBankName = isset($matches[1]) ? trim($matches[1]) : $bankName;
            $bankCode = isset($matches[2]) ? trim($matches[2]) : null;

            // 4. Create agent application (status: pending)
            $application = AgentApplication::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'state_id' => $request->state,
                'lga_id' => $request->lga,
                'bank_name' => $cleanBankName,
                'bank_code' => $bankCode,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
            ]);

            // 5. Send confirmation email to applicant
            // We wrap this in a sub-try/catch so email failures don't stop the user from seeing "Success"
            try {
                $subject = 'Agent Application Received';
                $content = [
                    'title' => 'Hi ' . $application->first_name . ',',
                    'body' => "
                    We've received your Agent Application and it’s currently under review.<br><br>
                    <strong>Name:</strong> {$application->first_name} {$application->last_name}<br>
                    <strong>Email:</strong> {$application->email}<br>
                    <strong>Phone:</strong> {$application->phone}<br>
                    <strong>State:</strong> {$application->state->name}<br>
                    <strong>LGA:</strong> {$application->lga->name}<br><br>
                    Once approved, you’ll receive your unique referral code via email.
                ",
                    'footer' => 'Warm regards,<br>Offa University Admissions Team',
                ];

                Mail::to($application->email)->send(new GeneralMail($subject, $content, false));
                Log::info('Agent application confirmation email sent to ' . $application->email);

            } catch (\Exception $e) {
                // Log the error but continue execution
                Log::warning('Failed to send agent application confirmation email to ' . $application->email . '. Error: ' . $e->getMessage());
            }

            // 6. Notify admin
            try {
                $adminEmail = env('ADMIN_EMAIL', 'vc@unioffa.edu.ng');
                $adminSubject = 'New Agent Application Submitted';
                $adminContent = [
                    'title' => 'New Agent Application Received',
                    'body' => "
                    A new agent application has been submitted.<br><br>
                    <strong>Name:</strong> {$application->first_name} {$application->last_name}<br>
                    <strong>Email:</strong> {$application->email}<br>
                    <strong>Phone:</strong> {$application->phone}<br>
                    <strong>Bank:</strong> {$application->bank_name}<br>
                    <strong>Account Name:</strong> {$application->account_name}<br>
                    <strong>Account Number:</strong> {$application->account_number}<br>
                    You can review and approve this application from the admin dashboard.
                ",
                    'footer' => '— Automated Notification from Offa University Website',
                ];

                Mail::to($adminEmail)->send(new GeneralMail($adminSubject, $adminContent, false));

            } catch (\Exception $e) {
                Log::error('Failed to send Admin Notification for Agent Application. Error: ' . $e->getMessage());
            }

            // 7. Return Success
            return redirect()->back()->with('success', 'Your application has been submitted successfully! Your unique referral code will be emailed to you once your application is approved.');

        } catch (\Exception $e) {
            // This catches database errors or critical logic failures
            Log::error('Agent Application Critical Error: ' . $e->getMessage());
            return redirect()->back()->with('error', "An error occurred while submitting your application. Please try again later.");
        }
    }
    public function getLgas($state_id)
    {
        $lgas = Lga::where('state_id', $state_id)->get();
        return response()->json($lgas);
    }

    public function scholarshipApplication()
    {
        $title = 'Scholarship Application';

        return view('website.scholarship-application', compact('title'));
    }

    public function prochancellor()
    {
        $title = 'Pro-Chancellor';
        // Loads resources/views/website/pro-chancellor.blade.php
        return view('website.pro-chancellor', compact('title'));
    }

    public function registrar()
    {
        $title = 'The Registrar';
        // Loads resources/views/website/registrar.blade.php
        return view('website.registrar', compact('title'));
    }

    // --- Council Members ---

    public function council_suraj()
    {
        $title = 'Council Member - Mr. Suraj Oyewale';
        // Loads resources/views/website/council-suraj.blade.php
        return view('website.council-suraj', compact('title'));
    }

    public function council_abdulateef()
    {
        $title = 'Council Member - Mr. Oyewale O. Abdulateef';
        // Loads resources/views/website/council-abdulateef.blade.php
        return view('website.council-abdulateef', compact('title'));
    }

    public function council_abdulrasheed()
    {
        $title = 'Council Member - Abdulrasheed Oyewale';
        // Loads resources/views/website/council-abdulrasheed.blade.php
        return view('website.council-abdulrasheed', compact('title'));
    }

    public function council_akeem()
    {
        $title = 'Council Member - Dr. Akeem Oyewale';
        // Loads resources/views/website/council-akeem.blade.php
        return view('website.council-akeem', compact('title'));
    }
}

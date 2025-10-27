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

class GeneralController extends Controller
{
    public function home()
    {
        $title = 'Home';
        $events = [];
        $blogs = [];

        return view('website.home', compact('title', 'events', 'blogs'));
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

        return view('website.application-form', compact('title', 'states', 'lgas'));
    }
    public function submitAgentApplication(Request $request)
    {
        // Validate input
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
            // Double-check duplicate prevention
            if (AgentApplication::where('email', $request->email)->orWhere('phone', $request->phone)->exists()) {
                return redirect()->back()->with('error', 'An account with this email or phone number already exists.');
            }

            // Create the agent application
            $application = AgentApplication::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'state_id' => $request->state,
                'lga_id' => $request->lga,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
            ]);

            // Send confirmation email to applicant
            $subject = 'Agent Application Received';
            $content = [
                'title' => 'Hi ' . $application->first_name . ',',
                'body' => "
                We've received your Agent Application and it’s currently under review.<br><br>
                Here’s a quick summary of your submission:<br><br>
                <strong>Name:</strong> {$application->first_name} {$application->last_name}<br>
                <strong>Email:</strong> {$application->email}<br>
                <strong>Phone:</strong> {$application->phone}<br>
                <strong>State:</strong> {$application->state->name}<br>
                <strong>LGA:</strong> {$application->lga->name}<br><br>
                Once approved, you’ll receive your unique referral code via email.<br><br>
                Thank you for your interest in partnering with Offa University.
            ",
                'footer' => 'Warm regards,<br>Offa University Admissions Team',
            ];

            Mail::to($application->email)->send(new GeneralMail($subject, $content, false));

            // Send a copy to admin
            $adminEmail = env('ADMIN_EMAIL', 'vc@unioffa.edu.ng');

            $adminSubject = 'New Agent Application Submitted';
            $adminContent = [
                'title' => 'New Agent Application Received',
                'body' => "
                A new agent application has been submitted.<br><br>
                <strong>Name:</strong> {$application->first_name} {$application->last_name}<br>
                <strong>Email:</strong> {$application->email}<br>
                <strong>Phone:</strong> {$application->phone}<br>
                <strong>State:</strong> {$application->state->name}<br>
                <strong>LGA:</strong> {$application->lga->name}<br>
                <strong>Bank:</strong> {$application->bank_name}<br>
                <strong>Account Name:</strong> {$application->account_name}<br>
                <strong>Account Number:</strong> {$application->account_number}<br><br>
                You can review and approve this application from the admin dashboard.
            ",
                'footer' => '— Automated Notification from Offa University Website',
            ];

            Mail::to($adminEmail)->send(new GeneralMail($adminSubject, $adminContent, false));

            return redirect()->back()->with('success', 'Your application has been submitted successfully! A confirmation email has been sent to you.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while submitting your application. Please try again later.');
        }
    }
    // API endpoint for fetching LGAs by state
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
}

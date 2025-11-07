<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\AdmissionList;
use App\Models\ApplicationSetting;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\UserApplications;
use App\Models\AgentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public function index_admin(Request $request)
    {
        $sessions = UserApplications::select('academic_session')->distinct()->pluck('academic_session');

        $selectedSession = $request->get('academic_session', $sessions->first());

        // Get all campuses & application types for filters
        $campuses = Campus::all();
        $applicationTypes = ApplicationSetting::all();

        // Read selected filters
        $selectedCampusId = $request->get('campus_id');
        $selectedApplicationId = $request->get('application_id');

        // Applicants per campus (with count)
        $campusApplicants = Campus::withCount([
            'users as applicant_count' => function ($q) {
                $q->whereHas('userType', fn($q2) => $q2->where('name', 'applicant'))
                    ->whereHas('applications', fn($q3) => $q3->whereNotNull('submitted_by'));
            },
        ])->get();

        // Applicants per application type
        $applicationApplicants = ApplicationSetting::withCount(['userApplications as applicant_count' => function ($q) use ($selectedSession) {
            $q->where('academic_session', $selectedSession)
                ->whereNotNull('submitted_by');
        }])->get();

        // Admitted + not admitted stats
        $admittedCount = AdmissionList::where('admission_status', 'admitted')->count();
        $notAdmittedCount = AdmissionList::where('admission_status', '!=', 'admitted')
            ->orWhereNull('admission_status')
            ->count();

        // Query students with filters
        $students = User::whereHas('userType', fn($q) => $q->where('name', 'applicant'))
            ->with([
                'applications.applicationSetting',
                'transactions',
                'admissionList',
                'department',
                'courseOfStudy.firstDepartment',   // <-- Add this
                'courseOfStudy.secondDepartment',  // <-- And this
            ])
            ->when($selectedCampusId, fn($q) => $q->where('campus_id', $selectedCampusId))
            ->when($selectedApplicationId, function ($q) use ($selectedApplicationId) {
                $q->whereHas('applications', fn($qa) => $qa->where('application_setting_id', $selectedApplicationId));
            })
            ->get()
            ->map(function ($user) {
                return (object) [
                    'id' => $user->id,
                    'registration_no' => $user->registration_no,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'application_type' => optional($user->applications->first()?->applicationSetting)->name,
                    'application_modules_enable' => optional($user->applications->first()?->applicationSetting)->modules_enable,
                    'application_id' => $user->applications->first()?->id,
                    'application_status' => $user->applications->first()?->submitted_by ? 'submitted' : 'not submitted',
                    'payment_status' => $user->transactions->where('payment_type', 'application')->where('payment_status', 1)->first()->payment_status ?? 'unpaid',
                    'payment_ref' => $user->transactions->where('payment_type', 'application')->where('payment_status', 1)->first()->refernce_number ?? null,
                    'admissionList' => $user->admissionList,
                    'admissionListDepartmet' => $user->admissionList?->department,
                    'first_choice' => $user->courseOfStudy?->firstDepartment?->department_name,
                    'second_choice' => $user->courseOfStudy?->secondDepartment?->department_name,
                ];
            });

        $departments = Department::all();
        $faculties = Faculty::all();
        return view('staff.admin_dashboard', compact(
            'sessions',
            'selectedSession',
            'campuses',
            'applicationTypes',
            'applicationApplicants',
            'campusApplicants',
            'admittedCount',
            'notAdmittedCount',
            'students',
            'selectedCampusId',
            'selectedApplicationId',
            'departments',
            'faculties',
        ));
    }

    public function admitStudent($userId, Request $request)
    {
        $user_application_id = $request->application_id;
        $user_application = UserApplications::findOrFail($user_application_id);
        $user_application->is_approved = 1;
        $user_application->save();

        // Get or create admission record
        $admission = AdmissionList::firstOrNew(['user_id' => $userId]);
        $admission->admission_status = 'admitted';
        $admission->approved_department_id = $request->final_course; // optional, if you want to track
        $admission->save();

        $department = Department::find($request->final_course);
        $user = User::findOrFail($userId);

        $applicationSetting = ApplicationSetting::find($user_application->application_setting_id);

        $to = $user->email;

        $subject = 'Offer of Admission - Offa University';

        $content = [
            'title' => 'Dear ' . $user->full_name . ',',
            'body' => 'Congratulations! We are delighted to inform you that you have been offered admission to Offa University to study ' . ($department->department_name ?? 'your chosen course') . '. for the ' . $user_application->academic_session . ' academic session admission. log in to your portal for further information',
            'footer' => '',
        ];

        Mail::to($to)->send(new GeneralMail($subject, $content, false));

        return back()->with('success', 'Student admitted successfully.');
    }

    public function recommendStudent($userId, Request $request)
    {
        $user_application_id = $request->application_id;
        $user_application = UserApplications::findOrFail($user_application_id);
        $user_application->is_approved = 2;
        $user_application->save();
        // Get or create admission record
        $admission = AdmissionList::firstOrNew(['user_id' => $userId]);
        $admission->admission_status = 'recommended';
        $admission->approved_department_id = $request->final_course; // optional, if you want to track
        $admission->save();

        return back()->with('success', 'Student recommendation successfully.');
    }

    public function showApplicantDetails($userId, $applicationId)
    {
        $application = UserApplications::with([
            'applicationSetting',
            'profile',
            'olevels',
            'jambDetail',
            'documents',
            'educationHistories',
            'user.courseOfStudy.firstDepartment',
            'user.courseOfStudy.secondDepartment',
        ])
            ->where('id', $applicationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Process olevels to fix double-encoded JSON and pair subjects with grades
        foreach ($application->olevels as $olevel) {
            $subjects = is_string($olevel->subjects) ? json_decode($olevel->subjects, true) : $olevel->subjects;
            $subjects = is_string($subjects) ? json_decode($subjects, true) : $subjects;
            $subjects = is_array($subjects) ? $subjects : [];

            $grades = is_string($olevel->grades) ? json_decode($olevel->grades, true) : $olevel->grades;
            $grades = is_string($grades) ? json_decode($grades, true) : $grades;
            $grades = is_array($grades) ? $grades : [];

            $olevel->subjects = array_combine($subjects, $grades) ?: [];
        }

        // Process jambDetail to fix double-encoded JSON and pair subjects with scores
        if ($application->jambDetail) {
            $subjects = is_string($application->jambDetail->subjects) ? json_decode($application->jambDetail->subjects, true) : $application->jambDetail->subjects;
            $subjects = is_string($subjects) ? json_decode($subjects, true) : $subjects;
            $subjects = is_array($subjects) ? $subjects : [];

            $scores = is_string($application->jambDetail->subject_scores) ? json_decode($application->jambDetail->subject_scores, true) : $application->jambDetail->subject_scores;
            $scores = is_string($scores) ? json_decode($scores, true) : $scores;
            $scores = is_array($scores) ? $scores : [];

            $application->jambDetail->subject_scores = array_combine($subjects, $scores) ?: [];
        }

        $modules = json_decode($application->applicationSetting->modules_enable, true);

        return view('staff.applicant_details', compact('application', 'modules'));
    }

    public function showAgentDetail()
    {
        $agentApplications = AgentApplication::all();
        return view('staff.agent-applicants', compact('agentApplications'));
    }

    public function changeAgentStatus(Request $request)
    {
        $agent = AgentApplication::findOrFail($request->agent_id);
        $previousStatus = $agent->status;

        $agent->status = $request->status;

        if ($request->status === 'approved') {
            // Generate unique code if not already assigned
            if (!$agent->unique_code) {
                $agent->unique_code = $this->generateUniqueCode();
            }

            // Only create Paystack subaccount & split if not already done
            if (!$agent->split_code) {
                try {
                    $paystackSecret = env('PAYSTACK_AUTH_KEY');

                    // Create Paystack subaccount
                    $subaccountResponse = Http::withToken($paystackSecret)
                        ->post('https://api.paystack.co/subaccount', [
                            'business_name' => "{$agent->first_name} {$agent->last_name}",
                            'settlement_bank' => $agent->bank_code,
                            'account_number' => $agent->account_number,
                            'percentage_charge' => 100,
                        ]);

                    if (!$subaccountResponse->successful() || !isset($subaccountResponse['data']['subaccount_code'])) {
                        throw new \Exception('Failed to create Paystack subaccount');
                    }

                    $subaccountCode = $subaccountResponse['data']['subaccount_code'];

                    // Create Paystack split
                    $splitResponse = Http::withToken($paystackSecret)
                        ->post('https://api.paystack.co/split', [
                            'name' => "{$agent->first_name} {$agent->last_name} Split for Agent",
                            'type' => 'percentage',
                            'currency' => 'NGN',
                            'subaccounts' => [
                                [
                                    'subaccount' => $subaccountCode,
                                    'share' => 40, // agent share
                                ],
                                [
                                    'subaccount' => 'ACCT_0hqs8sol7eyn3a3', // university subaccount
                                    'share' => 60,
                                ],
                            ],
                        ]);

                    if (!$splitResponse->successful() || !isset($splitResponse['data']['split_code'])) {
                        throw new \Exception('Failed to create Paystack split');
                    }

                    // Save both codes
                    $agent->split_code = $splitResponse['data']['split_code'];
                    // $agent->subaccount_code = $subaccountCode;
                } catch (\Exception $e) {
                    // Rollback approval if Paystack setup failed
                    $agent->status = $previousStatus;
                    $agent->save();

                    Log::error('Paystack setup failed for Agent ID ' . $agent->id . ': ' . $e->getMessage());
                    return back()->with('error', 'Approval failed because Paystack setup could not be completed. Please try again later.');
                }
            }
        }

        $agent->save();

        // Send status update email
        $subject = 'Agent Application Status Update';
        $content = [
            'title' => 'Hello ' . $agent->first_name . ',',
            'body' => "
We are writing to inform you that your application status has been updated to '{$request->status}'. 
" . ($agent->unique_code ? "Your unique agent code is: {$agent->unique_code}.<br><br>" : "") . "
Thank you for your interest in partnering with the University of Offa.",
            'footer' => 'Warm regards,<br>University of Offa Admissions Team',
        ];

        try {
            Mail::to($agent->email)->send(new GeneralMail($subject, $content, false));
        } catch (\Exception $e) {
            Log::warning('Failed to send agent status email to ' . $agent->email . ': ' . $e->getMessage());
        }

        return back()->with('success', 'Agent status updated successfully.');
    }

    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(4))); // Generates an 8-character code
        } while (AgentApplication::where('unique_code', $code)->exists());

        return $code;
    }
}

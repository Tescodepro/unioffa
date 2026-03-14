<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\AdmissionList;
use App\Models\AgentApplication;
use App\Models\ApplicationSetting;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\UserApplications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GeneralController extends Controller
{
    public function index_admin(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isProgDir = $user->hasRole('programme-director');
        $isCenterDir = $user->hasRole('center-director');

        $assignedTypeIds = $isProgDir
            ? $user->assignedApplicationTypes()->pluck('application_settings.id')->toArray()
            : [];

        $sessionsQuery = UserApplications::select('academic_session')->distinct();

        if ($isProgDir) {
            $sessionsQuery->whereIn('application_setting_id', $assignedTypeIds);
        }
        $sessions = $sessionsQuery->pluck('academic_session');

        $selectedSession = $request->get('academic_session', $sessions->first());

        // Get all campuses
        $campuses = Campus::all();

        // Filter application types for programme-directors
        if ($isProgDir) {
            $applicationTypes = ApplicationSetting::whereIn('id', $assignedTypeIds)->get();
        } else {
            $applicationTypes = ApplicationSetting::all();
        }

        // Read selected filters
        $selectedCampusId = $isCenterDir ? $user->campus_id : $request->get('campus_id');
        $selectedApplicationId = $request->get('application_id');

        // If they select an application ID they don't own, override
        if ($isProgDir && $selectedApplicationId && ! in_array($selectedApplicationId, $assignedTypeIds)) {
            $selectedApplicationId = null;
        }

        // Applicants per campus (with count)
        $campusApplicants = Campus::withCount([
            'users as applicant_count' => function ($q) use ($isProgDir, $assignedTypeIds, $selectedSession) {
                $q->whereHas('userType', fn ($q2) => $q2->whereIn('name', ['applicant', 'student']))
                    ->whereHas('applications', function ($q3) use ($isProgDir, $assignedTypeIds, $selectedSession) {
                        $q3->where('academic_session', $selectedSession);
                        if ($isProgDir) {
                            $q3->whereIn('application_setting_id', $assignedTypeIds);
                        }
                    });
            },
        ])->get();

        // Applicants per application type (only showing assigned ones for prog-dirs)
        $applicationApplicantsQuery = ApplicationSetting::withCount([
            'userApplications as applicant_count' => function ($q) use ($selectedSession) {
                $q->where('academic_session', $selectedSession);
            },
        ]);
        if ($isProgDir) {
            $applicationApplicantsQuery->whereIn('id', $assignedTypeIds);
        }
        $applicationApplicants = $applicationApplicantsQuery->get();

        // Admitted + not admitted stats (scoped)
        $admittedQuery = AdmissionList::where('admission_status', 'admitted')->where('session_admitted', $selectedSession);
        $notAdmittedQuery = AdmissionList::where(function ($q) {
            $q->where('admission_status', '!=', 'admitted')->orWhereNull('admission_status');
        })->where('session_admitted', $selectedSession);

        if ($isProgDir) {
            $admittedQuery->whereHas('user.userApplications', fn ($q) => $q->whereIn('application_setting_id', $assignedTypeIds));
            $notAdmittedQuery->whereHas('user.userApplications', fn ($q) => $q->whereIn('application_setting_id', $assignedTypeIds)->where('submitted_by', '!=', null));
        }

        $admittedCount = $admittedQuery->count();
        $notAdmittedCount = $notAdmittedQuery->count();

        // Query students with filters
        $students = User::whereHas('userType', fn ($q) => $q->whereIn('name', ['applicant', 'student']))
            ->with([
                'applications' => fn ($q) => $q->where('academic_session', $selectedSession),
                'applications.applicationSetting',
                'transactions',
                'admissionList',
                'department',
                'courseOfStudy.firstDepartment',
                'courseOfStudy.secondDepartment',
            ])
            // SCOPE TO SESSIONS AND PROG-DIR ASSIGNMENTS OR SELECTED FILTER
            ->whereHas('applications', function ($qa) use ($isProgDir, $isCenterDir, $assignedTypeIds, $selectedApplicationId, $selectedSession) {
                $qa->where('academic_session', $selectedSession);

                if ($selectedApplicationId) {
                    $qa->where('application_setting_id', $selectedApplicationId);
                } elseif ($isProgDir && ! $isCenterDir) {
                    $qa->whereIn('application_setting_id', $assignedTypeIds);
                }
            })
            ->when($selectedCampusId, fn ($q) => $q->where('campus_id', $selectedCampusId))
            ->get()
            ->map(function ($user) {
                $payment_status = $user->transactions->where('payment_type', 'application')->where('payment_status', 1)->first()->payment_status ?? 'unpaid';
                $isAdmitted = $user->admissionList && $user->admissionList->admission_status === 'admitted';

                // Prioritization score:
                // 1: Paid but not admitted (priority)
                // 2: Paid and Admitted
                // 3: Unpaid
                $priority = 3;
                if ($payment_status == 1) {
                    $priority = $isAdmitted ? 2 : 1;
                }

                return (object) [
                    'id' => $user->id,
                    'registration_no' => $user->registration_no,
                    'full_name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'application_type' => optional($user->applications->first()?->applicationSetting)->name,
                    'application_modules_enable' => optional($user->applications->first()?->applicationSetting)->modules_enable,
                    'application_id' => $user->applications->first()?->id,
                    'application_status' => $user->applications->first()?->submitted_by ? 'submitted' : 'not submitted',
                    'payment_status' => $payment_status,
                    'payment_ref' => $user->transactions->where('payment_type', 'application')->where('payment_status', 1)->first()->refernce_number ?? null,
                    'admissionList' => $user->admissionList,
                    'admissionListDepartmet' => $user->admissionList?->department,
                    'first_choice' => $user->courseOfStudy?->firstDepartment?->department_name,
                    'second_choice' => $user->courseOfStudy?->secondDepartment?->department_name,
                    'priority' => $priority,
                ];
            })
            ->sortBy('priority')
            ->values();

        $faculties = Faculty::all();
        $departments = Department::all();

        $dashRoute = $request->route()->getName();
        $detailRoute = 'admission.details';
        $admitRoute = 'admission.admit';

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
            'dashRoute',
            'detailRoute',
            'admitRoute',
        ));
    }

    public function admitStudent($userId, Request $request)
    {
        $user_application_id = $request->application_id;
        $user_application = UserApplications::findOrFail($user_application_id);

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->hasRole('programme-director')) {
            $assignedTypes = $user->assignedApplicationTypes()->pluck('application_settings.id')->toArray();
            if (! in_array($user_application->application_setting_id, $assignedTypes)) {
                return back()->with('error', 'Unauthorized: You cannot admit students for this application type.');
            }
        }

        $user_application->is_approved = 1;
        $user_application->save();

        // Get or create admission record
        $admission = AdmissionList::firstOrNew(['user_id' => $userId]);
        $admission->admission_status = 'admitted';
        $admission->approved_department_id = $request->final_course; // optional, if you want to track
        $admission->session_admitted = activeSession()?->name ?? '';
        $admission->save();

        $department = Department::find($request->final_course);
        $studentUser = User::findOrFail($userId);

        $applicationSetting = ApplicationSetting::find($user_application->application_setting_id);

        $to = $studentUser->email;

        $subject = 'Offer of Admission - Offa University';

        $content = [
            'title' => 'Dear '.$studentUser->full_name.',',
            'body' => 'Congratulations! We are delighted to inform you that you have been offered admission to Offa University to study '.($department->department_name ?? 'your chosen course').'. for the '.$user_application->academic_session.' academic session admission. log in to your portal for further information',
            'footer' => '',
        ];

        // Mail::to($to)->send(new GeneralMail($subject, $content, false));

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

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->hasRole('programme-director')) {
            $assignedTypes = $user->assignedApplicationTypes()->pluck('application_settings.id')->toArray();
            if (! in_array($application->application_setting_id, $assignedTypes)) {
                abort(403, 'Unauthorized: You are not assigned to manage this application type ('.optional($application->applicationSetting)->name.').');
            }
        }

        // Process olevels to fix double-encoded JSON and pair subjects with grades
        foreach ($application->olevels as $olevel) {
            $subjects = is_array($olevel->subjects) ? $olevel->subjects : json_decode((string) $olevel->subjects, true);
            $subjects = is_array($subjects) ? $subjects : json_decode((string) $subjects, true);
            $subjects = is_array($subjects) ? $subjects : [];

            $grades = is_array($olevel->grades) ? $olevel->grades : json_decode((string) $olevel->grades, true);
            $grades = is_array($grades) ? $grades : json_decode((string) $grades, true);
            $grades = is_array($grades) ? $grades : [];

            $olevel->subjects = array_combine($subjects, $grades) ?: [];
        }

        // Process jambDetail to fix double-encoded JSON and pair subjects with scores
        if ($application->jambDetail) {
            $subjects = is_array($application->jambDetail->subjects) ? $application->jambDetail->subjects : json_decode((string) $application->jambDetail->subjects, true);
            $subjects = is_array($subjects) ? $subjects : json_decode((string) $subjects, true);
            $subjects = is_array($subjects) ? $subjects : [];

            $scores = is_array($application->jambDetail->subject_scores) ? $application->jambDetail->subject_scores : json_decode((string) $application->jambDetail->subject_scores, true);
            $scores = is_array($scores) ? $scores : json_decode((string) $scores, true);
            $scores = is_array($scores) ? $scores : [];

            $application->jambDetail->subject_scores = array_combine($subjects, $scores) ?: [];
        }

        $modules = is_array($application->applicationSetting->modules_enable)
            ? $application->applicationSetting->modules_enable
            : json_decode((string) $application->applicationSetting->modules_enable, true);

        $departments = \App\Models\Department::orderBy('department_name')->get();

        return view('staff.applicant_details', compact('application', 'modules', 'departments'));
    }

    public function showAgentDetail()
    {
        $agentApplications = AgentApplication::withCount('referredUsers')->get();

        return view('staff.agent-applicants', compact('agentApplications'));
    }

    public function changeAgentStatus(Request $request)
    {
        // 1. Validate the Request
        $request->validate([
            'agent_id' => 'required|exists:agent_applications,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $agent = AgentApplication::findOrFail($request->agent_id);
        $previousStatus = $agent->status;

        // Update status in memory (not saved yet)
        $agent->status = $request->status;

        // --- Logic for Approval ---
        if ($request->status === 'approved') {

            // A. Generate unique code if not already assigned
            if (! $agent->unique_code) {
                $agent->unique_code = $this->generateUniqueCode();
            }

            // B. Paystack Logic: Only run if we don't have a split code yet
            if (! $agent->split_code) {
                try {
                    // Use the correct ENV key for your Paystack Secret
                    $paystackSecret = env('PAYSTACK_AUTH_KEY');

                    // --- Step 1: Create Paystack Subaccount for Agent ---
                    if (! $agent->subaccount_code) {
                        $subaccountResponse = Http::withToken($paystackSecret)
                            ->post('https://api.paystack.co/subaccount', [
                                'business_name' => "{$agent->first_name} {$agent->last_name} Agency",
                                'settlement_bank' => $agent->bank_code,
                                'account_number' => $agent->account_number,
                                'percentage_charge' => 0, // 0 = Main account pays fees. Set to 100 if Agent pays fees.
                                'description' => "Subaccount for Agent {$agent->first_name} {$agent->last_name}",
                            ]);

                        if (! $subaccountResponse->successful() || ! isset($subaccountResponse['data']['subaccount_code'])) {
                            // Log the actual error from Paystack for debugging
                            Log::error('Paystack Subaccount Creation Failed: '.$subaccountResponse->body());
                            throw new \Exception('Failed to create Paystack subaccount');
                        }

                        // Save the subaccount immediately to avoid duplication if next step fails
                        $subaccountCode = $subaccountResponse['data']['subaccount_code'];
                        $agent->subaccount_code = $subaccountCode;
                        $agent->save();
                    } else {
                        $subaccountCode = $agent->subaccount_code;
                    }

                    // --- Step 2: Create Paystack Split Group ---
                    // Defined in .env or fallback to the hardcoded ID
                    $universitySubaccount = env('UNIVERSITY_SUBACCOUNT_CODE', 'ACCT_0hqs8sol7eyn3a3');

                    $splitResponse = Http::withToken($paystackSecret)
                        ->post('https://api.paystack.co/split', [
                            'name' => "{$agent->first_name} {$agent->last_name} Split Group",
                            'type' => 'percentage',
                            'currency' => 'NGN',
                            'subaccounts' => [
                                [
                                    'subaccount' => $subaccountCode,
                                    'share' => 40, // Agent gets 40%
                                ],
                                [
                                    'subaccount' => $universitySubaccount,
                                    'share' => 60, // University gets 60%
                                ],
                            ],
                        ]);

                    if (! $splitResponse->successful() || ! isset($splitResponse['data']['split_code'])) {
                        Log::error('Paystack Split Creation Failed: '.$splitResponse->body());
                        throw new \Exception('Failed to create Paystack split');
                    }

                    // Save the split code
                    $agent->split_code = $splitResponse['data']['split_code'];

                } catch (\Exception $e) {
                    // --- Rollback ---
                    // If Paystack fails, revert status to previous (e.g., pending)
                    $agent->status = $previousStatus;
                    $agent->save();

                    Log::error('Paystack setup failed for Agent ID '.$agent->id.': '.$e->getMessage());

                    return back()->with('error', 'Approval failed: Could not complete Paystack setup. '.$e->getMessage());
                }
            }
        }

        // Final Save
        $agent->save();

        // --- Send Status Notification Email ---
        $subject = 'Agent Application Status Update';

        // Build email body based on status
        $emailBody = "We are writing to inform you that your application status has been updated to <strong>'{$request->status}'</strong>.<br><br>";

        if ($agent->unique_code && $request->status === 'approved') {
            $emailBody .= "Your unique agent referral code is: <h2 style='color:green;'>{$agent->unique_code}</h2><br>Use this code for your candidates.<br><br>";
        }

        $emailBody .= 'Thank you for your interest in partnering with the '.\App\Models\SystemSetting::get('school_name', 'University of Offa').'.';

        $content = [
            'title' => 'Hello '.$agent->first_name.',',
            'body' => $emailBody,
            'footer' => 'Warm regards,<br>'.\App\Models\SystemSetting::get('school_name', 'University of Offa').' Admissions Team',
        ];

        try {
            Mail::to($agent->email)->send(new GeneralMail($subject, $content, false));
        } catch (\Exception $e) {
            Log::warning('Failed to send agent status email to '.$agent->email.': '.$e->getMessage());
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

<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserType;
use App\Services\UniqueIdService;
use Illuminate\Support\Facades\Auth;
use App\Mail\ApplicantRegisteredMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\{ApplicationSetting, UserApplications, AdmissionList, Profile, Olevel, Alevel, Campus, CourseOfStudy, Document, JambDetail, EducationHistory, Department, Faculty, Transaction};


class ApplicationController extends Controller
{

    public function index()
    {
        $title = 'Application Registration Form';
        // get center
        $campuses = Campus::all();
        return view('applications.register', compact('title', 'campuses'));
    }

    public function createAccount(Request $request, UniqueIdService $uniqueIdService)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'center' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $uniqueId = $uniqueIdService->generate('applicant');

        // Save user (example)
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'campus_id' => $request->center,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'username' => $uniqueId,
            'registration_no' => $uniqueId,
            'user_type_id' => UserType::where('name', 'applicant')->first()->id,
        ]);

        // get center
        $campuses = Campus::all();

        // Send mail with application number
        Mail::to($user->email)->send(new ApplicantRegisteredMail($user, $uniqueId));


        return redirect()->route('application.login', compact('campuses'))->with('success', 'Registration successful Please login to continue.');
    }

    public function login()
    {
        $title = 'Login Page';
        return view('applications.login', compact('title'));
    }

    public function loginAction(Request $request)
    {
        $credentials = $request->validate([
            'email_registration_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Decide which column to use
        $fieldType = filter_var($credentials['email_registration_number'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Build proper credentials array
        $authCredentials = [
            $fieldType => $credentials['email_registration_number'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('application.dashboard'))->with('success', 'You must be logged in.'); // or your home route
        }

        return back()->with( 'error', 'The provided credentials do not match our records.' );
    }

    public function logoutAction(Request $request)
    {
        Auth::logout();
        return redirect()->route('application.login')->with('success','Log out');
    }

    public function dashboard()
    {
        $title = 'Application Dashboard';
        $applicationSettings = ApplicationSetting::where('enabled', 1)->get();
        
        $applications = UserApplications::with(['applicationSetting', 'transactions'])
            ->where('user_id', Auth::id())
            ->paginate(10);


        return view('applications.dashboard', compact('title', 'applicationSettings', 'applications'));
    }

    public function startApplication(Request $request)
    { 
        $request->validate([
            'application_setting_id' => 'required|exists:application_settings,id'
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login first');
        }

        // Check if the user already has an application for this setting
        $userApplication = UserApplications::where('user_id', Auth::user()->id)
            ->where('application_setting_id', $request->application_setting_id)
            ->first();

        if ($userApplication) {
            return redirect()->route('application.form', [
                'user_application_id' => $userApplication->id
            ])->with('success', 'You have already initialized this application before.');
        }

        // Check if any active application settings exist
        if (ApplicationSetting::where('enabled', true)->count() == 0) {
            return redirect()->back()->with('error', 'No active application settings found');
        }

        try {
            // Initialize new application
            $recordApplicationInitialization = UserApplications::create([
                'user_id' => Auth::user()->id,
                'application_setting_id' => $request->application_setting_id,
                'academic_session' => $request->academic_session,
            ]);

            return redirect()->route('application.form', [
                'user_application_id' => $recordApplicationInitialization->id
            ])->with('success', 'Application initialized successfully');
        } catch (\Exception $th) {
            return redirect()->back()->with('error', 'An error occurred while initializing the application');
        }
    }

    public function applicationForm($user_application_id)
    {
        $users = Auth::user();
        $title = Auth::user()->full_name . ' Application Form';

        $application = UserApplications::with('applicationSetting')
            ->where('user_id', Auth::id())
            ->where('id', $user_application_id)
            ->firstOrFail();

        $modules = json_decode($application->applicationSetting->modules_enable, true);



        // Load each module's data
        $profile = Profile::where('user_application_id', $user_application_id)->first();
        $olevel = Olevel::where('user_application_id', $user_application_id)->first();
        // $alevel = Alevel::where('user_application_id', $user_application_id)->first();
        $alevel = [];
        $courseOfStudy = CourseOfStudy::where('user_application_id', $user_application_id)->first();
        $documents = Document::where('user_application_id', $user_application_id)->get()->keyBy('type');
        $jambDetails = JambDetail::where('user_application_id', $user_application_id)->first();
        $educationHistories = EducationHistory::where('user_application_id', $user_application_id)->get();

        // === get department and faculties
        $departments = Department::all();
        $faculties = Faculty::all();

        // Payment Settings 
        $payment_transaction = Transaction::where('user_id', $users->id)
                    ->where('session', $application->academic_session)
                    ->whereIn('payment_type', ['application', 'acceptance'])
                    ->get();
        
        $application_payment_status = Transaction::where('user_id', $users->id)
            ->where('session', $application->academic_session)
            ->whereIn('payment_type', ['application', 'acceptance'])
            ->where('payment_status', 1)
            ->get()
            ->keyBy('payment_type');  // 👈 This makes ['application'] and ['acceptance'] available

        $admission_status = AdmissionList::where('user_id', $users->id)
                ->where('session_admitted', $application->academic_session)
                ->where('admission_status', 'admitted')
                ->count();



        return view('applications.application_form', compact(
            'title',
            'user_application_id',
            'application',
            'modules',
            'profile',
            'olevel',
            'alevel',
            'courseOfStudy',
            'documents',
            'jambDetails',
            'educationHistories',
            'departments',
            'faculties',
            'payment_transaction',
            'application_payment_status',
            'admission_status',
        ));
    }
    public function saveProfile(Request $request, $user_application_id)
    {
        $request->validate([
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string|max:500',
            'state_of_origin' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
        ]);

        Profile::updateOrCreate(
            ['user_application_id' => $user_application_id],
            [
                'user_id' => Auth::id(),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'state_of_origin' => $request->state_of_origin,
                'nationality' => $request->nationality,
            ]
        );

        return redirect()->back()->with('success', 'Profile saved successfully!');
    }

    public function saveOlevel(Request $request, $user_application_id)
    {
        $request->validate([
            'olevel_exam_type' => 'required|in:waec,neco,nabteb',
            'olevel_year' => 'required|integer|min:2010|max:' . date('Y'),
            'olevel_subjects' => 'required|array|min:1',
            'olevel_subjects.*' => 'required|string',
            'olevel_grades' => 'required|array|min:1',
            'olevel_grades.*' => 'required|in:A1,B2,B3,C4,C5,C6,D7,E8,F9',
        ]);

        // Ensure subjects and grades arrays have the same count
        if (count($request->olevel_subjects) !== count($request->olevel_grades)) {
            return redirect()->back()->withErrors(['error' => 'Subjects and grades count mismatch']);
        }

        Olevel::updateOrCreate(
            ['user_application_id' => $user_application_id],
            [
                'user_id' => Auth::id(),
                'exam_type' => $request->olevel_exam_type,
                'exam_year' => $request->olevel_year,
                'subjects' => json_encode($request->olevel_subjects),
                'grades' => json_encode($request->olevel_grades),
            ]
        );

        return redirect()->back()->with('success', 'O\'Level results saved successfully!');
    }

    public function saveAlevel(Request $request, $user_application_id)
    {
        $request->validate([
            'alevel_exam_type' => 'nullable|in:ijmb,jupeb,cambridge',
            'alevel_year' => 'nullable|integer|min:2010|max:2025',
            'alevel_grades' => 'nullable|array',
        ]);

        // Only save if exam type is provided
        if ($request->alevel_exam_type) {
            Alevel::updateOrCreate(
                ['user_application_id' => $user_application_id],
                [
                    'user_id' => Auth::id(),
                    'exam_type' => $request->alevel_exam_type,
                    'exam_year' => $request->alevel_year,
                    'grades' => json_encode($request->alevel_grades ?? []),
                ]
            );

            return redirect()->back()->with('success', 'A\'Level results saved successfully!');
        }

        return redirect()->back()->with('info', 'A\'Level section skipped');
    }

    public function saveCourseOfStudy(Request $request, $user_application_id)
    {
        $request->validate([
            'first_choice' => 'required|string',
            'second_choice' => 'nullable|string|different:first_choice',
        ]);

        CourseOfStudy::updateOrCreate(
            ['user_application_id' => $user_application_id],
            [
                'user_id' => Auth::id(),
                'first_department_id' => $request->first_choice,
                'second_department_id' => $request->second_choice,
            ]
        );

        return redirect()->back()->with('success', 'Course selections saved successfully!');
    }

    public function saveDocuments(Request $request, $user_application_id)
    {
        $application = UserApplications::findOrFail($user_application_id);
        $modules = json_decode($application->applicationSetting->modules_enable, true);
        $requiredDocs = $modules['documents'] ?? [];

        $rules = [];
        foreach ($requiredDocs as $doc) {
            // Check if document already exists
            $existingDoc = Document::where('user_application_id', $user_application_id)
                                ->where('type', $doc)
                                ->first();
            
            $rules["documents.{$doc}"] = $existingDoc ? 'nullable|file|mimes:pdf|max:2048' : 'required|file|mimes:pdf|max:2048';
        }

        $request->validate($rules);

        foreach ($requiredDocs as $doc) {
            if ($request->hasFile("documents.{$doc}")) {
                $file = $request->file("documents.{$doc}");
                
                // Delete old file if exists
                $existingDoc = Document::where('user_application_id', $user_application_id)
                                    ->where('type', $doc)
                                    ->first();
                
                if ($existingDoc && Storage::exists($existingDoc->file_path)) {
                    Storage::delete($existingDoc->file_path);
                }

                // Store new file
                $filename = time() . '_' . $doc . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('documents/' . Auth::id(), $filename, 'public');

                Document::updateOrCreate(
                    [
                        'user_application_id' => $user_application_id,
                        'type' => $doc
                    ],
                    [
                        'user_id' => Auth::id(),
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Documents uploaded successfully!');
    }

    public function handleFormSubmission(Request $request, $user_application_id)
    {
        $user = Auth::user();
        $user_application = UserApplications::findOrFail($user_application_id);
        $user_application->submitted_by = now();
        $user_application->save();

        $admission = AdmissionList::updateOrCreate(
            ['user_id' => $user->id],
            [
                'admission_status' => 'pending',
                'session_admitted' => $user_application->academic_session,
                ]
        );

        return redirect()->back()->with("success", "Your application has been successfully submitted!");
    }

    public function downloadAdmissionLetter($applicationId)
    {
        $application = UserApplications::with(['user', 'admissionList.department'])
        ->findOrFail($applicationId);


        $student = $application->user;
        $profile = $application->profile;

        $department = AdmissionList::where('user_id', $profile->user_id)
            ->where('session_admitted', $application->academic_session)
            ->join('departments', 'admission_lists.approved_department_id', '=', 'departments.id')
            ->select('departments.department_name')
            ->first();

        $data = [
            'student' => $student,
            'session'=> $application->academic_session,
            'profile' => $profile,
            'department' => $department,
            'application' => $application,
            'date' => Carbon::now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('applications.admission-letter', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download('Admission_Letter_' . $student->full_name . '.pdf');
    }

}

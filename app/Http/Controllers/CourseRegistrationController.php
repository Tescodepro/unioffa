<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Student\DashboardController;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Department;
use App\Models\Result;
use App\Models\Student;
use App\Services\PaymentStatusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('student.department.faculty');
        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $currentSession = activeSession()->name;
        $activeSemester = activeSemester();
        $currentSemester = $activeSemester->code ?? ($activeSemester->name ?? '1st');

        // Check for outstanding debt from previous sessions
        if ($student->isBlockedByDebt()) {
            $debt = $student->getOutstandingDebt();

            return redirect()->route('student.dashboard')->with('error', 'You have an outstanding debt of ₦'.number_format($debt, 2).'. Please clear your balance before registering for courses.');
        }

        // Check payment status for current session
        $paymentStatusService = new PaymentStatusService;
        $rawStatus = $paymentStatusService->getStatus($student, $currentSession);

        // Key the status by payment type for easier access in view
        $keyedStatus = collect($rawStatus)->keyBy('payment_type')->toArray();

        $tuitionPercentage = 100; // Default if tuition not in outstanding list (cleared)
        if (isset($keyedStatus['tuition'])) {
            $tuitionPercentage = $keyedStatus['tuition']['percentage_paid'];
        }

        $payment_status = [
            'status' => $keyedStatus,
            'allCleared' => $paymentStatusService->hasClearedAll($student, $currentSession),
            'allCompulsoryCleared' => $paymentStatusService->hasClearedCompulsory($student, $currentSession),
            'outstanding' => $paymentStatusService->getTotalOutstanding($student, $currentSession),
            'tuitionPercentage' => $tuitionPercentage,
        ];

        // Filters and default selections
        $departments = Department::orderBy('department_name')->get();
        $levels = ['100', '200', '300', '400', '500'];
        $selectedDepartmentId = $request->input('department_id', $student->department_id);
        $selectedLevel = $request->input('level', $student->level);

        // Registered courses for current session/semester
        $registeredCourses = CourseRegistration::with('course')
            ->where('student_id', $user->id)
            ->where('session', $currentSession)
            ->where('semester', $currentSemester)
            ->get();

        // Unit Summary
        $maxSemesterUnits = (int) \App\Models\SystemSetting::get('max_units_per_semester', 24);
        $maxSessionUnits = (int) \App\Models\SystemSetting::get('max_units_per_session', 48);
        $currentSemesterUnits = $registeredCourses->sum('course_unit');
        $currentSessionUnits = CourseRegistration::where('student_id', $user->id)
            ->where('session', $currentSession)
            ->sum('course_unit');

        // Failed courses for Carry Over
        $failedCourseIds = Result::where('student_id', $user->id)
            ->where(function ($q) {
                $q->where('grade', 'F')
                    ->orWhere('remark', 'Fail');
            })
            ->where('semester', $currentSemester)
            ->pluck('course_id')
            ->unique()
            ->toArray();

        // If payment not cleared, we still need to pass everything to the view
        // to avoid "undefined variable" errors and show the payment warning/filter
        $courseRegistrationSetting = \App\Models\CourseRegistrationSetting::getActiveForStudent($student, $currentSession, $currentSemester);
        $hasPaidLateFee = false;

        $closestIncrementDate = null;
        $closestIncrementAmount = null;
        $closestClosingDate = null;
        $closestClosingAmount = null;

        try {
            $paymentSettings = app(DashboardController::class)->getPaymentSettingsForStudent($user, $currentSession, $currentSemester, $activeSemester);
            if ($paymentSettings) {
                $hasLatePenalty = $paymentSettings->contains('has_late_penalty', true);

                if ($hasLatePenalty) {
                    $penalty = $paymentSettings->firstWhere('has_late_penalty', true);
                    $closestIncrementDate = $penalty->increment_date ?? null;
                    $closestIncrementAmount = $penalty->increment_amount ?? null;
                }

                $upcomingPenalty = $paymentSettings->filter(function ($payment) {
                    return ! empty($payment->upcoming_closing_date) && now()->lt(\Carbon\Carbon::parse($payment->upcoming_closing_date));
                })->sortBy('upcoming_closing_date')->first();

                if ($upcomingPenalty) {
                    $closestClosingDate = $upcomingPenalty->upcoming_closing_date;
                    $closestClosingAmount = $upcomingPenalty->upcoming_penalty_amount;
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to fetch payment settings for course reg check: '.$e->getMessage());
        }

        if ($courseRegistrationSetting && now()->gt($courseRegistrationSetting->closing_date)) {
            $hasPaidLateFee = \App\Models\Transaction::where('user_id', $user->id)
                ->where('payment_type', 'late_course_registration')
                ->where('payment_status', 1)
                ->where('session', $currentSession)
                ->where('semester', $currentSemester)
                ->exists();
        }

        if (! $payment_status['allCompulsoryCleared']) {
            $courses = collect();

            // Allow showing courses if tuition is >= 60%, it's 1st semester, and no OTHER COMPULSORY fees are pending
            $compulsoryPendingStatus = collect($keyedStatus)->filter(fn ($p) => $p['is_compulsory'] && $p['balance'] > 0);
            $otherCompulsoryPending = $compulsoryPendingStatus->except('tuition')->count();

            $hasPartialTuitionAccess = ($tuitionPercentage >= 60 && strtolower($currentSemester) === '1st' && $otherCompulsoryPending === 0);

            if ($hasPartialTuitionAccess) {
                $courses = Course::where('active_for_register', 1)
                    ->where('level', $selectedLevel)
                    ->where(function ($q) use ($currentSemester, $tuitionPercentage) {
                        if ($tuitionPercentage >= 100) {
                            // Don't filter by semester (allow both)
                        } else {
                            $q->where('semester', $currentSemester);
                        }
                    })
                    ->where(function ($query) use ($selectedDepartmentId) {
                        $query->where('department_id', $selectedDepartmentId)
                            ->orWhereJsonContains('other_departments', $selectedDepartmentId);
                    })
                    ->get();
            }

            return view('student.course-registration', compact(
                'courses',
                'registeredCourses',
                'payment_status',
                'departments',
                'levels',
                'selectedDepartmentId',
                'selectedLevel',
                'failedCourseIds',
                'maxSemesterUnits',
                'maxSessionUnits',
                'currentSemesterUnits',
                'currentSessionUnits',
                'currentSemester',
                'courseRegistrationSetting',
                'hasPaidLateFee',
                'closestIncrementDate',
                'closestIncrementAmount',
                'closestClosingDate',
                'closestClosingAmount'
            ))->with('error', $hasPartialTuitionAccess ? null : 'You must clear all compulsory outstanding payments before registering courses.');
        }

        $isRegularOrDiploma = in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA']);
        $courses = Course::where('active_for_register', 1)
            ->where('level', $selectedLevel)
            ->where(function ($q) use ($currentSemester, $tuitionPercentage) {
                if ($tuitionPercentage >= 100) {
                    // Don't filter by semester (allow both)
                } else {
                    $q->where('semester', $currentSemester);
                }
            })
            ->where(function ($query) use ($selectedDepartmentId) {
                $query->where('department_id', $selectedDepartmentId)
                    ->orWhereJsonContains('other_departments', $selectedDepartmentId);
            })
            ->get();

        return view('student.course-registration', compact(
            'courses',
            'registeredCourses',
            'payment_status',
            'departments',
            'levels',
            'selectedDepartmentId',
            'selectedLevel',
            'failedCourseIds',
            'maxSemesterUnits',
            'maxSessionUnits',
            'currentSemesterUnits',
            'currentSessionUnits',
            'currentSemester',
            'courseRegistrationSetting',
            'hasPaidLateFee',
            'closestIncrementDate',
            'closestIncrementAmount',
            'closestClosingDate',
            'closestClosingAmount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
        ]);

        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Check for outstanding debt from previous sessions
        if ($student->isBlockedByDebt()) {
            $debt = $student->getOutstandingDebt();

            return redirect()->route('student.dashboard')->with('error', 'You have an outstanding debt of ₦'.number_format($debt, 2).'. Please clear your balance before registering for courses.');
        }

        // Check payment status before allowing registration
        $paymentStatusService = new PaymentStatusService;
        $currentSession = activeSession()->name;
        $rawStatus = $paymentStatusService->getStatus($student, $currentSession);
        $keyedStatus = collect($rawStatus)->keyBy('payment_type')->toArray();
        $tuitionPercentage = 100;
        if (isset($keyedStatus['tuition'])) {
            $tuitionPercentage = $keyedStatus['tuition']['percentage_paid'];
        }

        $currentSemester = activeSemester()->code ?? (activeSemester()->name ?? '1st');

        if (! $paymentStatusService->hasClearedCompulsory($student, $currentSession)) {
            $compulsoryPendingStatus = collect($keyedStatus)->filter(fn ($p) => $p['is_compulsory'] && $p['balance'] > 0);
            $otherCompulsoryPending = $compulsoryPendingStatus->except('tuition')->count();

            $canRegisterPartial = ($tuitionPercentage >= 60 && strtolower($currentSemester) === '1st' && $otherCompulsoryPending === 0);

            if (! $canRegisterPartial) {
                return redirect()->back()->with('error', 'You must clear all compulsory outstanding payments before registering courses.');
            }
        }

        $successCount = 0;

        // Unit Limit Validation
        $maxSemesterUnits = (int) \App\Models\SystemSetting::get('max_units_per_semester', 24);
        $maxSessionUnits = (int) \App\Models\SystemSetting::get('max_units_per_session', 48);

        // Calculate units for new courses separately per semester
        $newRequestedCourseIds = [];
        $newUnitsBySemester = [];
        $newUnitsTotal = 0;

        // Ensure courses array only contains unique IDs
        $requestedCourses = array_unique($request->courses);

        foreach ($requestedCourses as $courseId) {
            $course = Course::find($courseId);
            if (! $course || ! $course->active_for_register) {
                continue;
            }

            // Enforce semester restriction for < 100% tuition
            if ($tuitionPercentage < 100 && strtolower($course->semester) === '2nd') {
                return redirect()->back()->with('error', "You must pay 100% of your tuition to register for 2nd semester courses like {$course->course_code}.");
            }

            $exists = CourseRegistration::where('student_id', $user->id)
                ->where('course_id', $courseId)
                ->where('session', $currentSession)
                ->where('semester', $course->semester)
                ->exists();

            if (! $exists) {
                $newRequestedCourseIds[] = $courseId;

                $sem = strtolower($course->semester);
                if (! isset($newUnitsBySemester[$sem])) {
                    $newUnitsBySemester[$sem] = 0;
                }

                $newUnitsBySemester[$sem] += $course->course_unit;
                $newUnitsTotal += $course->course_unit;
            }
        }

        if (empty($newRequestedCourseIds)) {
            return redirect()->back()->with('info', 'No new courses were selected for registration.');
        }

        // Get existing units grouped by semester
        $existingUnitsBySemester = CourseRegistration::where('student_id', $user->id)
            ->where('session', $currentSession)
            ->selectRaw('LOWER(semester) as sem, SUM(course_unit) as total')
            ->groupBy('sem')
            ->pluck('total', 'sem')
            ->toArray();

        // Check limits per semester
        foreach ($newUnitsBySemester as $sem => $units) {
            $currentSemTotal = $existingUnitsBySemester[$sem] ?? 0;

            if (($currentSemTotal + $units) > $maxSemesterUnits) {
                $semDisplay = ucfirst($sem);

                return redirect()->back()->with('error', 'Limit Exceeded: Adding these courses would bring your total to '.($currentSemTotal + $units)." units, which exceeds the limit of {$maxSemesterUnits} units for the {$semDisplay} semester.");
            }
        }

        // Check current session total
        $currentSessionTotal = array_sum($existingUnitsBySemester);

        if (($currentSessionTotal + $newUnitsTotal) > $maxSessionUnits) {
            return redirect()->back()->with('error', 'Limit Exceeded: Adding these courses would bring your total to '.($currentSessionTotal + $newUnitsTotal)." units, which exceeds the session limit of {$maxSessionUnits}.");
        }

        foreach ($newRequestedCourseIds as $courseId) {
            $course = Course::findOrFail($courseId);

            CourseRegistration::firstOrCreate(
                [
                    'student_id' => $user->id,
                    'course_id' => $course->id,
                    'session' => $currentSession,
                    'semester' => $course->semester,
                    'matric_no' => $student->matric_no,
                    'course_code' => $course->course_code,
                    'course_title' => $course->course_title,
                    'course_unit' => $course->course_unit,
                    'status' => 'pending',
                ]
            );
            $successCount++;
        }

        if ($successCount > 0) {
            return redirect()->back()->with('success', "{$successCount} course(s) registered successfully.");
        }

        return redirect()->back()->with('info', 'No new courses were registered (all may already be registered).');
    }

    public function approve($id)
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Course registration approved.');
    }

    public function reject($id)
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Course registration rejected.');
    }

    public function downloadCourseForm()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $registeredCourses = CourseRegistration::with('course')
            ->where('matric_no', $student->matric_no)
            ->where('session', activeSession()->name ?? null)
            ->get();

        $pdf = Pdf::loadView('student.course-registration-printable', [
            'student' => $student,
            'user' => $user,
            'registeredCourses' => $registeredCourses,
            'session' => activeSession(),
            'semester' => activeSemester(),
            'schoolName' => \App\Models\SystemSetting::get('school_name', 'University of Offa'),
        ]);

        return $pdf->download('course_form_'.$student->user->full_name.'.pdf');
    }

    public function removeCourse($id)
    {
        $registration = CourseRegistration::findOrFail($id);

        // Verify the course belongs to the authenticated student
        if ($registration->student_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $courseName = $registration->course->course_code;
        $registration->delete();

        return redirect()->back()->with('success', "Course {$courseName} has been removed successfully.");
    }
}

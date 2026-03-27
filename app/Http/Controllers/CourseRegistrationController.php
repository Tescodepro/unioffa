<?php

namespace App\Http\Controllers;

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

        // Check payment status
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

        if ($courseRegistrationSetting && now()->gt($courseRegistrationSetting->closing_date)) {
            $hasPaidLateFee = \App\Models\Transaction::where('user_id', $user->id)
                ->where('payment_type', 'late_course_registration')
                ->where('payment_status', 1)
                ->where('session', $currentSession)
                ->where('semester', $currentSemester)
                ->exists();
        }

        if (! $payment_status['allCleared']) {
            $courses = collect();

            // Allow showing courses if tuition is >= 60% and it's 1st semester
            $isRegularOrDiploma = in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA']);

            $hasPartialTuitionAccess = $isRegularOrDiploma
                ? ($tuitionPercentage >= 60 && strtolower($currentSemester) === '1st')
                : (isset($keyedStatus['tuition']) && $keyedStatus['tuition']['percentage_paid'] >= 60 && strtolower($currentSemester) === '1st');

            if ($hasPartialTuitionAccess) {
                $courses = Course::where('active_for_register', 1)
                    ->where('level', $selectedLevel)
                    ->where(function ($q) use ($currentSemester, $isRegularOrDiploma, $tuitionPercentage) {
                        if ($isRegularOrDiploma && $tuitionPercentage >= 100) {
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
                'hasPaidLateFee'
            ))->with('error', $hasPartialTuitionAccess ? null : 'You must clear all outstanding payments before registering courses.');
        }

        $isRegularOrDiploma = in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA']);
        $courses = Course::where('active_for_register', 1)
            ->where('level', $selectedLevel)
            ->where(function ($q) use ($currentSemester, $isRegularOrDiploma, $tuitionPercentage) {
                if ($isRegularOrDiploma && $tuitionPercentage >= 100) {
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
            'hasPaidLateFee'
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

        // Check payment status before allowing registration
        $paymentStatusService = new PaymentStatusService;
        $currentSession = activeSession()->name;
        $rawStatus = $paymentStatusService->getStatus($student, $currentSession);
        $keyedStatus = collect($rawStatus)->keyBy('payment_type')->toArray();
        $isRegularOrDiploma = in_array(strtoupper($student->programme), ['REGULAR', 'DIPLOMA']);

        $tuitionPercentage = 100;
        if (isset($keyedStatus['tuition'])) {
            $tuitionPercentage = $keyedStatus['tuition']['percentage_paid'];
        }

        if (! $paymentStatusService->hasClearedAll($student, $currentSession)) {
            $canRegisterPartial = $isRegularOrDiploma && $tuitionPercentage >= 60;

            // Ensure no other fees except tuition are pending for partial access
            $otherPending = collect($keyedStatus)->except('tuition')->count();

            if (! $canRegisterPartial || $otherPending > 0) {
                return redirect()->back()->with('error', 'You must clear all outstanding payments before registering courses.');
            }
        }

        $currentSession = activeSession()->name;
        $currentSemester = activeSemester()->code ?? (activeSemester()->name ?? '1st');
        $successCount = 0;

        // Unit Limit Validation
        $maxSemesterUnits = (int) \App\Models\SystemSetting::get('max_units_per_semester', 24);
        $maxSessionUnits = (int) \App\Models\SystemSetting::get('max_units_per_session', 48);

        // Calculate units for new courses only
        $newRequestedCourseIds = [];
        $newUnits = 0;

        foreach ($request->courses as $courseId) {
            $course = Course::find($courseId);
            if (! $course || ! $course->active_for_register) {
                continue;
            }

            // Enforce semester restriction for REGULAR/DIPLOMA with < 100% tuition
            if ($isRegularOrDiploma && $tuitionPercentage < 100 && strtolower($course->semester) === '2nd') {
                return redirect()->back()->with('error', "You must pay 100% of your tuition to register for 2nd semester courses like {$course->course_code}.");
            }

            $exists = CourseRegistration::where('student_id', $user->id)
                ->where('course_id', $courseId)
                ->where('session', $currentSession)
                ->where('semester', $course->semester)
                ->exists();

            if (! $exists) {
                $newRequestedCourseIds[] = $courseId;
                $newUnits += $course->course_unit;
            }
        }

        if (empty($newRequestedCourseIds)) {
            return redirect()->back()->with('info', 'No new courses were selected for registration.');
        }

        // Check semester total (using course semester)
        // Note: For partial registration, they can only do 1st semester anyway
        $semesterForLimit = $currentSemester; // Default to active
        $currentSemesterTotal = CourseRegistration::where('student_id', $user->id)
            ->where('session', $currentSession)
            ->where('semester', $semesterForLimit)
            ->sum('course_unit');

        if (($currentSemesterTotal + $newUnits) > $maxSemesterUnits) {
            return redirect()->back()->with('error', 'Limit Exceeded: Adding these courses would bring your total to '.($currentSemesterTotal + $newUnits)." units, which exceeds the semester limit of {$maxSemesterUnits}.");
        }

        // Check current session total
        $currentSessionTotal = CourseRegistration::where('student_id', $user->id)
            ->where('session', $currentSession)
            ->sum('course_unit');

        if (($currentSessionTotal + $newUnits) > $maxSessionUnits) {
            return redirect()->back()->with('error', 'Limit Exceeded: Adding these courses would bring your total to '.($currentSessionTotal + $newUnits)." units, which exceeds the session limit of {$maxSessionUnits}.");
        }

        foreach ($newRequestedCourseIds as $courseId) {
            $course = Course::findOrFail($courseId);

            CourseRegistration::create([
                'student_id' => $user->id,
                'matric_no' => $student->matric_no,
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'course_title' => $course->course_title,
                'course_unit' => $course->course_unit,
                'session' => $currentSession,
                'semester' => $course->semester,
                'status' => 'pending',
            ]);
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

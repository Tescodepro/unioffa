<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
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

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Check payment status first
        $paymentStatusService = new PaymentStatusService;
        $payment_status = [
            'status' => $paymentStatusService->getStatus($student, activeSession()->name),
            'allCleared' => $paymentStatusService->hasClearedAll($student, activeSession()->name),
            'outstanding' => $paymentStatusService->getTotalOutstanding($student, activeSession()->name),
        ];

        // If payment not cleared, don't allow course registration
        if (!$payment_status['allCleared']) {
            return view('student.course-registration', [
                'courses' => collect(),
                'registrations' => collect(),
                'registeredCourses' => collect(),
                'payment_status' => $payment_status,
                'error' => 'You must clear all outstanding payments before registering courses.',
            ]);
        }

        $departmentId = $student->department_id;
        $level = $student->level;
        if (($student->entry_mode == 'DE' or $student->entry_mode == 'TRANSFER') and $student->admission_session == activeSession()->name) {
            $level = 200;
        }
        $currentSession = activeSession()->name;
        $currentSemester = activeSemester()->code;

        // Load available courses
        // dd($level);
        $courses = Course::where('active_for_register', 1)
            ->where('level', $level)
            ->where(function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId)
                    ->orWhereJsonContains('other_departments', $departmentId);
            })
            ->get();

        // Get all registrations for search
        $query = CourseRegistration::where('student_id', $user->id);
        if ($request->has('search')) {
            $query->where('course_title', 'like', '%' . $request->search . '%')
                ->orWhere('course_code', 'like', '%' . $request->search . '%');
        }
        $registrations = $query->with('course', 'session', 'semester')->get();

        // Get registered courses for current session/semester only
        $registeredCourses = CourseRegistration::with('course')
            ->where('student_id', $user->id)
            ->where('session', $currentSession)
            ->where('semester', $currentSemester)
            ->get();

        return view('student.course-registration', compact('courses', 'registrations', 'registeredCourses', 'payment_status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
        ]);

        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        // Check payment status before allowing registration
        $paymentStatusService = new PaymentStatusService;
        if (!$paymentStatusService->hasClearedAll($student, activeSession()->name)) {
            return redirect()->back()->with('error', 'You must clear all outstanding payments before registering courses.');
        }

        $currentSession = activeSession()->name;
        $currentSemester = activeSemester()->code;
        $successCount = 0;

        foreach ($request->courses as $courseId) {
            $course = Course::findOrFail($courseId);

            // Verify course belongs to student's department/level
            if ($course->level != $student->level) {
                continue; // Skip if course is not for student's level
            }

            // Prevent duplicate registration
            $exists = CourseRegistration::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->where('session', $currentSession)
                ->where('semester', $currentSemester)
                ->exists();

            if (!$exists) {
                CourseRegistration::create([
                    'student_id' => $user->id,
                    'course_id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->course_title,
                    'course_unit' => $course->course_unit,
                    'session' => $currentSession,
                    'semester' => $currentSemester,
                    'status' => 'pending',
                ]);
                $successCount++;
            }
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

        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $registeredCourses = CourseRegistration::with('course')
            ->where('student_id', $user->id)
            ->where('session', activeSession()->name ?? null)
            ->where('semester', activeSemester()->code ?? null)
            ->get();

        $pdf = Pdf::loadView('student.course-registration-printable', [
            'student' => $student,
            'user' => $user,
            'registeredCourses' => $registeredCourses,
            'session' => activeSession(),
            'semester' => activeSemester(),
        ]);

        return $pdf->download('course_form_' . $student->first_name . '_' . $student->last_name . '.pdf');
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

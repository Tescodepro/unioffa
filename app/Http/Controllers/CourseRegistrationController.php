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
        $departmentId = $student->department_id;
        $level = $student->level;

        // Load available courses
        // $courses = Course::where('department_id', $departmentId)
        //     ->where('level', $level)
        //     ->get();
        $courses = Course::get();

        // Search filter for registered courses
        $query = CourseRegistration::where('student_id', $user->id);
        if ($request->has('search')) {
            $query->where('course_title', 'like', '%'.$request->search.'%')
                ->orWhere('course_code', 'like', '%'.$request->search.'%');
        }

        $registrations = $query->with('course', 'session', 'semester')->get();

        $registeredCourses = CourseRegistration::with('course')
            ->where('student_id', $user->id)
            ->where('session_id', activeSession()->id ?? null)
            ->where('semester_id', activeSemester()->id ?? null)
            ->get();

        // Check payment status and update course registrations accordingly
        $paymentStatusService = new PaymentStatusService;
        $payment_status = [
            'status' => $paymentStatusService->getStatus($student, activeSession()->name),
            'allCleared' => $paymentStatusService->hasClearedAll($student, activeSession()->name),
            'outstanding' => $paymentStatusService->getTotalOutstanding($student, activeSession()->name)
        ];

        return view('student.course-registration', compact('courses', 'registrations', 'registeredCourses', 'payment_status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'courses' => 'required|array',
        ]);

        $student = Auth::user();

        foreach ($request->courses as $courseId) {
            $course = Course::findOrFail($courseId);

            // Prevent duplicate registration
            $exists = CourseRegistration::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('session_id', activeSession()->id)
                ->where('semester_id', activeSemester()->id)
                ->exists();

            if (! $exists) {
                CourseRegistration::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'course_code' => $course->course_code,
                    'course_title' => $course->course_title,
                    'course_unit' => $course->course_unit,
                    'session_id' => activeSession()->id,
                    'semester_id' => activeSemester()->id,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Selected courses registered successfully.');
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
        $student = Student::where('user_id', $user->id)->with('department')->first();

        $registeredCourses = CourseRegistration::with('course')
            ->where('student_id', $user->id)
            ->where('session_id', activeSession()->id ?? null)
            ->where('semester_id', activeSemester()->id ?? null)
            ->get();

        $pdf = Pdf::loadView('student.course-registration-printable', [
            'student' => $student,
            'user' => $user,
            'registeredCourses' => $registeredCourses,
            'session' => activeSession(),
            'semester' => activeSemester(),
        ]);

        return $pdf->download('course_form_'.$student->full_name.'.pdf');
    }
}

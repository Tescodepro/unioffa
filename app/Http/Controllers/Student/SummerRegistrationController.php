<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\SummerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SummerRegistrationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $currentSession = activeSession();
        $courses = Course::where('active_for_register', 1)->get();

        // Check if student has an existing summer registration
        $summerRegistration = SummerRegistration::where('student_id', $user->id)
            ->where('academic_session', $currentSession->name ?? null)
            ->first();

        $registeredCourses = [];
        if ($summerRegistration && $summerRegistration->payment_status === 'paid') {
            $registeredCourses = CourseRegistration::with('course')
                ->where('student_id', $user->id)
                ->where('session', $currentSession->name ?? '')
                ->where('semester', '3rd') // or 'Summer Semester' based on helpers
                ->get();
        }

        return view('student.summer-registration.index', compact('courses', 'summerRegistration', 'registeredCourses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:courses,id',
            'reason_for_increase' => 'nullable|string',
        ]);

        $user = Auth::user();
        $currentSession = activeSession();

        if (! $currentSession) {
            return redirect()->back()->with('error', 'No active academic session found.');
        }

        $courseIds = array_unique($request->courses);
        $courseCount = count($courseIds);

        $summaryFee = 30000.00;
        $courseFeeTotal = $courseCount * 20000.00;
        $totalFee = $summaryFee + $courseFeeTotal;

        $status = 'pending_payment';
        if ($courseCount > 6) {
            $status = 'pending_vc_approval';
            if (empty($request->reason_for_increase)) {
                return redirect()->back()->with('error', 'A reason must be provided when requesting more than 6 courses.');
            }
        }

        $registration = SummerRegistration::updateOrCreate(
            [
                'student_id' => $user->id,
                'academic_session' => $currentSession->name,
                'payment_status' => 'pending',
            ],
            [
                'courses' => $courseIds,
                'summary_fee' => $summaryFee,
                'course_fee_total' => $courseFeeTotal,
                'total_fee' => $totalFee,
                'status' => $status,
                'reason_for_increase' => $request->reason_for_increase,
            ]
        );

        if ($status === 'pending_vc_approval') {
            return redirect()->route('student.summer.index')->with('success', 'Your request for more than 6 courses has been submitted to the VC for approval.');
        }

        return redirect()->route('student.summer.payment', $registration->id);
    }

    public function payment($id)
    {
        $registration = SummerRegistration::where('student_id', Auth::id())->findOrFail($id);

        if ($registration->status === 'pending_vc_approval') {
            return redirect()->route('student.summer.index')->with('error', 'Your request is pending VC approval.');
        }

        if ($registration->payment_status === 'paid') {
            return redirect()->route('student.summer.index')->with('info', 'Payment has already been made.');
        }

        return view('student.summer-registration.payment', compact('registration'));
    }

    // In a real scenario, this would integrate with the payment gateway callback.
    // Assuming a simplified mock or integration here.
    public function simulatePayment($id)
    {
        $registration = SummerRegistration::where('student_id', Auth::id())->findOrFail($id);

        if ($registration->payment_status === 'paid') {
            return redirect()->route('student.summer.index');
        }

        $registration->update([
            'payment_status' => 'paid',
            'status' => 'registered',
        ]);

        $user = Auth::user();
        $student = $user->student;
        $currentSession = activeSession();

        // Register the actual courses
        foreach ($registration->courses as $courseId) {
            $course = Course::find($courseId);
            if ($course) {
                CourseRegistration::firstOrCreate([
                    'student_id' => $user->id,
                    'course_id' => $course->id,
                    'session' => $currentSession->name,
                    'semester' => '3rd', // Summer semester
                    'matric_no' => $student->matric_no,
                    'course_code' => $course->course_code,
                    'course_title' => $course->course_title,
                    'course_unit' => $course->course_unit,
                    'status' => 'approved', // Summer courses might be auto-approved
                ]);
            }
        }

        return redirect()->route('student.summer.index')->with('success', 'Payment successful. Courses registered.');
    }
}

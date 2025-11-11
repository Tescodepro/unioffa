<?php

namespace App\Http\Controllers\Staff\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ResultController extends Controller
{
    // Display the upload page
    public function uploadPage()
    {
        $user = auth()->user();

        // ✅ Load courses based on user role
        if ($user->hasRole('lecturer')) {
            $courses = $user->courses()->orderBy('course_title')->get();

            $sessions = AcademicSession::where(function ($query) use ($user) {
                $query->where('status_upload_result', '1')
                    ->orWhereJsonContains('lecturar_ids', (string) $user->id);
            })
                ->orderBy('name', 'desc')
                ->get();
            $semesters = AcademicSemester::where(function ($query) use ($user) {
                $query->where('status_upload_result', '1')
                    ->orWhereJsonContains('lecturar_ids', (string) $user->id);
            }) // Optional: only show active semesters
                ->orderBy('name')
                ->get();
        } else {
            $courses = Course::orderBy('course_title')->get();
            $sessions = AcademicSession::orderBy('name', 'desc')->get();
            $semesters = AcademicSemester::orderBy('name')->get();
        }

        return view('staff.lecturer.results.upload', compact('courses', 'sessions', 'semesters'));
    }

    // Process uploaded Excel
    public function processUpload(Request $request)
    {
        $request->validate([
            'course_id' => 'required|uuid',
            'session' => 'required|string',
            'semester' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = auth()->user();
        if ($user->hasRole('lecturer') && ! $user->isAssignedToCourse($course->id)) {
            return back()->with('error', 'You are not authorized to upload results for this course.');
        }

        // ✅ Read Excel
        $rows = Excel::toArray([], $request->file('file'))[0];

        if (empty($rows)) {
            return back()->with('error', 'The uploaded file is empty.');
        }

        // ✅ Extract and normalize headers
        $headers = array_shift($rows); // Remove first row (headers)
        $normalizedHeaders = array_map(function ($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);

        // ✅ Map data rows with normalized headers
        $normalized = collect($rows)->map(function ($row) use ($normalizedHeaders) {
            return array_combine($normalizedHeaders, $row);
        })->filter(function ($row) {
            // Skip empty rows
            return ! empty(array_filter($row));
        });

        // ✅ Initialize report
        $report = [
            'uploaded' => [],
            'skipped_not_student' => [],
            'skipped_not_registered' => [],
            'errors' => [],
        ];

        foreach ($normalized as $index => $row) {
            // Skip empty or malformed rows
            if (empty($row['matric_no'])) {
                continue;
            }

            $matric_no = trim($row['matric_no']);
            $ca = floatval($row['ca'] ?? 0);
            $exam = floatval($row['examination'] ?? 0);
            $total = $ca + $exam;

            // 1️⃣ Validate student exists
            $student = Student::where('matric_no', $matric_no)->first();
            if (! $student) {
                $report['skipped_not_student'][] = "Matric No: {$matric_no} — not found in student records.";

                continue;
            }

            // 2️⃣ Validate course registration
            $registered = CourseRegistration::where([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'session' => $request->session,
                'semester' => $request->semester,
            ])->exists();

            if (! $registered) {
                $report['skipped_not_registered'][] = "Matric No: {$matric_no} — did not register {$course->course_code} ({$course->course_title}) for {$request->semester} Semester, {$request->session} session.";

                continue;
            }

            // 3️⃣ Compute grade
            if ($total >= 70) {
                $grade = 'A';
            } elseif ($total >= 60) {
                $grade = 'B';
            } elseif ($total >= 50) {
                $grade = 'C';
            } elseif ($total >= 45) {
                $grade = 'D';
            } else {
                $grade = 'F';
            }

            $remark = $grade != 'F' ? 'Pass' : 'Fail';

            try {
                // 4️⃣ Insert or update result
                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'session' => $request->session,
                        'semester' => $request->semester,
                    ],
                    [
                        'matric_no' => $student->matric_no,
                        'course_code' => $course->course_code,
                        'course_title' => $course->course_title,
                        'course_unit' => $course->course_unit,
                        'ca' => $ca,
                        'exam' => $exam,
                        'total' => $total,
                        'grade' => $grade,
                        'remark' => $remark,
                        'uploaded_by' => auth()->id(),
                        'status' => 'pending',
                    ]
                );

                $report['uploaded'][] = "Matric No: {$matric_no} — uploaded successfully.";
            } catch (\Exception $e) {
                $report['errors'][] = "Matric No: {$matric_no} — error: ".$e->getMessage();
            }
        }

        // ✅ Build summary
        $summary = '
    ✅ Uploaded: '.count($report['uploaded']).'
    ❌ Not Students: '.count($report['skipped_not_student']).'
    ⚠️ Not Registered: '.count($report['skipped_not_registered']).'
    🧯 Errors: '.count($report['errors']);

        // ✅ Flash report to session (for detailed display)
        session()->flash('upload_report', $report);

        return back()->with('success', 'Results processed successfully. '.$summary);
    }

    public function downloadSheet(Request $request)
    {
        $request->validate([
            'course_id' => 'required|uuid',
            'session' => 'required|string',
            'semester' => 'required|string',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Fetch registered students
        $students = CourseRegistration::where('course_id', $course->id)
            ->where('session', $request->session)
            ->where('semester', $request->semester)
            ->with('student')
            ->get();

        // Transform data into simple array
        $data = $students->map(function (CourseRegistration $reg) {
            return [
                'matric_no' => $reg->student->matric_no ?? '',
                'ca' => 0,
                'exam' => 0,
            ];
        })->toArray(); // ✅ convert to plain array

        // Define headings (optional, but helpful)
        $headings = ['Matric No', 'CA', 'Examination'];

        // Export and download
        return Excel::download(new \App\Exports\ArrayExport($data, $headings), "{$course->course_code}_result_sheet.xlsx");
    }

    public function approveResults(Request $request) {}

    public function viewuploadReport(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('lecture')) {
            $courses = $user->courses()->orderBy('course_title')->get();
            $sessions = AcademicSession::where(function ($q) use ($user) {
                $q->where('status_upload_result', '1')
                    ->orWhereJsonContains('lecturar_ids', (string) $user->id);
            })->orderBy('name', 'desc')->get();

            $semesters = AcademicSemester::where(function ($q) use ($user) {
                $q->where('status_upload_result', '1')
                    ->orWhereJsonContains('lecturar_ids', (string) $user->id);
            })->orderBy('name')->get();
        } else {
            $courses = Course::orderBy('course_title')->get();
            $sessions = AcademicSession::orderBy('name', 'desc')->get();
            $semesters = AcademicSemester::orderBy('name')->get();
        }

        // If user submitted the form, fetch results too
        $results = collect();
        $course = null;

        if ($request->has(['course_id', 'session', 'semester'])) {
            $request->validate([
                'course_id' => 'required|uuid|exists:courses,id',
                'session' => 'required|string',
                'semester' => 'required|string',
            ]);

            $course = Course::find($request->course_id);

            $results = Result::where([
                'course_id' => $course->id,
                'session' => $request->session,
                'semester' => $request->semester,
                'uploaded_by' => $user->id,
            ])->orderBy('matric_no')->get();
        }

        return view('staff.lecturer.results.view-uploaded', compact(
            'courses', 'sessions', 'semesters', 'results', 'course'
        ));
    }

    public function viewUploadedResults(Request $request)
    {
        $request->validate([
            'course_id' => 'required|uuid|exists:courses,id',
            'session' => 'required|string',
            'semester' => 'required|string',
        ]);

        $course = Course::findOrFail($request->course_id);
        $user = auth()->user();

        if ($user->hasRole('lecturer')) {
            $isAssigned = $user->courses()->where('course_id', $course->id)->exists();
            if (! $isAssigned) {
                return back()->with('error', 'You are not allowed to view results for this course.');
            }
        }

        $results = Result::where([
            'course_id' => $course->id,
            'session' => $request->session,
            'semester' => $request->semester,
            'uploaded_by' => $user->id,
        ])->orderBy('matric_no')->get();

        if ($results->isEmpty()) {
            return back()->with('error', 'No uploaded results found for your selection.');
        }

        return view('staff.lecturer.results.view-uploaded', compact('course', 'results'));
    }

    public function downloadResults(Request $request)
    {
        // Validate input
        $request->validate([
            'course_id' => 'required|uuid|exists:courses,id',
            'session' => 'required|string',
            'semester' => 'required|string',
        ]);

        try {
            $course = Course::findOrFail($request->course_id);
            $user = auth()->user();

            // ✅ Check if lecturer is authorized to download results for this course
            if ($user->hasRole('lecturer')) {
                $isAssigned = $user->courses()->where('course_id', $course->id)->exists();

                if (! $isAssigned) {
                    return redirect()->back()->with('error', 'You are not authorized to download results for this course.');
                }
            }

            // ✅ Fetch results uploaded by current user for the specified course, session, and semester
            $results = Result::where([
                'course_id' => $course->id,
                'session' => $request->session,
                'semester' => $request->semester,
                'uploaded_by' => $user->id, // ✅ Only results uploaded by current user
            ])
                ->orderBy('matric_no')
                ->get();

            // Check if results exist
            if ($results->isEmpty()) {
                return redirect()->back()->with('error', 'No results found that you uploaded for the selected course, session, and semester.');
            }

            // ✅ Prepare data for Excel export
            $data = [];

            // Add header row
            $headings = ['Matric No', 'CA', 'Examination', 'Total', 'Grade', 'Remark', 'Status'];

            // Add data rows
            foreach ($results as $result) {
                $data[] = [
                    $result->matric_no,
                    $result->ca,
                    $result->exam,
                    $result->total,
                    $result->grade,
                    $result->remark,
                    ucfirst($result->status), // Show status (pending, approved, rejected)
                ];
            }

            // ✅ Generate filename
            $filename = sprintf(
                '%s_%s_%s_%s_MyResults.xlsx',
                $course->course_code,
                str_replace('/', '-', $request->session),
                $request->semester,
                now()->format('Y-m-d_His')
            );

            // ✅ Export to Excel
            return Excel::download(
                new \App\Exports\ArrayExport($data, $headings),
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Failed to download results: '.$e->getMessage());

            return redirect()->back()->with('error', 'Failed to download results. Please try again.');
        }
    }
}

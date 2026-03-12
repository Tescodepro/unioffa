<?php

namespace App\Http\Controllers\Staff\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Department;
use App\Models\Result;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

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
            'course_code' => 'required|string',
            'session' => 'required|string',
            'semester' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $course = Course::where('course_code', $request->course_code)->firstOrFail();
        $user = auth()->user();
        if ($user->hasRole('lecturer') && !$user->isAssignedToCourse($course->id)) {
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
            return !empty(array_filter($row));
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

            // 1️⃣ Validate scores <= 100
            if ($total > 100) {
                $report['errors'][] = "Matric No: {$matric_no} — Total score ({$total}) cannot exceed 100.";

                continue;
            }

            // 2️⃣ Validate student exists
            $student = Student::where('matric_no', $matric_no)->first();
            if (!$student) {
                $report['skipped_not_student'][] = "Matric No: {$matric_no} — not found in student records.";

                continue;
            }

            // 2️⃣ Validate course registration
            $registered = CourseRegistration::where([
                'matric_no' => $matric_no,
                'course_code' => $course->course_code,
                'session' => $request->input('session'),
                'semester' => $request->input('semester'),
            ])->exists();

            if (!$registered) {
                $report['skipped_not_registered'][] = "Matric No: {$matric_no} — did not register {$course->course_code} ({$course->course_title}) for {$request->input('semester')} Semester, {$request->input('session')} session.";

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
                        'student_id' => $student->user_id,
                        'matric_no' => $student->matric_no,
                        'course_id' => $course->id,
                        'course_code' => $course->course_code,
                        'session' => $request->input('session'),
                        'semester' => $request->input('semester'),
                    ],
                    [
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
                $report['errors'][] = "Matric No: {$matric_no} — error: " . $e->getMessage();
            }
        }

        // ✅ Build summary
        $summary = '
            ✅ Uploaded: ' . count($report['uploaded']) . '
            ❌ Not Students: ' . count($report['skipped_not_student']) . '
            ⚠️ Not Registered: ' . count($report['skipped_not_registered']) . '
            🧯 Errors: ' . count($report['errors']);

        // ✅ Flash report to session (for detailed display)
        session()->flash('upload_report', $report);

        return back()->with('success', 'Results processed successfully. ' . $summary);
    }

    public function downloadSheet(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string',
            'session' => 'required|string',
            'semester' => 'required|string',
        ]);

        $courseCode = $request->course_code;

        // Fetch registered students using course_code, including the student and profile details
        $students = CourseRegistration::where('course_code', $courseCode)
            ->where('session', $request->input('session'))
            ->where('semester', $request->input('semester'))
            ->with('student')
            ->get();

        // Check for existing results for this course, session, and semester
        $existingResults = Result::where('course_code', $courseCode)
            ->where('session', $request->input('session'))
            ->where('semester', $request->input('semester'))
            ->get()
            ->keyBy('student_id'); // Key by student_id for reliable lookup

        // Transform data into simple array
        $data = $students->map(function (CourseRegistration $reg) use ($existingResults) {
            // Find existing result using student_id
            $result = $existingResults->get($reg->student_id);

            $ca = '0';
            $exam = '0';

            if ($result) {
                $ca = is_numeric($result->ca) ? (string) (float) $result->ca : '0';
                $exam = is_numeric($result->exam) ? (string) (float) $result->exam : '0';
            }

            return [
                'matric_no' => $reg->matric_no ?? '', // Keep original case for display
                'name' => $reg->student ? trim($reg->student->last_name . ' ' . $reg->student->first_name) : '',
                'ca' => $ca,
                'exam' => $exam,
            ];
        })->toArray(); // ✅ convert to plain array

        // Define headings (optional, but helpful)
        $headings = ['Matric No', 'Name', 'CA', 'Examination'];

        // Export and download
        return Excel::download(new \App\Exports\ArrayExport($data, $headings), "{$courseCode}_result_sheet.xlsx");
    }

    public function approveResults(Request $request)
    {
    }

    public function viewUploadedResults(Request $request)
    {
        $user = auth()->user();

        // 1. Load Filter Data (always needed for the dropdowns)
        if ($user->hasRole('lecturer')) {
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

        // 2. Load Results if filters are provided
        $results = collect();
        $course = null;

        if ($request->has(['course', 'session', 'semester'])) {
            $request->validate([
                'course' => 'required|string|exists:courses,course_code',
                'session' => 'required|string',
                'semester' => 'required|string',
            ]);

            $course = Course::where('course_code', $request->course)->firstOrFail();

            if ($user->hasRole('lecturer')) {
                $isAssigned = $user->courses()->where('course_id', $course->id)->exists();
                if (!$isAssigned) {
                    return back()->with('error', 'You are not allowed to view results for this course.');
                }
            }

            $results = Result::with('user')->where([
                'course_code' => $course->course_code,
                'session' => $request->input('session'),
                'semester' => $request->input('semester'),
                'uploaded_by' => $user->id,
            ])->orderBy('matric_no')->get();

            if ($results->isEmpty()) {
                session()->flash('warning', 'No uploaded results found for your selection.');
            }
        }

        return view('staff.lecturer.results.view-uploaded', compact(
            'courses',
            'sessions',
            'semesters',
            'results',
            'course'
        ));
    }

    public function printUploadedResults(Request $request)
    {
        $request->validate([
            'course' => 'required',
            'session' => 'required',
            'semester' => 'required',
        ]);

        $user = auth()->user();
        $course = Course::with('department.faculty')->where('course_code', $request->course)->firstOrFail();

        $results = Result::with('user')->where([
            'course_code' => $course->course_code,
            'session' => $request->input('session'),
            'semester' => $request->input('semester'),
            'uploaded_by' => $user->id,
        ])->orderBy('matric_no')->get();

        return view('staff.lecturer.results.print-uploaded', compact('results', 'course'));
    }

    public function printSummaryReport(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'level' => 'required',
        ]);

        $selectedDepartment = Department::with('faculty')->findOrFail($request->department_id);
        $studentTypeId = UserType::where('name', 'student')->value('id');


        $students = User::query()
            ->join('students', 'users.id', '=', 'students.user_id')
            ->where('users.user_type_id', $studentTypeId)
            ->where('students.department_id', $request->department_id)
            ->where('students.level', $request->level)
            ->select('users.*')
            ->with('results')
            ->get();

        $students->transform(function ($student) {
            $totalUnitsOffered = 0;
            $totalUnitsPassed = 0;
            $totalGradePoints = 0;

            foreach ($student->results as $result) {
                $unit = (int) $result->course_unit;
                $score = (float) $result->total;
                $totalUnitsOffered += $unit;
                
                // Use dynamic grading system
                $points = \App\Models\GradingSystem::getPoint($score);
                
                $totalGradePoints += ($unit * $points);
                if ($score >= 40) {
                    $totalUnitsPassed += $unit;
                }
            }

            $cgpa = $totalUnitsOffered > 0 ? $totalGradePoints / $totalUnitsOffered : 0;
            $student->units_offered = $totalUnitsOffered;
            $student->units_passed = $totalUnitsPassed;
            $student->cgpa = number_format($cgpa, 2);
            return $student;
        });

        return view('staff.lecturer.results.print-summary', compact('students', 'selectedDepartment'));
    }

    public function downloadResults(Request $request)
    {
        // Validate input
        $request->validate([
            'course_id' => 'required|string|exists:courses,course_code',
            'session' => 'required|string',
            'semester' => 'required|string',
        ]);

        try {
            $course = Course::where('course_code', $request->course_id)->firstOrFail();
            $user = auth()->user();

            // ✅ Check if lecturer is authorized to download results for this course
            if ($user->hasRole('lecturer')) {
                $isAssigned = $user->courses()->where('course_id', $course->id)->exists();

                if (!$isAssigned) {
                    return redirect()->back()->with('error', 'You are not authorized to download results for this course.');
                }
            }

            // ✅ Fetch results uploaded by current user for the specified course, session, and semester
            $results = Result::where([
                'course_id' => $course->id,
                'session' => $request->input('session'),
                'semester' => $request->input('semester'),
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
                str_replace('/', '-', $request->input('session')),
                $request->input('semester'),
                now()->format('Y-m-d_His')
            );

            // ✅ Export to Excel
            return Excel::download(
                new \App\Exports\ArrayExport($data, $headings),
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('Failed to download results: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to download results. Please try again.');
        }
    }

    public function downloadBacklogTemplate()
    {
        $path = public_path('templates/backlog_templateoffa.xlsx');

        return response()->download($path);
    }

    public function showBacklogUploadPage()
    {
        return view('staff.lecturer.results.backlog-upload');
    }

    public function processBacklogUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx'],
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $logs = [
                'saved_records' => [],
                'updated_records' => [],
                'missing_values' => [],
                'invalid_rows' => [],
                'general_errors' => [],
            ];

            foreach ($sheet as $index => $row) {

                if ($index === 1) {
                    continue; // skip header
                }

                $matric = trim($row['A'] ?? '');

                // Format Course Code to ALWAYS be "XXX 000"
                $rawCourseCode = strtoupper(trim($row['B'] ?? ''));
                $courseCode = preg_replace('/^([A-Z]+)\s*(\d+)$/', '$1 $2', $rawCourseCode);

                $courseTitle = trim($row['C'] ?? '');
                $courseUnit = trim($row['D'] ?? '');
                $session = trim($row['E'] ?? '');

                // Semester validation
                $semester = strtolower(trim($row['F'] ?? ''));
                if (!in_array($semester, ['1st', '2nd', '3rd'])) {
                    $logs['invalid_rows'][] = "Row $index: Invalid semester '$semester'. Must be 1st, 2nd, or 3rd.";

                    continue;
                }

                // Safely convert CA and Exam to numbers
                $ca = is_numeric(trim($row['G'] ?? '')) ? (float) trim($row['G']) : null;
                $exam = is_numeric(trim($row['H'] ?? '')) ? (float) trim($row['H']) : null;

                $missing = [];
                if ($matric === '') {
                    $missing[] = 'Matric Number (Column A)';
                }
                if ($courseCode === '') {
                    $missing[] = 'Course Code (Column B)';
                }
                if ($ca === null) {
                    $missing[] = 'CA Score (Column G)';
                }
                if ($exam === null) {
                    $missing[] = 'Exam Score (Column H)';
                }

                if (!empty($missing)) {
                    $logs['missing_values'][] = "Row $index is missing or invalid: " . implode(', ', $missing);

                    continue;
                }

                // Score Validation
                if ($ca + $exam > 100) {
                    $logs['invalid_rows'][] = "Row $index: Total score (" . ($ca + $exam) . ') cannot exceed 100.';

                    continue;
                }

                $student = User::where('username', $matric)->first();
                $course = Course::where('course_code', $courseCode)->first();

                if (!$student) {
                    $logs['invalid_rows'][] = "Row $index: Student with matric $matric not found.";

                    continue;
                }

                if (!$course) {
                    $logs['invalid_rows'][] = "Row $index: Course $courseCode not found.";

                    continue;
                }

                try {
                    $result = Result::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'matric_no' => $student->username,
                            'course_id' => $course->id,
                            'course_code' => $course->course_code ?? $courseCode,
                            'session' => $session,
                            'semester' => $semester,
                        ],
                        [
                            'course_title' => $course->course_title ?? $courseTitle,
                            'course_unit' => $course->course_unit ?? $courseUnit,
                            'ca' => $ca,
                            'exam' => $exam,
                            'total' => $ca + $exam,
                            'uploaded_by' => auth()->id(),
                            'status' => 'pending',
                        ]
                    );

                    if ($result->wasRecentlyCreated) {
                        $logs['saved_records'][] = "✅ Row $index: Result for {$student->username} in {$course->course_code} has been successfully created.";
                    } else {
                        $logs['updated_records'][] = "✏️ Row $index: Result for {$student->username} in {$course->course_code} was updated with new scores (CA: {$ca}, Exam: {$exam}).";
                    }

                } catch (Throwable $e) {
                    $logs['invalid_rows'][] = "Row $index failed to save or update. Error: " . $e->getMessage();
                }
            }
            session()->put('upload_logs', $logs);

            return back()->with('success', 'Upload process completed. Check the logs for details.');

        } catch (Throwable $e) {
            return back()->with('error', 'Unable to read the file. Please check the format.');
        }
    }

    public function viewTranscript(User $student)
    {
        // Load results with course details
        $results = Result::where('student_id', $student->id)
            ->with('course')
            ->orderBy('session')
            ->orderBy('semester')
            ->get();

        // Optional: group by session for a proper transcript layout
        $resultsBySession = $results->groupBy('session');

        return view('staff.lecturer.results.student-transcript', compact('student', 'resultsBySession'));
    }

    public function summaryByDepartment(Request $request)
    {
        $departments = Department::all();
        $levels = ['100', '200', '300', '400', '500'];

        $students = [];

        $selectedDepartment = null;
        if ($request->filled('department_id') && $request->filled('level')) {
            $selectedDepartment = Department::with('faculty')->find($request->department_id);

            // 1. Get UserType ID for 'Student'
            $studentTypeId = UserType::where('name', 'student')->value('id');


            // 2. Query Users by Joining the 'students' profile table
            $students = User::query()
                ->join('students', 'users.id', '=', 'students.user_id')
                ->where('users.user_type_id', $studentTypeId)
                ->where('students.department_id', $request->department_id)
                ->where('students.level', $request->level)
                ->select('users.*') // Select User fields (like username/matric)
                ->with('results')   // Load results using the new 'matric_no' relationship
                ->get();

            // 3. Process Metrics (Using columns directly from Results table)
            $students->transform(function ($student) {
                $totalUnitsOffered = 0;
                $totalUnitsPassed = 0;
                $totalGradePoints = 0;

                foreach ($student->results as $result) {
                    // Ensure we handle numeric values safely
                    $unit = (int) $result->course_unit;
                    $score = (float) $result->total;

                    // Add to total units offered
                    $totalUnitsOffered += $unit;

                    // --- 5.0 Grading System ---
                    // Use dynamic grading system
                    $points = \App\Models\GradingSystem::getPoint($score);

                    // Calculate Grade Points for this course
                    $totalGradePoints += ($unit * $points);

                    // Calculate Units Passed (Assuming 40 is pass mark)
                    if ($score >= 40) {
                        $totalUnitsPassed += $unit;
                    }
                }

                // Calculate CGPA
                $cgpa = $totalUnitsOffered > 0 ? $totalGradePoints / $totalUnitsOffered : 0;

                // Attach data to the student object
                $student->units_offered = $totalUnitsOffered;
                $student->units_passed = $totalUnitsPassed;
                $student->cgpa = number_format($cgpa, 2);

                return $student;
            });
        }

        return view('staff.lecturer.results.summary-report', compact('students', 'departments', 'levels', 'selectedDepartment'));
    }

    public function manageStatus(Request $request)
    {
        $departments = Department::all();
        $levels = ['100', '200', '300', '400', '500'];

        // Generate Sessions
        $currentYear = date('Y');
        $sessions = [];
        for ($i = 0; $i < 5; $i++) {
            $sessions[] = ($currentYear - $i) . '/' . ($currentYear - $i + 1);
        }

        $records = [];

        if ($request->filled(['department_id', 'level', 'session', 'semester'])) {

            $records = User::query()
                ->join('students', 'users.id', '=', 'students.user_id')
                ->join('results', 'users.username', '=', 'results.matric_no')
                ->where('students.department_id', $request->department_id)
                ->where('students.level', $request->level)
                ->where('results.session', $request->input('session'))
                ->where('results.semester', $request->input('semester'))
                ->select(
                    'users.first_name as name', // OR users.first_name, users.last_name if you have those columns
                    'users.username as matric_no',
                    'results.session',
                    'results.semester',

                    // --- Aggregated Calculations ---

                    // 1. Total Units Offered
                    DB::raw('SUM(results.course_unit) as total_units'),

                    // 2. Total Units Passed (Assuming 40 is pass mark)
                    DB::raw('SUM(CASE WHEN results.total >= 40 THEN results.course_unit ELSE 0 END) as units_passed'),

                    // 3. Total Raw Scores
                    DB::raw('SUM(results.ca) as total_ca'),
                    DB::raw('SUM(results.exam) as total_exam'),
                    DB::raw('SUM(results.total) as total_score'),

                    // 4. GPA Calculation (5.0 Scale)
                    DB::raw('SUM(results.course_unit * (CASE 
                    WHEN results.total >= 70 THEN 5 
                    WHEN results.total >= 60 THEN 4 
                    WHEN results.total >= 50 THEN 3 
                    WHEN results.total >= 45 THEN 2 
                    WHEN results.total >= 40 THEN 1 
                    ELSE 0 END
                )) / NULLIF(SUM(results.course_unit), 0) as gpa'),

                    // 5. Status
                    DB::raw('MAX(results.status) as current_status')
                )
                ->groupBy('users.id', 'users.first_name', 'users.username', 'results.session', 'results.semester')
                ->get();
        }

        return view('staff.lecturer.results.manage-status', compact('records', 'departments', 'levels', 'sessions'));
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'selected_students' => 'required|array',
            'status' => 'required|in:pending,recommended,approved,published',
            'session' => 'required',
            'semester' => 'required',
        ]);

        // Update results for ALL selected matric numbers in that session/semester
        Result::whereIn('matric_no', $request->selected_students)
            ->where('session', $request->input('session'))
            ->where('semester', $request->input('semester'))
            ->update(['status' => $request->status]);

        return back()->with('success', 'Bulk update successful! Changed status to ' . ucfirst($request->status));
    }

    // 2. Process the Status Update
    public function updateStatus(Request $request)
    {
        $request->validate([
            'matric_no' => 'required',
            'session' => 'required',
            'semester' => 'required',
            'status' => 'required|in:pending,recommended,approved,published',
        ]);

        // Bulk update results for this student, session, and semester
        $updated = Result::where('matric_no', $request->matric_no)
            ->where('session', $request->input('session'))
            ->where('semester', $request->input('semester'))
            ->update(['status' => $request->status]);

        return back()->with('success', "Updated $updated result(s) to '{$request->status}' for {$request->matric_no}.");
    }

    public function transcriptSearchPage()
    {
        return view('staff.lecturer.results.student-transcript');
    }

    public function searchTranscript(Request $request)
    {
        $request->validate([
            'matric' => 'required|string',
        ]);

        $student = User::where('username', $request->matric)->first();

        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $results = Result::where('matric_no', $request->matric)
            ->with('course')
            ->orderBy('session')
            ->orderBy('semester')
            ->get();

        $gradePoints = [
            'A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 0,
        ];

        $resultsBySession = $results->groupBy('session');
        $sessionMetrics = [];
        $cumulativeTCO = 0;
        $cumulativeTCP = 0;
        $cumulativeTWGP = 0;

        foreach ($resultsBySession as $session => $sessionResults) {
            $tco = 0; $tcp = 0; $twgp = 0;

            foreach ($sessionResults as $r) {
                $unit = (int) ($r->course_unit ?? ($r->course->course_unit ?? 0));
                
                // Use dynamic points from score
                $score = (float) ($r->total ?? (($r->ca ?? 0) + ($r->exam ?? 0)));
                $gp = \App\Models\GradingSystem::getPoint($score);
                
                $tco += $unit;
                if ($score >= 40) {
                    $tcp += $unit;
                }
                $twgp += ($unit * $gp);
            }

            $gpa = $tco > 0 ? round($twgp / $tco, 2) : 0.00;
            
            $cumulativeTCO += $tco;
            $cumulativeTCP += $tcp;
            $cumulativeTWGP += $twgp;
            
            $cgpa = $cumulativeTCO > 0 ? round($cumulativeTWGP / $cumulativeTCO, 2) : 0.00;

            $sessionMetrics[$session] = [
                'tco' => $tco,
                'tcp' => $tcp,
                'twgp' => $twgp,
                'gpa' => number_format($gpa, 2),
                'cgpa' => number_format($cgpa, 2),
            ];
        }

        $finalMetrics = [
            'ctco' => $cumulativeTCO,
            'ctcp' => $cumulativeTCP,
            'ctwgp' => $cumulativeTWGP,
            'cgpa' => $cumulativeTCO > 0 ? number_format($cumulativeTWGP / $cumulativeTCO, 2) : "0.00",
        ];

        return view('staff.lecturer.results.student-transcript', compact('student', 'resultsBySession', 'sessionMetrics', 'finalMetrics'));
    }

    public function printTranscript(Request $request)
    {
        $request->validate([
            'matric' => 'required|string',
        ]);

        $student = User::with('student.department.faculty')->where('username', $request->matric)->firstOrFail();

        $results = Result::where('matric_no', $request->matric)
            ->with('course')
            ->orderBy('session')
            ->orderBy('semester')
            ->get();

        $gradePoints = [
            'A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 0,
        ];

        $resultsBySession = $results->groupBy('session');
        $sessionMetrics = [];
        $cumulativeTCO = 0;
        $cumulativeTCP = 0;
        $cumulativeTWGP = 0;

        foreach ($resultsBySession as $session => $sessionResults) {
            $tco = 0; $tcp = 0; $twgp = 0;
            foreach ($sessionResults as $r) {
                $unit = (int) ($r->course_unit ?? ($r->course->course_unit ?? 0));
                $score = (float) ($r->total ?? (($r->ca ?? 0) + ($r->exam ?? 0)));
                $gp = \App\Models\GradingSystem::getPoint($score);
                $tco += $unit;
                if ($score >= 40) { $tcp += $unit; }
                $twgp += ($unit * $gp);
            }
            $gpa = $tco > 0 ? round($twgp / $tco, 2) : 0.00;
            $cumulativeTCO += $tco;
            $cumulativeTCP += $tcp;
            $cumulativeTWGP += $twgp;
            $cgpa = $cumulativeTCO > 0 ? round($cumulativeTWGP / $cumulativeTCO, 2) : 0.00;

            $sessionMetrics[$session] = [
                'tco' => $tco, 'tcp' => $tcp, 'twgp' => $twgp,
                'gpa' => number_format($gpa, 2), 'cgpa' => number_format($cgpa, 2),
            ];
        }

        $finalMetrics = [
            'ctco' => $cumulativeTCO, 'ctcp' => $cumulativeTCP, 'ctwgp' => $cumulativeTWGP,
            'cgpa' => $cumulativeTCO > 0 ? number_format($cumulativeTWGP / $cumulativeTCO, 2) : "0.00",
        ];

        return view('staff.lecturer.results.print-transcript', compact('student', 'resultsBySession', 'sessionMetrics', 'finalMetrics'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ca' => 'required|numeric|min:0',
            'exam' => 'required|numeric|min:0',
        ]);
        $result = Result::findOrFail($id);

        $result->ca = $request->ca;
        $result->exam = $request->exam;
        $result->total = $request->ca + $request->exam;
        $result->status = 'pending';
        $result->save();

        return back()->with('success', 'Result updated successfully.');
    }

    public function destroy($id)
    {
        $result = Result::findOrFail($id);
        $result->delete();

        return back()->with('success', 'Result deleted successfully.');
    }
}

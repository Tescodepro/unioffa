<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use App\Models\UserType;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsTemplateExport;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Mail;

class IctStudentController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        // 🔹 Total number of students
        $totalStudents = Student::count();

        // 🔹 Students grouped by Department (including department name and faculty)
        $studentsByDept = Department::with(['faculty'])
            ->withCount('students')
            ->orderBy('department_name')
            ->get();

        // 🔹 Students grouped by Faculty
        $studentsByFaculty = Faculty::with(['departments.students'])
            ->get()
            ->map(function ($faculty) {
                $faculty->total_students = $faculty->departments->sum(fn($dept) => $dept->students->count());
                return $faculty;
            });

        // 🔹 Optional: Students grouped by Programme
        $studentsByProgramme = Student::select('programme', DB::raw('COUNT(*) as total'))
            ->groupBy('programme')
            ->orderBy('programme')
            ->get();

        // 🔹 Pass all data to Blade
        return view('staff.ict.dashboard', compact(
            'totalStudents',
            'studentsByDept',
            'studentsByFaculty',
            'studentsByProgramme'
        ));
    }
    public function index(Request $request)
    {
        $departmentId = $request->department_id;
        $level = $request->level;
        $name = $request->name;
        $matric = $request->matric_no;
        $phone = $request->phone;
        $email = $request->email;

        $students = Student::with(['user', 'department.faculty'])
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->when($level, fn($q) => $q->where('level', $level))
            ->when(
                $name,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%")
                )
            )
            ->when($matric, fn($q) => $q->where('matric_no', 'like', "%{$matric}%"))
            ->when(
                $phone,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('phone', 'like', "%{$phone}%")
                )
            )
            ->when(
                $email,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('email', 'like', "%{$email}%")
                )
            )
            ->orderBy('matric_no')
            ->paginate(20);

        $departments = Department::with('faculty')->get();

        $stats = [
            'total' => Student::count(),
            'by_department' => Student::select('department_id', DB::raw('COUNT(*) as total'))
                ->groupBy('department_id')
                ->with('department')
                ->get(),
        ];

        return view('staff.ict.students.index', compact('students', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::with('faculty')->orderBy('department_name')->get();
        $campuses = Campus::all();
        return view('staff.ict.students.create', compact('departments', 'campuses'));
    }
    //Store single student
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|string|unique:users,phone',
            'department_id'   => 'required|exists:departments,id',
            'level'           => 'required|integer|in:100,200,300,400,500',
            'gender'          => 'required|in:male,female',
            'admission_year'  => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'campus_id'       => 'required|exists:campuses,id',
            'stream'          => 'nullable|integer',
            'entry_mode'      => 'required|string|in:TOPUP,IDELUTME,IDELDE,UTME,TRANSFER,DIPLOMA,DE',
            'dob'             => 'required|date|before:today',
        ]);

        try {
            DB::beginTransaction();

            $department = Department::findOrFail($request->department_id);

            // Extract admission year (first part before slash)
            $admissionYear = (int) explode('/', $request->admission_year)[0];

            // Generate matric number using the entry_mode directly
            $matricNo = Student::generateMatricNo(
                $department->department_code,
                $request->admission_year,
                $request->entry_mode
            );

            // Get user type
            $userType = UserType::where('name', 'student')->firstOrFail();
            // Create user
            $user = User::create([
                'first_name'       => $request->first_name,
                'last_name'        => $request->last_name,
                'middle_name'      => $request->middle_name ?? Null,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'username'         => $matricNo,
                'registration_no'  => null,
                'password'         => Hash::make($request->last_name),
                'user_type_id'     => $userType->id,
                'date_of_birth'    => $request->dob,
                'campus_id'        => $request->campus_id,
            ]);

            if (in_array($request->entry_mode, ['UTME', 'TRANSFER', 'DE'])) {
                $programme = 'REGULAR';
            } else {
                $programme = strtoupper($request->entry_mode);
            }


            Student::create([
                'user_id'            => $user->id,
                'campus_id'          => $request->campus_id,
                'department_id'      => $department->id,
                'matric_no'          => $matricNo,
                'programme'          => $programme, // Store original entry mode
                'stream'             => $request->stream,
                'entry_mode'         => $request->entry_mode,
                'level'              => $request->level,
                'admission_session'  => $request->admission_year,
                'admission_date'     => "{$admissionYear}-09-01", // Assuming Sept admission
                'status'             => 1,
                'sex'                => $request->gender, // Map gender to sex
                'address'            => null, // Optional field not in form
            ]);

            $name = $request->first_name . ' ' . $request->last_name;

            $to = $request->email;

            // Assuming you've generated these two variables earlier in your code
            $matric_number = $matricNo;
            $password = $request->last_name;
            $subject = "Welcome to Offa University! Your Student Account Details";
            $content = [
                'title' => "Welcome, {$request->first_name}!",
                'body' => 'We are delighted to welcome you to **Offa University**! 🎓
    
    <br><br>
    
    Your student account has been successfully created. Please find your essential login credentials below:
    
    <br>
    
    - **Matriculation Number:** **' . $matric_number . '**
    - **Initial Password:** **' . $password . '** <br>
    
    We strongly recommend that you log in to the student portal immediately and change your password for security purposes.
    
    <br><br>
    
    **Student Portal Link:** [Insert your portal link here]
    
    <br><br>
    
    We look forward to your success!',
                'footer' => 'Best regards,  
        Offa University Administration',
            ];
            Mail::to($to)->send(new GeneralMail($subject, $content, false));

            DB::commit();

            return redirect()
                ->route('ict.students.index')
                ->with('success', "Student added successfully. Matric No: {$matricNo} AND password is {$request->last_name}.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }
    // Bulk upload form
    public function bulkUploadForm()
    {
        $departments = Department::with('faculty')->get();
        return view('staff.ict.students.bulk', compact('departments'));
    }

    // Bulk upload process
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            $successCount = session('upload_success_count', 0);
            $skipCount = session('upload_skip_count', 0);

            if ($successCount > 0 && $skipCount == 0) {
                return redirect()
                    ->route('ict.students.index')
                    ->with('success', "Successfully uploaded {$successCount} student(s).");
            } elseif ($successCount > 0 && $skipCount > 0) {
                return redirect()
                    ->route('ict.students.index')
                    ->with('warning', "Uploaded {$successCount} student(s). {$skipCount} row(s) were skipped due to errors.");
            } else {
                return back()->with('error', 'No students were uploaded. Please check the errors and try again.');
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return back()->with('upload_errors', $errors);
        } catch (Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // Download template
    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'students_upload_template.xlsx');
    }

    public function edit($id)
    {
        $student = Student::with('user', 'department.faculty')->findOrFail($id);
        $departments = Department::with('faculty')->get();
        return view('staff.ict.students.edit', compact('student', 'departments'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'matric_no'  => 'required|string|max:255',
            'department_id' => 'required|uuid',
            'programme'  => 'required|string|max:255',
            'level'      => 'required|integer',
            'sex'        => 'required|string',
        ]);

        $student = Student::with('user')->findOrFail($id);

        // Update user details
        $student->user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'username'   => $request->matric_no,
        ]);

        // Update student details
        $student->update([
            'matric_no'     => $request->matric_no,
            'department_id' => $request->department_id,
            'programme'     => $request->programme,
            'level'         => $request->level,
            'sex'           => $request->sex,
        ]);

        return redirect()->route('ict.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Student deleted successfully.');
    }

    private function getUserTypeId(string $name): ?string
    {
        return UserType::where('name', $name)->value('id');
    }

    public function getAllUsers(Request $request)
    {
        // Get filter inputs
        $search = $request->input('search');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $username = $request->input('username');
        $userTypeId = $request->input('user_type_id');

        // Query with filters
        $users = User::with('userType')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('middle_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                });
            })
            ->when($email, fn($q) => $q->where('email', 'like', "%$email%"))
            ->when($phone, fn($q) => $q->where('phone', 'like', "%$phone%"))
            ->when($username, fn($q) => $q->where('username', 'like', "%$username%"))
            ->when($userTypeId, fn($q) => $q->where('user_type_id', $userTypeId))
            ->paginate(20);

        $userTypes = \App\Models\UserType::orderBy('name')->get();

        return view('staff.ict.users.listofusers', compact('users', 'userTypes'));
    }

    public function updateUsers(Request $request, $id)
    {
        $request->validate([
            'first_name'   => 'required|string|max:255',
            'middle_name'  => 'nullable|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $id,
            'phone'        => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'first_name'   => $request->first_name,
            'middle_name'  => $request->middle_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
        ]);

        return back()->with('success', 'User updated successfully.');
    }
}

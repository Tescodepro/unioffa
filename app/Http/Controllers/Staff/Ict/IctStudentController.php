<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Exports\StudentsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Mail\GeneralMail;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use App\Services\UniqueIdService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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
                $faculty->total_students = $faculty->departments->map(fn ($dept) => $dept->students->count())->sum();

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
        $campusId = $request->campus_id;
        $stream = $request->stream;

        $students = Student::with(['user', 'department.faculty'])
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->when($level, fn ($q) => $q->where('level', $level))
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->when($stream, fn ($q) => $q->where('stream', $stream))
            ->when(
                $name,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%")
                )
            )
            ->when($matric, fn ($q) => $q->where('matric_no', 'like', "%{$matric}%"))
            ->when(
                $phone,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('phone', 'like', "%{$phone}%")
                )
            )
            ->when(
                $email,
                fn ($q) => $q->whereHas(
                    'user',
                    fn ($u) => $u->where('email', 'like', "%{$email}%")
                )
            )
            ->orderBy('matric_no')
            ->paginate(20);

        $departments = Department::with('faculty')->get();
        $campuses = Campus::all();

        $stats = [
            'total' => Student::count(),
            'by_department' => Student::select('department_id', DB::raw('COUNT(*) as total'))
                ->groupBy('department_id')
                ->with('department')
                ->get(),
        ];

        return view('staff.ict.students.index', compact('students', 'departments', 'campuses', 'stats'));
    }

    public function create()
    {
        $departments = Department::with('faculty')->orderBy('department_name')->get();
        $campuses = Campus::all();
        $entryModes = \App\Models\EntryMode::orderBy('name')->get();

        return view('staff.ict.students.create', compact('departments', 'campuses', 'entryModes'));
    }

    // Store single student
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|in:100,200,300,400,500',
            'gender' => 'required|in:male,female',
            'admission_year' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'campus_id' => 'required|exists:campuses,id',
            'stream' => 'nullable|integer',
            'entry_mode' => 'required|string|exists:entry_modes,code',
            'dob' => 'required|date|before:today',
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
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name ?? null,
                'email' => $request->email,
                'phone' => $request->phone,
                'username' => $matricNo,
                'registration_no' => null,
                'password' => Hash::make($request->last_name),
                'user_type_id' => $userType->id,
                'date_of_birth' => $request->dob,
                'campus_id' => $request->campus_id,
            ]);

            $entryModeRecord = \App\Models\EntryMode::where('code', $request->entry_mode)->firstOrFail();
            $programme = $entryModeRecord->student_type;

            Student::create([
                'user_id' => $user->id,
                'campus_id' => $request->campus_id,
                'department_id' => $department->id,
                'matric_no' => $matricNo,
                'programme' => $programme, // Store original entry mode
                'stream' => $request->stream,
                'entry_mode' => $request->entry_mode,
                'level' => $request->level,
                'admission_session' => $request->admission_year,
                'admission_date' => "{$admissionYear}-09-01", // Assuming Sept admission
                'status' => 1,
                'sex' => $request->gender, // Map gender to sex
                'address' => null, // Optional field not in form
            ]);

            $name = $request->first_name.' '.$request->last_name;

            $to = $request->email;

            // Assuming you've generated these two variables earlier in your code
            $matric_number = $matricNo;
            $password = $request->last_name;
            $subject = 'Welcome to Offa University! Your Student Account Details';
            $content = [
                'title' => "Welcome, {$request->first_name}!",
                'body' => 'We are delighted to welcome you to **Offa University**! 🎓
    
    <br><br>
    
    Your student account has been successfully created. Please find your essential login credentials below:
    
    <br>
    
    - **Matriculation Number:** **'.$matric_number.'**
    - **Initial Password:** **'.$password.'** <br>
    
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

            return back()->with('error', 'Error creating student: '.$e->getMessage());
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
                $errors[] = "Row {$failure->row()}: ".implode(', ', $failure->errors());
            }

            return back()->with('upload_errors', $errors);
        } catch (Exception $e) {
            return back()->with('error', 'Upload failed: '.$e->getMessage());
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
        $entryModes = \App\Models\EntryMode::orderBy('name')->get();
        $campuses = \App\Models\Campus::orderBy('name')->get();

        return view('staff.ict.students.edit', compact('student', 'departments', 'entryModes', 'campuses'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'matric_no' => [
                'required',
                'string',
                'max:255',
                // must not conflict with other students
                Rule::unique('students', 'matric_no')->ignore($student->id),
                // must not conflict with users.username
                Rule::unique('users', 'username')->ignore($student->user_id),
            ],
            'department_id' => 'required|uuid',
            'programme' => 'required|string|max:255',
            'level' => 'required|integer',
            'sex' => 'required|string',
            'entry_mode' => 'required|string|exists:entry_modes,code',
            'campus_id' => 'nullable|uuid|exists:campuses,id',
        ]);

        // Update user
        $student->user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->matric_no,
        ]);

        // Update student
        $student->update([
            'matric_no' => $request->matric_no,
            'department_id' => $request->department_id,
            'programme' => $request->programme,
            'level' => $request->level,
            'sex' => $request->sex,
            'entry_mode' => $request->entry_mode,
            'campus_id' => $request->campus_id ?: null,
        ]);

        return redirect()->route('ict.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $uniqueIdService = new UniqueIdService;
        $uniqueId = $uniqueIdService->generate('applicant');
        $student->delete();

        $userType = UserType::where('name', 'applicant')->firstOrFail();
        // update users->username
        $user = $student->user;
        $user->username = $uniqueId;
        $user->registration_no = $uniqueId;
        $user->user_type_id = $userType->id;
        $user->save();

        return back()->with('success', 'Student deleted successfully.');
    }

    private function getUserTypeId(string $name): ?string
    {
        return UserType::where('name', $name)->value('id');
    }

    public function getAllUsers(Request $request)
    {
        // Only keep the two lightweight server-side pre-filters.
        // All text searching is handled client-side by DataTables.
        $userTypeId = $request->input('user_type_id');
        $campusId = $request->input('campus_id');

        $users = User::withTrashed()->with(['userType', 'campus'])
            ->when($userTypeId, fn ($q) => $q->where('user_type_id', $userTypeId))
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->orderBy('first_name')
            ->get();

        $userTypes = UserType::orderBy('name')->get();
        $campuses = Campus::orderBy('name')->get();

        return view('staff.ict.users.listofusers', compact('users', 'userTypes', 'campuses'));
    }

    public function updateUsers(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'user_type_id' => 'required|exists:user_types,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'application_types' => 'nullable|array',
            'application_types.*' => 'exists:application_settings,id',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type_id' => $request->user_type_id,
            'campus_id' => $request->campus_id ?: null,
        ]);

        $userType = UserType::find($request->user_type_id);
        if ($userType && $userType->name === 'programme-director') {
            $user->assignedApplicationTypes()->sync($request->application_types ?? []);
        } else {
            // If they are no longer a programme director, clear their assigned types
            $user->assignedApplicationTypes()->sync([]);
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function storeUsers(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'user_type_id' => 'required|exists:user_types,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'application_types' => 'nullable|array',
            'application_types.*' => 'exists:application_settings,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type_id' => $request->user_type_id,
            'campus_id' => $request->campus_id ?: null,
            'password' => bcrypt('password123'),
        ]);

        $userType = UserType::find($request->user_type_id);
        if ($userType && $userType->name === 'programme-director' && $request->has('application_types')) {
            $user->assignedApplicationTypes()->sync($request->application_types);
        }

        return back()->with('success', 'User added successfully.');
    }

    public function disableUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Soft delete

        return back()->with('success', 'User disabled successfully.');
    }

    public function enableUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', 'User enabled successfully.');
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return back()->with('success', 'User permanently deleted.');
    }
}

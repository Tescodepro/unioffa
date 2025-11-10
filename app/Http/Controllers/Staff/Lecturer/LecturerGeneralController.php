<?php

namespace App\Http\Controllers\Staff\Lecturer;

use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LecturerGeneralController extends Controller
{
    public function dean_dashboard()
    {
        // 1️⃣ Get the logged-in staff
        $staff = auth()->user()->staff;

        if (! $staff) {
            abort(403, 'Staff record not found for this user.');
        }

        // 2️⃣ Get faculty and department info
        $faculty = Faculty::find($staff->faculty_id);

        // 3️⃣ Fetch departments under this faculty with total students
        $departments = Department::where('faculty_id', $staff->faculty_id)
            ->withCount('students')
            ->get();

        // 4️⃣ Get user info
        $user = auth()->user();

        return view('staff.lecturer.dean_dashboard', compact('staff', 'user', 'faculty', 'departments'));
    }

    public function lecturer_dashboard()
    {
        // 1️⃣ Get the logged-in staff
        $staff = auth()->user()->staff;

        if (! $staff) {
            abort(403, 'Staff record not found for this user.');
        }

        // 2️⃣ Get department and faculty info
        $department = Department::find($staff->department_id);
        $faculty = Faculty::find($staff->faculty_id);

        // 3️⃣ Get courses assigned to this lecturer
        $courses = auth()->user()->courses()
            ->orderBy('course_code')
            ->get();

        // 4️⃣ Get total number of students in the lecturer's department grouped by level
        $studentsByLevel = \App\Models\Student::where('department_id', $staff->department_id)
            ->select('level', \DB::raw('count(*) as student_count'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        // 5️⃣ Get total students in department
        $totalStudents = \App\Models\Student::where('department_id', $staff->department_id)->count();

        // 6️⃣ Get total assigned courses
        $totalCourses = $courses->count();

        // 7️⃣ Get user info
        $user = auth()->user();

        return view('staff.lecturer.lecturer_dashboard', compact(
            'staff',
            'user',
            'department',
            'faculty',
            'courses',
            'studentsByLevel',
            'totalStudents',
            'totalCourses'
        ));
    }

    public function department_students($departmentId)
    {
        $department = Department::with('students')->findOrFail($departmentId);

        return view('staff.lecturer.department_students', compact('department'));
    }

    // ============================== STAFF PROFILE ================================== //
    public function listStaff()
    {
        // Fetch all staff with their associated user, faculty, and department
        $staffs = Staff::with(['user', 'faculty', 'department'])->get();

        return view('staff.lecturer.staff_list', compact('staffs'));
    }

    public function addStaff(Request $request)
    {
        // Validate input
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive',
            'user_type_id' => 'required|exists:user_types,id',
            'username' => 'required|unique:users,username',
        ]);

        // Check if a staff already exists with the same email or phone number
        $existingUser = User::where('email', $request->email)->orWhere('phone', $request->phone)->first();
        if ($existingUser) {
            return redirect()->back()->withErrors(['error' => 'A staff member with this email or phone number already exists.']);
        }

        // Check if a staff already exists with the same email or phone number
        $existingStaff = Staff::whereHas('user', function ($query) use ($request) {
            $query->where('email', $request->email)->orWhere('phone', $request->phone);
        })->first();
        if ($existingStaff) {
            return redirect()->back()->withErrors(['error' => 'A staff member with this email or phone number already exists.']);
        }

        try {
            // ✅ Start database transaction
            DB::beginTransaction();

            $staff_no = 'STAFF'.rand(1000, 9999);
            $defaultPassword = 'password123'; // Store in variable for email

            // Create User
            $user = User::create([
                'id' => Str::uuid(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => strtolower($request->username),
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($defaultPassword),
                'user_type_id' => $request->user_type_id,
            ]);

            // Create Staff
            $staff = Staff::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'staff_no' => $staff_no,
                'status' => $request->status,
            ]);

            // Get faculty and department names
            $faculty = Faculty::find($request->faculty_id);
            $department = Department::find($request->department_id);
            $userType = UserType::find($request->user_type_id);

            // ✅ Commit transaction - all database operations successful
            DB::commit();

            // ✅ Send welcome email to the new staff member (after commit)
            try {
                $to = $user->email;
                $subject = 'Welcome to Offa University - Staff Account Created';

                $content = [
                    'title' => 'Hello '.$user->first_name.' '.$user->last_name.',',
                    'body' => 'Your staff account has been successfully created at Offa University.<br><br>

            <strong>Your Account Details:</strong><br>  
            - Staff No: <strong>'.$staff_no.'</strong><br>  
            - Username: <strong>'.$user->username.'</strong><br>  
            - Email: <strong>'.$user->email.'</strong><br>  
            - Temporary Password: <strong>'.$defaultPassword.'</strong><br>  
            - Role: <strong>'.ucfirst($userType->name ?? 'Staff').'</strong><br>  
            - Faculty: <strong>'.($faculty->faculty_name ?? 'N/A').'</strong><br>  
            - Department: <strong>'.($department->department_name ?? 'N/A').'</strong><br><br>

            <strong>Important:</strong> Please login and change your password immediately for security reasons.<br><br>

            Login URL: <a href="'.route('staff.login').'">'.route('staff.login').'</a>',
                    'footer' => 'Welcome to the team!<br>Offa University Administration',
                ];

                Mail::to($to)->send(new GeneralMail($subject, $content, false));
            } catch (\Exception $e) {
                // Log the error but don't fail the staff creation
                \Log::error('Failed to send welcome email to staff: '.$e->getMessage());
            }

            return redirect()->back()->with('success', 'Staff added successfully. Login credentials have been sent to their email.');

        } catch (\Exception $e) {
            // ✅ Rollback transaction on any error
            DB::rollBack();

            // Log the error
            \Log::error('Failed to create staff: '.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Failed to create staff account. Please try again.'])->withInput();
        }
    }

    public function updateStaff(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $user = $staff->user;

        // Validate input
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required|unique:users,phone,'.$user->id,
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Update user info
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update staff info
        $staff->update([
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Staff updated successfully.');
    }

    public function destroyStaff($id)
    {
        $staff = Staff::findOrFail($id);

        // Force delete associated user
        if ($staff->user) {
            $staff->user->forceDelete();
        }

        // Delete staff record itself
        $staff->delete();

        return redirect()->back()->with('success', 'Staff and associated user deleted permanently.');
    }

    public function courseAssignments()
    {
        $assignments = DB::table('course_user')
            ->join('courses', 'course_user.course_id', '=', 'courses.id')
            ->join('users', 'course_user.user_id', '=', 'users.id')
            ->select(
                'course_user.id',
                'courses.course_code',
                'courses.course_title',
                'users.first_name',
                'users.last_name'
            )
            ->get();

        $courses = Course::orderBy('course_code')->get();
        $lecturers = User::whereHas('userType', function ($q) {
            $q->whereIn('name', ['Lecturer', 'HOD', 'Dean']);
        })->orderBy('first_name')->get();

        return view('staff.lecturer.course_assignment', compact('assignments', 'courses', 'lecturers'));
    }

    public function assignCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|uuid',
            'user_id' => 'required|uuid',
        ]);

        // Check if this assignment already exists
        $existing = DB::table('course_user')
            ->where('course_id', $request->course_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (! $existing) {
            DB::table('course_user')->insert([
                'id' => Str::uuid(), // ✅ Fix for the missing UUID
                'course_id' => $request->course_id,
                'user_id' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Optional: Update timestamp if already exists
            DB::table('course_user')
                ->where('id', $existing->id)
                ->update(['updated_at' => now()]);
        }

        return back()->with('success', 'Course successfully assigned to lecturer.');
    }

    public function deleteAssignment($id)
    {
        DB::table('course_user')->where('id', $id)->delete();

        return back()->with('success', 'Assignment deleted successfully.');
    }
}

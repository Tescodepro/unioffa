<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PaymentSetting;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Setup UserType
    $this->studentType = UserType::updateOrCreate(
        ['name' => 'student'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'students.dashboard']
    );

    // 2. Setup Campus
    $this->campus = Campus::create([
        'name' => 'Main Campus',
        'slug' => 'main-campus',
        'address' => 'Offa, Kwara State',
        'phone_number' => '08000000000',
        'email' => 'info@unioffa.edu.ng',
        'direction' => 'Near the bridge',
    ]);

    // 3. Setup Faculty and Department
    $this->faculty = Faculty::create([
        'faculty_name' => 'Faculty of Science',
        'faculty_code' => 'FSC',
        'description' => 'Science Faculty',
    ]);

    $this->department = Department::create([
        'faculty_id' => $this->faculty->id,
        'department_name' => 'Computer Science',
        'department_code' => 'CSC',
        'qualification' => 'B.Sc.',
        'department_description' => 'CS Department',
    ]);

    // 4. Setup User and Student
    $this->user = User::factory()->create([
        'user_type_id' => $this->studentType->id,
        'campus_id' => $this->campus->id,
    ]);

    $this->student = Student::create([
        'user_id' => $this->user->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => '23/FSC/CSC/014',
        'level' => '300',
        'programme' => 'REGULAR',
        'entry_mode' => 'UTME',
        'sex' => 'female',
        'admission_session' => '2025/2026',
    ]);

    // 5. Setup Academic Session and Semester
    $this->session = AcademicSession::create([
        'name' => '2025/2026',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
    ]);

    // Make the semester a specific override matching REGULAR
    $this->semester = AcademicSemester::create([
        'name' => 'Second Semester',
        'code' => '2nd',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
        'programme' => ['REGULAR'],
    ]);
});

it('clears registration for REGULAR students when session fees are paid, ignoring semester overrides', function () {
    // 6. Setup Payment Settings
    // fee 1: session-wide tuition
    PaymentSetting::create([
        'faculty_id' => $this->faculty->id,
        'department_id' => $this->department->id,
        'level' => [300],
        'payment_type' => 'tuition',
        'amount' => 100000,
        'session' => '2025/2026',
        'semester' => null, // Session-wide
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
    ]);

    // fee 2: semester-specific exam fee (SHOULD BE IGNORED for REGULAR)
    PaymentSetting::create([
        'faculty_id' => $this->faculty->id,
        'department_id' => $this->department->id,
        'level' => [300],
        'payment_type' => 'exam_fee',
        'amount' => 5000,
        'session' => '2025/2026',
        'semester' => '2nd',
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
    ]);

    // 7. Pay ONLY the session tuition
    Transaction::create([
        'user_id' => $this->user->id,
        'payment_type' => 'tuition',
        'amount' => 100000,
        'description' => 'Tuition Payment',
        'payment_method' => 'paystack',
        'session' => '2025/2026',
        'semester' => 'Second Semester',
        'payment_status' => 1,
        'refernce_number' => 'REF-'.Str::random(10),
    ]);

    // 8. Access course registration
    actingAs($this->user)
        ->get(route('students.course.registration'))
        ->assertSuccessful()
        ->assertViewHas('payment_status', function ($status) {
            // allCleared should be true because the semester-specific exam_fee is ignored
            return $status['allCleared'] === true;
        });
});

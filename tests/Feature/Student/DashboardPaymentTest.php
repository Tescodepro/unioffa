<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PaymentSetting;
use App\Models\Student;
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
        'admission_session' => '2023/2024',
    ]);

    // 5. Setup Academic Session and Semester
    $this->session = AcademicSession::create([
        'name' => '2025/2026',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
    ]);

    // Make the semester a "specific override" that matches the student
    $this->semester = AcademicSemester::create([
        'name' => 'Second Semester',
        'code' => '2nd',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
        'programme' => ['REGULAR'], // This makes it a specific override
    ]);
});

it('can load payment page with installment settings without json_decode error', function () {
    // 6. Setup Payment Setting with installments
    PaymentSetting::create([
        'faculty_id' => $this->faculty->id,
        'department_id' => $this->department->id,
        'level' => [300],
        'payment_type' => 'tuition',
        'amount' => 100000,
        'session' => '2025/2026',
        'semester' => '2nd',
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
        'installmental_allow_status' => 1,
        'number_of_instalment' => 2,
        'list_instalment_percentage' => [60, 40], // Eloquent will cast this to JSON string in DB
    ]);

    // 7. Test the route
    actingAs($this->user)
        ->get(route('students.load_payment'))
        ->assertSuccessful()
        ->assertViewIs('student.payment')
        ->assertViewHas('paymentSettings');
});

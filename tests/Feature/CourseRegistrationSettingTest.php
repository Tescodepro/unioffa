<?php

use App\Models\User;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\AcademicSemester;
use App\Models\UserType;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Campus;
use App\Models\SystemSetting;
use App\Models\CourseRegistrationSetting;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->userType = UserType::create(['name' => 'student']);
    $this->campus = Campus::create([
        'name' => 'Main Campus', 
        'slug' => 'main-campus',
        'address' => 'Test Address',
        'phone_number' => '08000000000',
        'email' => 'campus@example.com',
        'direction' => 'Test Direction'
    ]);
    $this->faculty = Faculty::create([
        'faculty_name' => 'Test Faculty', 
        'faculty_code' => 'TF',
        'description' => 'Test Description'
    ]);
    $this->department = Department::create([
        'department_name' => 'Test Department',
        'department_code' => 'TD',
        'faculty_id' => $this->faculty->id,
        'department_description' => 'Test Description',
        'qualification' => 'B.Sc.'
    ]);

    $this->user = User::factory()->create([
        'user_type_id' => $this->userType->id
    ]);

    $this->student = Student::create([
        'user_id' => $this->user->id,
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'level' => '100',
        'matric_no' => '2024001',
        'programme' => 'B.Sc.',
        'entry_mode' => 'UTME',
        'admission_session' => '2024/2025'
    ]);

    $this->session = AcademicSession::create([
        'name' => '2024/2025',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => []
    ]);

    $this->semester = AcademicSemester::create([
        'name' => '1st',
        'code' => '1st',
        'status' => 1,
        'status_upload_result' => 0,
        'academic_session_id' => $this->session->id,
        'lecturar_ids' => [],
        'students_ids' => []
    ]);

    SystemSetting::create(['key' => 'max_units_per_semester', 'value' => '24']);
    SystemSetting::create(['key' => 'max_units_per_session', 'value' => '48']);

    \App\Models\PaymentSetting::create([
        'payment_type' => 'tuition',
        'amount' => 100000,
        'session' => '2024/2025',
        'level' => [100],
        'installmental_allow_status' => 0
    ]);
});

test('it blocks course registration if closing date is passed and shows late fee payment form', function () {
    // Set deadline in the past
    CourseRegistrationSetting::create([
        'campus_id' => $this->campus->id,
        'entry_mode' => ['UTME'],
        'session' => '2024/2025',
        'semester' => '1st',
        'closing_date' => now()->subDays(2),
        'late_registration_fee' => 5000,
    ]);

    // Make tuition payment so we don't hit the generic payment block
    Transaction::create([
        'user_id' => $this->user->id,
        'amount' => 100000,
        'payment_type' => 'tuition',
        'session' => '2024/2025',
        'payment_status' => 1,
        'description' => 'Tuition Payment',
        'refernce_number' => 'REF123',
        'payment_method' => 'manual'
    ]);

    $response = actingAs($this->user)->get(route('students.course.registration'));

    $response->assertStatus(200);
    $response->assertSee('Registration Period Closed');
    $response->assertSee('Pay Late Registration Fee');
    $response->assertSee('5,000.00');
});

test('it allows course registration if late fee is paid', function () {
    CourseRegistrationSetting::create([
        'campus_id' => $this->campus->id,
        'entry_mode' => ['UTME'],
        'session' => '2024/2025',
        'semester' => '1st',
        'closing_date' => now()->subDays(2),
        'late_registration_fee' => 5000,
    ]);

    // Make tuition payment
    Transaction::create([
        'user_id' => $this->user->id,
        'amount' => 100000,
        'payment_type' => 'tuition',
        'session' => '2024/2025',
        'payment_status' => 1,
        'description' => 'Tuition Payment',
        'refernce_number' => 'REF123',
        'payment_method' => 'manual'
    ]);

    // Make late fee payment
    Transaction::create([
        'user_id' => $this->user->id,
        'amount' => 5000,
        'payment_type' => 'late_course_registration',
        'session' => '2024/2025',
        'semester' => '1st',
        'payment_status' => 1,
        'description' => 'Late Registration Fee',
        'refernce_number' => 'REF124',
        'payment_method' => 'paystack'
    ]);

    $response = actingAs($this->user)->get(route('students.course.registration'));

    $response->assertStatus(200);
    $response->assertDontSee('Registration Period Closed');
    // Ensure the courses table is rendered
    $response->assertViewHas('courses');
    $response->assertSee('Available Courses');
});

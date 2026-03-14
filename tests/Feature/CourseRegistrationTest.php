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

test('it can access course registration page when payments are not cleared without undefined variable error', function () {
    // Navigate to course registration
    $response = actingAs($this->user)->get(route('students.course.registration'));

    $response->assertStatus(200);
    $response->assertViewIs('student.course-registration');
    $response->assertViewHas('maxSemesterUnits');
    $response->assertViewHas('currentSemesterUnits');
    $response->assertSee('Payment Status: Pending');
    $response->assertSee('Please clear your dues to enable course registration.');
});

test('it can access course registration page and see filters for partial tuition payment', function () {
    // Mock partial payment in a way the controller understands
    // Since we are using RefreshDatabase, we can create a PaymentSetting and a Transaction
    // or we can just mock the behavior if needed. 
    // But let's rely on the real service logic.
    
    // Create a tuition payment setting
    \App\Models\PaymentSetting::create([
        'payment_type' => 'tuition',
        'amount' => 100000,
        'session' => '2024/2025',
        'level' => [100],
        'installmental_allow_status' => 1,
        'number_of_instalment' => 2
    ]);

    // Create a transaction for 60% (60,000)
    \App\Models\Transaction::create([
        'user_id' => $this->user->id,
        'amount' => 60000,
        'payment_type' => 'tuition',
        'session' => '2024/2025',
        'payment_status' => 1,
        'description' => 'Tuition Payment',
        'refernce_number' => 'REF123',
        'payment_method' => 'manual'
    ]);

    $response = actingAs($this->user)->get(route('students.course.registration'));

    $response->assertStatus(200);
    // Should NOT see the hard error message if condition in view is met
    // Note: The controller still sets the 'error' flash message, but the view condition displays the filters
    $response->assertViewHas('courses');
    $response->assertSee('Department'); // From partials.filter-form
});

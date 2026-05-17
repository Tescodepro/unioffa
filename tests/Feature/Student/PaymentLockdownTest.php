<?php

use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\PaymentLockdownSetting;
use App\Models\PaymentSetting;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use Carbon\Carbon;
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
    $this->adminType = UserType::updateOrCreate(
        ['name' => 'superadmin'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'admin.dashboard']
    );

    // 2. Setup Campus (Center)
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

    $this->semester = AcademicSemester::create([
        'name' => 'Second Semester',
        'code' => '2nd',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
        'programme' => ['REGULAR'],
    ]);

    // 6. Setup Payment Setting
    PaymentSetting::create([
        'faculty_id' => $this->faculty->id,
        'department_id' => $this->department->id,
        'level' => [300],
        'payment_type' => 'tuition',
        'amount' => 100000,
        'session' => '2025/2026',
        'semester' => null,
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
        'installmental_allow_status' => 0,
    ]);
});

it('applies lockdown to student matching criteria when deadline passed', function () {
    // Create lockdown that expired
    $lockdown = PaymentLockdownSetting::create([
        'title' => 'Expired Lockdown',
        'deadline' => Carbon::now()->subHour(),
        'departments' => [$this->department->id],
        'levels' => ['300'],
        'admission_sessions' => ['2023/2024'],
        'payment_type' => 'tuition',
    ]);

    // Acting as student, access payment page
    $response = actingAs($this->user)->get(route('students.load_payment'));

    $response->assertSuccessful();
    $response->assertViewHas('activeLockdown', function ($retrieved) use ($lockdown) {
        return $retrieved->id === $lockdown->id;
    });

    // Try to initiate payment and verify security block is triggered
    $postResponse = actingAs($this->user)->post(route('application.payment.process'), [
        'fee_type' => 'tuition',
        'amount' => 10000,
    ]);

    $postResponse->assertRedirect();
    $postResponse->assertSessionHas('error', function ($errorMsg) {
        return str_contains($errorMsg, 'Payment portal is locked') && str_contains($errorMsg, 'Expired Lockdown');
    });
});

it('does not block payment if lockdown deadline is in the future', function () {
    // Create lockdown in the future
    $lockdown = PaymentLockdownSetting::create([
        'title' => 'Future Lockdown',
        'deadline' => Carbon::now()->addDay(),
        'departments' => [$this->department->id],
        'levels' => ['300'],
        'admission_sessions' => ['2023/2024'],
        'payment_type' => 'tuition',
    ]);

    // Acting as student, access payment page
    $response = actingAs($this->user)->get(route('students.load_payment'));

    $response->assertSuccessful();
    $response->assertViewHas('activeLockdown', function ($retrieved) use ($lockdown) {
        return $retrieved->id === $lockdown->id;
    });

    // Try to initiate payment and verify security block does NOT trigger lockdown error
    // It might still fail on other checks (e.g. actual payment settings or late penalty checks),
    // but it should NOT return "Payment portal is locked"
    $postResponse = actingAs($this->user)->post(route('application.payment.process'), [
        'fee_type' => 'tuition',
        'amount' => 10000,
    ]);

    // If it redirects back due to other conditions, make sure it is not the lockdown error
    if (session('error')) {
        expect(session('error'))->not->toContain('Payment portal is locked');
    }
});

it('does not apply lockdown if student does not match the criteria', function () {
    // Create lockdown for 400 level students
    $lockdown = PaymentLockdownSetting::create([
        'title' => '400 Level Lockdown',
        'deadline' => Carbon::now()->subHour(),
        'departments' => [$this->department->id],
        'levels' => ['400'], // student is 300
        'admission_sessions' => ['2023/2024'],
        'payment_type' => 'tuition',
    ]);

    // Acting as student, access payment page
    $response = actingAs($this->user)->get(route('students.load_payment'));

    $response->assertSuccessful();
    $response->assertViewHas('activeLockdown', null);
});

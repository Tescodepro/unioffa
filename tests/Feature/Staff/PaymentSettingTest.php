<?php

use App\Models\PaymentSetting;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create staff user type if not exists
    $this->userType = UserType::updateOrCreate(
        ['name' => 'bursary'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'burser.dashboard']
    );

    // Create staff user
    $this->user = User::factory()->create([
        'user_type_id' => $this->userType->id,
    ]);
});

it('can store payment setting without installments and defaults number_of_instalment to 1', function () {
    $data = [
        'faculty_id' => null,
        'department_id' => null,
        'level' => ['100', '200'],
        'payment_type' => 'tuition',
        'amount' => 50000.00,
        'session' => '2025/2026',
        'semester' => 'first',
        'student_type' => ['IDELDE'],
        'entry_mode' => ['IDELDE'],
        'installmental_allow_status' => 0,
        // 'number_of_instalment' is omitted
    ];

    actingAs($this->user)
        ->post(route('bursary.payment-settings.store'), $data)
        ->assertRedirect(route('bursary.payment-settings.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('payment_settings', [
        'payment_type' => 'tuition',
        'amount' => 50000.00,
        'installmental_allow_status' => 0,
        'number_of_instalment' => 1,
    ]);
});

it('requires number_of_instalment when installments are allowed', function () {
    $data = [
        'payment_type' => 'tuition',
        'amount' => 50000.00,
        'session' => '2025/2026',
        'installmental_allow_status' => 1,
        // omitted number_of_instalment and list_instalment_percentage
    ];

    actingAs($this->user)
        ->post(route('bursary.payment-settings.store'), $data)
        ->assertSessionHasErrors(['number_of_instalment', 'list_instalment_percentage']);
});

it('can store payment setting with installments', function () {
    $data = [
        'payment_type' => 'tuition',
        'amount' => 50000.00,
        'session' => '2025/2026',
        'installmental_allow_status' => 1,
        'number_of_instalment' => 2,
        'list_instalment_percentage' => [60, 40],
    ];

    actingAs($this->user)
        ->post(route('bursary.payment-settings.store'), $data)
        ->assertRedirect(route('bursary.payment-settings.index'))
        ->assertSessionHas('success');

    $setting = PaymentSetting::where('payment_type', 'tuition')->first();
    expect($setting->number_of_instalment)->toBe(2);
    expect($setting->list_instalment_percentage)->toBe([60, 40]);
});

it('can store payment setting with admission_session', function () {
    $data = [
        'payment_type' => 'acceptance',
        'amount' => 30000.00,
        'session' => '2025/2026',
        'installmental_allow_status' => 0,
        'admission_session' => ['2024/2025', '2025/2026'],
    ];

    actingAs($this->user)
        ->post(route('bursary.payment-settings.store'), $data)
        ->assertRedirect(route('bursary.payment-settings.index'))
        ->assertSessionHas('success');

    $setting = PaymentSetting::where('payment_type', 'acceptance')->first();
    expect($setting->admission_session)->toBe(['2024/2025', '2025/2026']);
});

it('applies payment setting to student only if admission_session matches', function () {
    // Create an academic session
    $session = \App\Models\AcademicSession::create([
        'name' => '2025/2026',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
    ]);

    // Create Campus
    $campus = \App\Models\Campus::create([
        'name' => 'Main Campus',
        'slug' => 'main-campus',
        'address' => 'Offa, Kwara State',
        'phone_number' => '08000000000',
        'email' => 'info@unioffa.edu.ng',
        'direction' => 'Near the bridge',
    ]);

    // Create Faculty & Department
    $faculty = \App\Models\Faculty::create([
        'faculty_name' => 'Science',
        'faculty_code' => 'FSC',
        'description' => 'Science Faculty',
    ]);

    $department = \App\Models\Department::create([
        'faculty_id' => $faculty->id,
        'department_name' => 'Computer Science',
        'department_code' => 'CSC',
    ]);

    // Create student user type
    $studentUserType = \App\Models\UserType::updateOrCreate(
        ['name' => 'student'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'student.dashboard']
    );

    // Create student user
    $studentUser = \App\Models\User::factory()->create([
        'user_type_id' => $studentUserType->id,
    ]);

    // Create a student admitted in 2024/2025
    $student = \App\Models\Student::create([
        'user_id' => $studentUser->id,
        'status' => 'active',
        'campus_id' => $campus->id,
        'department_id' => $department->id,
        'matric_no' => '24/FSC/CSC/001',
        'level' => '100',
        'programme' => 'REGULAR',
        'entry_mode' => 'UTME',
        'sex' => 'female',
        'admission_session' => '2024/2025',
    ]);

    // Create a fee matching 2024/2025 admission session
    $matchingFee = PaymentSetting::create([
        'payment_type' => 'tuition',
        'amount' => 50000.00,
        'session' => '2025/2026',
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
        'admission_session' => ['2024/2025'],
    ]);

    // Create a fee for 2025/2026 admission session (should be ignored by student)
    $ignoredFee = PaymentSetting::create([
        'payment_type' => 'acceptance',
        'amount' => 20000.00,
        'session' => '2025/2026',
        'student_type' => ['REGULAR'],
        'entry_mode' => ['UTME'],
        'admission_session' => ['2025/2026'],
    ]);

    // Get fees for student
    $fees = PaymentSetting::getFeesForStudent($student, '2025/2026');

    expect($fees->pluck('id')->toArray())->toContain($matchingFee->id);
    expect($fees->pluck('id')->toArray())->not->toContain($ignoredFee->id);
});

<?php

use App\Models\Campus;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use App\Services\MatricNumberGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create student user type
    $this->studentUserType = UserType::updateOrCreate(
        ['name' => 'student'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'student.dashboard']
    );

    // Create Campus
    $this->campus = Campus::create([
        'name' => 'Main Campus',
        'slug' => 'main-campus',
        'address' => 'Offa, Kwara State',
        'phone_number' => '08000000000',
        'email' => 'info@unioffa.edu.ng',
        'direction' => 'Near the bridge',
    ]);

    // Create Faculty & Department
    $this->faculty = Faculty::create([
        'faculty_name' => 'Faculty of Management and Social Sciences',
        'faculty_code' => 'FMSS',
        'description' => 'FMSS description',
    ]);

    $this->department = Department::create([
        'faculty_id' => $this->faculty->id,
        'department_name' => 'Mass Communication',
        'department_code' => 'MAC',
    ]);

    // Create Entry Mode
    $this->entryMode = EntryMode::create([
        'name' => 'Direct Entry',
        'code' => 'DE',
        'student_type' => 'REGULAR',
        'matric_prefix' => 'DE',
        'default_level' => '200',
    ]);
});

it('generates a matric number and updates the username', function () {
    $user = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
        'username' => 'UOO/APP/2025/00001',
    ]);

    $student = Student::create([
        'user_id' => $user->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => 'UOO/APP/2025/00001',
        'level' => '200',
        'programme' => 'REGULAR',
        'entry_mode' => 'DE',
        'sex' => 'female',
        'admission_session' => '2025/2026',
    ]);

    $service = new MatricNumberGenerationService;
    $result = $service->generateIfNeeded($student);

    expect($result)->toBeTrue();

    $student->refresh();
    $user->refresh();

    // Expected format: 25/FMSS/DEMAC/001
    expect($student->matric_no)->toBe('25/FMSS/DEMAC/001');
    expect($user->username)->toBe('25/FMSS/DEMAC/001');
});

it('increments sequence correctly when previous matric numbers exist', function () {
    // Create first student and generate their matric number
    $user1 = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
        'username' => 'UOO/APP/2025/00001',
    ]);
    $student1 = Student::create([
        'user_id' => $user1->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => 'UOO/APP/2025/00001',
        'level' => '200',
        'programme' => 'REGULAR',
        'entry_mode' => 'DE',
        'sex' => 'female',
        'admission_session' => '2025/2026',
    ]);

    // Pre-create some existing matriculated students
    $userOld = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
        'username' => '25/FMSS/DEMAC/001',
    ]);
    Student::create([
        'user_id' => $userOld->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => '25/FMSS/DEMAC/001',
        'level' => '200',
        'programme' => 'REGULAR',
        'entry_mode' => 'DE',
        'sex' => 'male',
        'admission_session' => '2025/2026',
    ]);

    $service = new MatricNumberGenerationService;
    $result = $service->generateIfNeeded($student1);

    expect($result)->toBeTrue();
    $student1->refresh();

    // Should be sequence 002 since 001 is already taken by the pre-created student
    expect($student1->matric_no)->toBe('25/FMSS/DEMAC/002');
});

it('skips generation if student already has a valid matric number', function () {
    $user = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
        'username' => '25/FMSS/DEMAC/001',
    ]);

    $student = Student::create([
        'user_id' => $user->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => '25/FMSS/DEMAC/001',
        'level' => '200',
        'programme' => 'REGULAR',
        'entry_mode' => 'DE',
        'sex' => 'female',
        'admission_session' => '2025/2026',
    ]);

    $service = new MatricNumberGenerationService;
    $result = $service->generateIfNeeded($student);

    expect($result)->toBeFalse();
});

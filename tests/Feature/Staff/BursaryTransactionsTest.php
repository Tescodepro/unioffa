<?php

use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create staff user type
    $this->userType = UserType::updateOrCreate(
        ['name' => 'bursary'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'burser.dashboard']
    );

    // Create staff user
    $this->user = User::factory()->create([
        'user_type_id' => $this->userType->id,
    ]);

    // Create student user type
    $this->studentUserType = UserType::updateOrCreate(
        ['name' => 'student'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'student.dashboard']
    );

    // Create Academic Session
    $this->session = AcademicSession::create([
        'name' => '2026/2027',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
    ]);

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
        'faculty_name' => 'Science',
        'faculty_code' => 'FSC',
        'description' => 'Science Faculty',
    ]);

    $this->department = Department::create([
        'faculty_id' => $this->faculty->id,
        'department_name' => 'Computer Science',
        'department_code' => 'CSC',
    ]);

    // Create Entry Modes
    $this->utmeMode = EntryMode::create([
        'name' => 'Unified Tertiary Matriculation Examination',
        'code' => 'UTME',
        'student_type' => 'REGULAR',
        'matric_prefix' => 'UOO/UTME',
        'default_level' => '100',
    ]);

    $this->deMode = EntryMode::create([
        'name' => 'Direct Entry',
        'code' => 'DE',
        'student_type' => 'REGULAR',
        'matric_prefix' => 'UOO/DE',
        'default_level' => '200',
    ]);

    // Student A (UTME)
    $this->studentUserA = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
    ]);
    $this->studentA = Student::create([
        'user_id' => $this->studentUserA->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => 'UOO/UTME/2026/001',
        'level' => '100',
        'programme' => 'REGULAR',
        'entry_mode' => 'UTME',
        'sex' => 'male',
        'admission_session' => '2026/2027',
    ]);

    // Student B (DE)
    $this->studentUserB = User::factory()->create([
        'user_type_id' => $this->studentUserType->id,
    ]);
    $this->studentB = Student::create([
        'user_id' => $this->studentUserB->id,
        'status' => 'active',
        'campus_id' => $this->campus->id,
        'department_id' => $this->department->id,
        'matric_no' => 'UOO/DE/2026/001',
        'level' => '200',
        'programme' => 'REGULAR',
        'entry_mode' => 'DE',
        'sex' => 'female',
        'admission_session' => '2026/2027',
    ]);

    // Create transactions
    $this->transactionA = Transaction::create([
        'user_id' => $this->studentUserA->id,
        'amount' => 50000.00,
        'refernce_number' => 'TXN-UTME-123',
        'payment_status' => 1,
        'payment_type' => 'tuition',
        'session' => '2026/2027',
        'description' => 'Tuition payment UTME',
        'payment_method' => 'paystack',
    ]);

    $this->transactionB = Transaction::create([
        'user_id' => $this->studentUserB->id,
        'amount' => 60000.00,
        'refernce_number' => 'TXN-DE-456',
        'payment_status' => 1,
        'payment_type' => 'tuition',
        'session' => '2026/2027',
        'description' => 'Tuition payment DE',
        'payment_method' => 'paystack',
    ]);
});

it('can access transactions page and filter by entry_mode', function () {
    // Check without filter (both transactions present)
    actingAs($this->user)
        ->get(route('bursary.transactions'))
        ->assertOk()
        ->assertSee('TXN-UTME-123')
        ->assertSee('TXN-DE-456');

    // Filter by UTME
    actingAs($this->user)
        ->get(route('bursary.transactions', ['entry_mode' => 'UTME']))
        ->assertOk()
        ->assertSee('TXN-UTME-123')
        ->assertDontSee('TXN-DE-456');

    // Filter by DE
    actingAs($this->user)
        ->get(route('bursary.transactions', ['entry_mode' => 'DE']))
        ->assertOk()
        ->assertSee('TXN-DE-456')
        ->assertDontSee('TXN-UTME-123');
});

it('exports transactions to excel respecting entry_mode filter', function () {
    $response = actingAs($this->user)
        ->get(route('bursary.transactions.export', [
            'format' => 'excel',
            'entry_mode' => 'UTME',
        ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Disposition');
    expect($response->headers->get('Content-Disposition'))->toContain('attachment');
    expect($response->headers->get('Content-Disposition'))->toContain('.xlsx');
});

it('exports transactions to pdf respecting entry_mode filter', function () {
    $response = actingAs($this->user)
        ->get(route('bursary.transactions.export', [
            'format' => 'pdf',
            'entry_mode' => 'DE',
        ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type');
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

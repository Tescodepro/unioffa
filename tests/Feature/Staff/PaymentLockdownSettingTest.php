<?php

use App\Models\AcademicSession;
use App\Models\Campus;
use App\Models\Department;
use App\Models\EntryMode;
use App\Models\Faculty;
use App\Models\PaymentLockdownSetting;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Setup UserType for Staff (Bursary)
    $this->userType = UserType::updateOrCreate(
        ['name' => 'bursary'],
        ['id' => (string) Str::uuid(), 'dashboard_route' => 'burser.dashboard']
    );

    // 2. Create staff user
    $this->user = User::factory()->create([
        'user_type_id' => $this->userType->id,
    ]);

    // 3. Setup Campus
    $this->campus = Campus::create([
        'name' => 'Main Campus',
        'slug' => 'main-campus',
        'address' => 'Offa, Kwara State',
        'phone_number' => '08000000000',
        'email' => 'info@unioffa.edu.ng',
        'direction' => 'Near the bridge',
    ]);

    // 4. Setup Faculty and Department
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

    // 5. Setup EntryMode
    $this->entryMode = EntryMode::create([
        'name' => 'UTME',
        'code' => 'UTME',
        'student_type' => 'REGULAR',
        'matric_prefix' => 'CSC',
        'default_level' => 100,
    ]);

    // 6. Setup Session
    $this->session = AcademicSession::create([
        'name' => '2025/2026',
        'status' => 1,
        'status_upload_result' => 0,
        'lecturar_ids' => [],
        'students_ids' => [],
    ]);
});

it('can access payment lockdown settings index page', function () {
    PaymentLockdownSetting::create([
        'title' => 'First Semester Lockdown',
        'payment_type' => 'tuition',
        'deadline' => now()->addDays(5),
    ]);

    actingAs($this->user)
        ->get(route('bursary.payment-lockdown-settings.index'))
        ->assertSuccessful()
        ->assertViewHas('lockdowns');
});

it('can access create payment lockdown setting page', function () {
    actingAs($this->user)
        ->get(route('bursary.payment-lockdown-settings.create'))
        ->assertSuccessful()
        ->assertViewHas('campuses')
        ->assertViewHas('faculties')
        ->assertViewHas('departments');
});

it('can store a new payment lockdown setting', function () {
    $data = [
        'title' => 'New Lockdown',
        'payment_type' => 'tuition',
        'deadline' => now()->addDays(10)->toDateTimeString(),
        'campus_ids' => [$this->campus->id],
        'faculty_ids' => [$this->faculty->id],
        'department_ids' => [$this->department->id],
        'levels' => ['100', '200'],
        'admission_sessions' => ['2025/2026'],
        'genders' => ['Male'],
        'entry_modes' => ['UTME'],
        'programmes' => ['REGULAR'],
        'is_active' => '1',
    ];

    actingAs($this->user)
        ->post(route('bursary.payment-lockdown-settings.store'), $data)
        ->assertRedirect(route('bursary.payment-lockdown-settings.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('payment_lockdown_settings', [
        'title' => 'New Lockdown',
        'payment_type' => 'tuition',
        'is_active' => 1,
    ]);
});

it('can access edit payment lockdown setting page', function () {
    $lockdown = PaymentLockdownSetting::create([
        'title' => 'Lockdown to Edit',
        'payment_type' => 'tuition',
        'deadline' => now()->addDays(5),
    ]);

    actingAs($this->user)
        ->get(route('bursary.payment-lockdown-settings.edit', $lockdown))
        ->assertSuccessful()
        ->assertViewHas('lockdown');
});

it('can update a payment lockdown setting', function () {
    $lockdown = PaymentLockdownSetting::create([
        'title' => 'Old Title',
        'payment_type' => 'tuition',
        'deadline' => now()->addDays(5),
        'is_active' => true,
    ]);

    $data = [
        'title' => 'Updated Title',
        'payment_type' => 'acceptance',
        'deadline' => now()->addDays(20)->toDateTimeString(),
        'is_active' => '1',
    ];

    actingAs($this->user)
        ->put(route('bursary.payment-lockdown-settings.update', $lockdown), $data)
        ->assertRedirect(route('bursary.payment-lockdown-settings.index'))
        ->assertSessionHas('success');

    $lockdown->refresh();
    expect($lockdown->title)->toBe('Updated Title');
    expect($lockdown->payment_type)->toBe('acceptance');
});

it('can delete a payment lockdown setting', function () {
    $lockdown = PaymentLockdownSetting::create([
        'title' => 'Lockdown to Delete',
        'payment_type' => 'tuition',
        'deadline' => now()->addDays(5),
    ]);

    actingAs($this->user)
        ->delete(route('bursary.payment-lockdown-settings.destroy', $lockdown))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('payment_lockdown_settings', [
        'id' => $lockdown->id,
    ]);
});

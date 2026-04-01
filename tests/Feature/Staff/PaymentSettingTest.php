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

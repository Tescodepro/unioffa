@extends('layouts.app')

@section('title', 'Edit Payment Setting')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Edit Payment Setting</h4>
                    <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('bursary.payment-settings.update', $paymentSetting->id) }}" method="POST"
                    class="card p-4 shadow-sm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        {{-- Faculty --}}
                        <div class="col-md-6 mb-3">
                            <label>Faculty</label>
                            <select name="faculty_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}"
                                        {{ $paymentSetting->faculty_id == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->faculty_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Department --}}
                        <div class="col-md-6 mb-3">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ $paymentSetting->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->department_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Level --}}
                        <div class="col-md-6 mb-3">
                            <label>Level</label>
                            @php
                                $selectedLevels = $paymentSetting->level ?? [];
                            @endphp
                            <select name="level[]" multiple class="form-control">
                                @foreach ([100, 200, 300, 400, 500] as $lvl)
                                    <option value="{{ $lvl }}"
                                        {{ in_array($lvl, $selectedLevels) ? 'selected' : '' }}>
                                        {{ $lvl }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold CTRL (Windows) or CMD (Mac) to select multiple.</small>
                        </div>

                        {{-- Payment Type --}}
                        <div class="col-md-6 mb-3">
                            <label>Payment Type</label>
                            <input type="text" name="payment_type" class="form-control"
                                value="{{ old('payment_type', $paymentSetting->payment_type) }}" required>
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-3">
                            <label>Amount (₦)</label>
                            <input type="number" step="0.01" name="amount" class="form-control"
                                value="{{ old('amount', $paymentSetting->amount) }}" required>
                        </div>

                        {{-- Session --}}
                        <div class="col-md-6 mb-3">
                            <label>Session</label>
                            <input type="text" name="session" class="form-control"
                                value="{{ old('session', $paymentSetting->session) }}" placeholder="2024/2025" required>
                        </div>

                        {{-- Student Type --}}
                        <div class="col-md-6 mb-3">
                            <label>Student Type</label>
                            <input type="text" name="student_type" class="form-control"
                                value="{{ old('student_type', $paymentSetting->student_type) }}"
                                placeholder="REGULAR, TOPUP, IDEL">
                        </div>
                        {{-- Matric Number (Optional) --}}
<div class="col-md-6 mb-3">
    <label>Matric Number (Specific Student)</label>
    <input type="text" name="matric_number" class="form-control"
        value="{{ old('matric_number', $paymentSetting->matric_number) }}"
        placeholder="Enter matric number if specific to one student">
    <small class="text-muted">
        Leave blank to apply this payment setting to all students in the selected faculty/department.
    </small>
</div>


                        {{-- Description --}}
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $paymentSetting->description) }}</textarea>
                        </div>

                        {{-- INSTALLMENTAL SETTINGS --}}
                        <hr class="my-4">

                        <div class="col-md-4 mb-3">
                            <label>Allow Installment?</label>
                            <select name="installmental_allow_status" id="installmental_allow_status" class="form-control">
                                <option value="0"
                                    {{ !$paymentSetting->installmental_allow_status ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $paymentSetting->installmental_allow_status ? 'selected' : '' }}>
                                    Yes</option>
                            </select>
                        </div>

                        <div
                            class="col-md-4 mb-3 installment-section {{ !$paymentSetting->installmental_allow_status ? 'd-none' : '' }}">
                            <label>Number of Instalments</label>
                            <input type="number" name="number_of_instalment" id="number_of_instalment" min="1"
                                max="9"
                                value="{{ old('number_of_instalment', $paymentSetting->number_of_instalment) }}"
                                class="form-control">
                        </div>

                        <div
                            class="col-md-12 installment-section {{ !$paymentSetting->installmental_allow_status ? 'd-none' : '' }}">
                            <label>Instalment Percentages</label>
                            <div id="instalmentPercentages" class="row g-2">
                                @php
                                    $percentages = $paymentSetting->list_instalment_percentage ?? [];
                                @endphp
                                @foreach ($percentages as $index => $percent)
                                    <div class="col-md-2">
                                        <input type="number" name="list_instalment_percentage[]" class="form-control"
                                            value="{{ $percent }}" step="0.1" required>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Ensure the total equals 100%.</small>
                        </div>

                        <div class="col-12 mt-4">
                            <button class="btn btn-primary">
                                <i class="ti ti-save"></i> Update Setting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS: Dynamic instalment input handling --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allowInstallment = document.getElementById('installmental_allow_status');
            const installmentSection = document.querySelectorAll('.installment-section');
            const instalmentContainer = document.getElementById('instalmentPercentages');
            const numberInput = document.getElementById('number_of_instalment');

            allowInstallment.addEventListener('change', function() {
                installmentSection.forEach(sec => sec.classList.toggle('d-none', this.value == '0'));
            });

            numberInput.addEventListener('input', function() {
                instalmentContainer.innerHTML = '';
                const count = parseInt(this.value);
                if (count > 0) {
                    const equalSplit = Math.floor(100 / count);
                    for (let i = 0; i < count; i++) {
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = 'list_instalment_percentage[]';
                        input.step = '0.1';
                        input.required = true;
                        input.className = 'form-control col-md-2';
                        input.value = equalSplit;
                        instalmentContainer.appendChild(input);
                    }
                }
            });
        });
    </script>
@endsection

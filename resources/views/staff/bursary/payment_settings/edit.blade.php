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

                {{-- Alerts --}}
                @include('layouts.flash-message')

                <form action="{{ route('bursary.payment-settings.update', $paymentSetting->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1: Payment Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-currency-naira me-2"></i>Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Payment Type <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="payment_type" class="form-control"
                                        value="{{ old('payment_type', $paymentSetting->payment_type) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Amount (₦) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control"
                                        value="{{ old('amount', $paymentSetting->amount) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Session <span class="text-danger">*</span></label>
                                    <input type="text" name="session" class="form-control"
                                        value="{{ old('session', $paymentSetting->session) }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="2">{{ old('description', $paymentSetting->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Targeting (Who should pay?) --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Targeting (Who should pay?)</h5>
                            <small class="text-muted">Leave fields blank to apply to all students.</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Faculty</label>
                                    <select name="faculty_id" class="form-select">
                                        <option value="">All Faculties</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ $paymentSetting->faculty_id == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->faculty_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ $paymentSetting->department_id == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->department_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Level(s)</label>
                                    @php $selectedLevels = $paymentSetting->level ?? []; @endphp
                                    <select name="level[]" multiple class="form-select" style="min-height: 100px;">
                                        @foreach ([100, 200, 300, 400, 500] as $lvl)
                                            <option value="{{ $lvl }}" {{ in_array($lvl, $selectedLevels) ? 'selected' : '' }}>
                                                {{ $lvl }} Level
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Ctrl/Cmd + Click to select multiple.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Student Type (Programme)</label>
                                    <select name="student_type" class="form-select">
                                        <option value="">All Programmes</option>
                                        <option value="REGULAR" {{ old('student_type', $paymentSetting->student_type) == 'REGULAR' ? 'selected' : '' }}>REGULAR</option>
                                        <option value="TOPUP" {{ old('student_type', $paymentSetting->student_type) == 'TOPUP' ? 'selected' : '' }}>TOPUP</option>
                                        <option value="IDELDE" {{ old('student_type', $paymentSetting->student_type) == 'IDELDE' ? 'selected' : '' }}>IDELDE</option>
                                        <option value="IDELUTME" {{ old('student_type', $paymentSetting->student_type) == 'IDELUTME' ? 'selected' : '' }}>IDELUTME
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Entry Mode</label>
                                    <select name="entry_mode" class="form-select">
                                        <option value="">All Entry Modes</option>
                                        <option value="UTME" {{ old('entry_mode', $paymentSetting->entry_mode) == 'UTME' ? 'selected' : '' }}>UTME</option>
                                        <option value="DE" {{ old('entry_mode', $paymentSetting->entry_mode) == 'DE' ? 'selected' : '' }}>DE (Direct Entry)</option>
                                        <option value="TRANSFER" {{ old('entry_mode', $paymentSetting->entry_mode) == 'TRANSFER' ? 'selected' : '' }}>TRANSFER</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Specific Student (Matric No.)</label>
                                    <input type="text" name="matric_number" class="form-control"
                                        value="{{ old('matric_number', $paymentSetting->matric_number) }}"
                                        placeholder="Leave blank for all">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Installment Settings --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-list-numbers me-2"></i>Installment Options</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Allow Installment Payment?</label>
                                    <select name="installmental_allow_status" id="installmental_allow_status"
                                        class="form-select">
                                        <option value="0" {{ !$paymentSetting->installmental_allow_status ? 'selected' : '' }}>No - Full payment only</option>
                                        <option value="1" {{ $paymentSetting->installmental_allow_status ? 'selected' : '' }}>Yes - Allow partial payments</option>
                                    </select>
                                </div>
                                <div
                                    class="col-md-4 mb-3 installment-section {{ !$paymentSetting->installmental_allow_status ? 'd-none' : '' }}">
                                    <label class="form-label">Number of Instalments</label>
                                    <input type="number" name="number_of_instalment" id="number_of_instalment" min="2"
                                        max="9"
                                        value="{{ old('number_of_instalment', $paymentSetting->number_of_instalment ?? 2) }}"
                                        class="form-control">
                                </div>
                                <div
                                    class="col-md-12 installment-section {{ !$paymentSetting->installmental_allow_status ? 'd-none' : '' }}">
                                    <label class="form-label">Instalment Percentages</label>
                                    <div id="instalmentPercentages" class="row g-2">
                                        @php $percentages = $paymentSetting->list_instalment_percentage ?? []; @endphp
                                        @foreach ($percentages as $index => $percent)
                                            <div class="col-md-2 col-6">
                                                <small class="text-muted">Part {{ $index + 1 }}</small>
                                                <input type="number" name="list_instalment_percentage[]"
                                                    class="form-control mt-1" value="{{ $percent }}" step="0.1" required>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">Enter the percentage for each installment. Total must equal
                                        100%.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ti ti-device-floppy me-2"></i> Update Payment Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS: Dynamic instalment input handling --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const allowInstallment = document.getElementById('installmental_allow_status');
            const installmentSection = document.querySelectorAll('.installment-section');
            const instalmentContainer = document.getElementById('instalmentPercentages');
            const numberInput = document.getElementById('number_of_instalment');

            function generatePercentageInputs() {
                instalmentContainer.innerHTML = '';
                const count = parseInt(numberInput.value) || 2;
                if (count > 0) {
                    const equalSplit = Math.floor(100 / count);
                    for (let i = 0; i < count; i++) {
                        const div = document.createElement('div');
                        div.className = 'col-md-2 col-6';
                        const label = document.createElement('small');
                        label.className = 'text-muted';
                        label.textContent = 'Part ' + (i + 1);
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = 'list_instalment_percentage[]';
                        input.step = '0.1';
                        input.required = true;
                        input.className = 'form-control mt-1';
                        input.value = i === count - 1 ? (100 - equalSplit * (count - 1)) : equalSplit;
                        div.appendChild(label);
                        div.appendChild(input);
                        instalmentContainer.appendChild(div);
                    }
                }
            }

            allowInstallment.addEventListener('change', function () {
                const show = this.value === '1';
                installmentSection.forEach(sec => sec.classList.toggle('d-none', !show));
                if (show && instalmentContainer.children.length === 0) generatePercentageInputs();
            });

            numberInput.addEventListener('input', generatePercentageInputs);
        });
    </script>
@endsection
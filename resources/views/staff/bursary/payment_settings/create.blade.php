@extends('layouts.app')

@section('title', 'Add Payment Setting')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Add Payment Setting</h4>
                    <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>

                {{-- Alerts --}}
                @include('layouts.flash-message')

                <form action="{{ route('bursary.payment-settings.store') }}" method="POST">
                    @csrf

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
                                        placeholder="e.g. Tuition, Acceptance Fee" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Amount (₦) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control"
                                        placeholder="e.g. 50000" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Session <span class="text-danger">*</span></label>
                                    <input type="text" name="session" class="form-control" placeholder="e.g. 2024/2025"
                                        required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2"
                                        placeholder="Optional: Brief description of this payment"></textarea>
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
                                            <option value="{{ $faculty->id }}">{{ $faculty->faculty_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->department_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Level(s)</label>
                                    <select name="level[]" multiple class="form-select" style="min-height: 100px;">
                                        @foreach ([100, 200, 300, 400, 500] as $lvl)
                                            <option value="{{ $lvl }}">{{ $lvl }} Level</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Ctrl/Cmd + Click to select multiple.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Student Type (Programme)</label>
                                    <select name="student_type[]" multiple class="form-select" style="min-height: 100px;">
                                        <option value="REGULAR">REGULAR</option>
                                        <option value="TOPUP">TOPUP</option>
                                        <option value="IDELDE">IDELDE</option>
                                        <option value="IDELUTME">IDELUTME</option>
                                    </select>
                                    <small class="text-muted">Ctrl/Cmd + Click to select multiple.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Entry Mode</label>
                                    <select name="entry_mode[]" multiple class="form-select" style="min-height: 100px;">
                                        <option value="UTME">UTME</option>
                                        <option value="DE">DE (Direct Entry)</option>
                                        <option value="TRANSFER">TRANSFER</option>
                                    </select>
                                    <small class="text-muted">Ctrl/Cmd + Click to select multiple.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Specific Student (Matric No.)</label>
                                    <input type="text" name="matric_number" class="form-control"
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
                                        <option value="0" selected>No - Full payment only</option>
                                        <option value="1">Yes - Allow partial payments</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 installment-section d-none">
                                    <label class="form-label">Number of Instalments</label>
                                    <input type="number" name="number_of_instalment" id="number_of_instalment" min="2"
                                        max="9" class="form-control" value="2">
                                </div>
                                <div class="col-md-12 installment-section d-none">
                                    <label class="form-label">Instalment Percentages</label>
                                    <div id="instalmentPercentages" class="row g-2"></div>
                                    <small class="text-muted">Enter the percentage for each installment. Total must equal
                                        100%.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ti ti-device-floppy me-2"></i> Save Payment Setting
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
                        // Required only if visible
                        input.required = true; 
                        input.className = 'form-control mt-1';
                        input.value = i === count - 1 ? (100 - equalSplit * (count - 1)) : equalSplit;
                        div.appendChild(label);
                        div.appendChild(input);
                        instalmentContainer.appendChild(div);
                    }
                }
            }

            function toggleInstallmentFields() {
                const show = allowInstallment.value === '1';
                installmentSection.forEach(sec => sec.classList.toggle('d-none', !show));
                
                // Toggle required and disabled states to prevent validation errors on hidden fields
                if (show) {
                    numberInput.removeAttribute('disabled');
                    numberInput.setAttribute('required', 'required');
                    if (instalmentContainer.children.length === 0) generatePercentageInputs();
                } else {
                    numberInput.setAttribute('disabled', 'disabled');
                    numberInput.removeAttribute('required');
                    instalmentContainer.innerHTML = ''; // Clear generated inputs so they aren't validated
                }
            }

            allowInstallment.addEventListener('change', toggleInstallmentFields);
            numberInput.addEventListener('input', generatePercentageInputs);
            
            // Initial check
            // We manually call this to ensure initial state is correct (e.g. if browser cached value)
            if (allowInstallment.value === '1') {
                 // If accidentally cached as 1 but fields hidden by default HTML, this fixes it
                 toggleInstallmentFields();
            } else {
                 // Ensure disabled is set if default is 0
                 numberInput.setAttribute('disabled', 'disabled');
                 numberInput.removeAttribute('required');
            }
        });
    </script>
@endsection
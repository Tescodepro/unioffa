{{--
Shared partial for payment setting form fields.
--}}

@php
    $ps = $paymentSetting ?? null;
    $selectedLevels = $ps ? ($ps->level ?? []) : [];
    $selectedTypes = $ps ? ($ps->student_type ?? []) : [];
    $selectedModes = $ps ? ($ps->entry_mode ?? []) : [];
    $selectedFaculties = $ps ? ($ps->faculty_ids ?? []) : [];
    $selectedDepartments = $ps ? ($ps->department_ids ?? []) : [];
    $selectedSexes = $ps ? ($ps->sexes ?? []) : [];
    $selectedSemesters = $ps ? ($ps->semesters ?? []) : [];
    $percentages = $ps ? ($ps->list_instalment_percentage ?? []) : [];
    $matricNumbers = $ps ? ($ps->matric_numbers ?? []) : [];
    $selectedAdmissionSessions = $ps ? ($ps->admission_session ?? []) : [];
@endphp

<div class="row g-4">
    {{-- ── LEFT COLUMN: Core Config ── --}}
    <div class="col-lg-8">
        {{-- SECTION 1: Financial Details --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <div class="bg-soft-primary p-2 rounded-3 me-3">
                    <i class="ti ti-wallet fs-4 text-primary"></i>
                </div>
                <h6 class="mb-0 fw-bold">Financial Configuration</h6>
            </div>
            <div class="card-body bg-white pt-2">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Payment Type <span class="text-danger">*</span></label>
                        <input type="text" name="payment_type" class="form-control form-control-lg bg-light border-0 @error('payment_type') is-invalid @enderror"
                            value="{{ old('payment_type', optional($ps)->payment_type) }}" placeholder="e.g. tuition, acceptance" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Standard Amount (₦) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0 text-muted">₦</span>
                            <input type="number" step="0.01" name="amount" class="form-control bg-light border-0 @error('amount') is-invalid @enderror"
                                value="{{ old('amount', optional($ps)->amount) }}" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted text-uppercase">Internal Description</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="2"
                            placeholder="Briefly explain the purpose of this fee...">{{ old('description', optional($ps)->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="is_compulsory" value="0">
                            <input class="form-check-input" type="checkbox" name="is_compulsory" id="is_compulsory" value="1" {{ old('is_compulsory', $ps->is_compulsory ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small text-muted" for="is_compulsory">
                                Compulsory for Course Registration
                            </label>
                            <div class="form-text x-small">If enabled, students must clear this fee before they can register for courses.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: Academic Targeting --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <div class="bg-soft-info p-2 rounded-3 me-3">
                    <i class="ti ti-school fs-4 text-info"></i>
                </div>
                <h6 class="mb-0 fw-bold">Academic Targeting</h6>
            </div>
            <div class="card-body bg-white pt-2">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Target Faculties</label>
                        <select name="faculty_ids[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ in_array($faculty->id, $selectedFaculties) ? 'selected' : '' }}>
                                    {{ $faculty->faculty_code }} — {{ $faculty->faculty_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text x-small">Leave empty for universal faculty access</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Target Departments</label>
                        <select name="department_ids[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ in_array($dept->id, $selectedDepartments) ? 'selected' : '' }}>
                                    {{ $dept->department_code }} — {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Academic Level(s)</label>
                        <select name="level[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ([100, 200, 300, 400, 500] as $lvl)
                                <option value="{{ $lvl }}" {{ in_array($lvl, $selectedLevels) ? 'selected' : '' }}>
                                    Level {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Programme Type</label>
                        <select name="student_type[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ($entryModes->pluck('student_type')->unique()->filter() as $prog)
                                <option value="{{ $prog }}" {{ in_array($prog, $selectedTypes) ? 'selected' : '' }}>
                                    {{ $prog }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Mode of Entry</label>
                        <select name="entry_mode[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ($entryModes as $mode)
                                <option value="{{ $mode->code }}" {{ in_array($mode->code, $selectedModes) ? 'selected' : '' }}>
                                    {{ $mode->code }} ({{ $mode->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small text-muted text-uppercase">Target Admission Session(s)</label>
                        <select name="admission_session[]" multiple class="form-select select2-multi bg-light border-0">
                            @foreach ($sessions as $sess)
                                <option value="{{ $sess }}" {{ in_array($sess, $selectedAdmissionSessions) ? 'selected' : '' }}>
                                    {{ $sess }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text x-small">Leave empty for universal admission session access</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Individual Overrides --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <div class="bg-soft-dark p-2 rounded-3 me-3">
                    <i class="ti ti-users-group fs-4 text-dark"></i>
                </div>
                <h6 class="mb-0 fw-bold">Individual Student Filtering</h6>
            </div>
            <div class="card-body bg-white pt-2">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold small text-muted text-uppercase">Specific Matric Numbers</label>
                        <textarea name="matric_numbers[]" class="form-control bg-light border-0" rows="3" 
                            placeholder="Enter matric numbers separated by commas or new lines...">{{ implode(', ', $matricNumbers) }}</textarea>
                        <div class="form-text x-small text-info"><i class="ti ti-info-circle"></i> Use this to restrict or allow specific students regardless of their department/level.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT COLUMN: Schedule & Rules ── --}}
    <div class="col-lg-4">
        {{-- Academic Schedule --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold text-dark">Academic Schedule</h6>
            </div>
            <div class="card-body bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Active Session <span class="text-danger">*</span></label>
                    <select name="session" class="form-select bg-light border-0 shadow-none" required>
                        <option value="">-- Select Session --</option>
                        @foreach ($sessions as $sess)
                            <option value="{{ $sess }}" {{ old('session', optional($ps)->session) == $sess ? 'selected' : '' }}>
                                {{ $sess }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold small text-muted text-uppercase">Target Semester(s)</label>
                    <select name="semesters[]" multiple class="form-select select2-multi bg-light border-0 shadow-none">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->code }}" {{ in_array($sem->code, $selectedSemesters) ? 'selected' : '' }}>
                                {{ $sem->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text x-small">Empty = Full Session (No semester split)</div>
                </div>
            </div>
        </div>

        {{-- Installment Policy --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden border-top border-4 border-success">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">Installment Policy</h6>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggle_installments" {{ (optional($ps)->installmental_allow_status) ? 'checked' : '' }}>
                    <input type="hidden" name="installmental_allow_status" id="installmental_allow_status_hidden" value="{{ optional($ps)->installmental_allow_status ?? 0 }}">
                </div>
            </div>
            <div class="card-body bg-white pt-0 installment-section {{ !(optional($ps)->installmental_allow_status) ? 'd-none' : '' }}">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Number of Parts</label>
                    <input type="number" name="number_of_instalment" id="number_of_instalment" min="2" max="4"
                        class="form-control bg-light border-0 shadow-none" 
                        value="{{ old('number_of_instalment', optional($ps)->number_of_instalment ?? 2) }}"
                        {{ !(optional($ps)->installmental_allow_status) ? 'disabled' : '' }}>
                </div>
                <div class="p-3 bg-light rounded-3 shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small fw-bold text-muted text-uppercase">Split Percentages</span>
                        <span class="badge bg-white text-dark border shadow-sm rounded-pill"><span id="totalPct">0</span>% / 100%</span>
                    </div>
                    <div id="instalmentPercentages" class="row g-2">
                        @foreach ($percentages as $index => $percent)
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-0 text-muted">P{{ $index + 1 }}</span>
                                    <input type="number" name="list_instalment_percentage[]" class="form-control border-0 shadow-none pct-input"
                                        value="{{ $percent }}" step="0.1" min="1" max="100">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text x-small mt-3 text-center">Percentages must sum to exactly 100%</div>
                </div>
            </div>
            <div class="card-footer bg-success bg-opacity-5 border-0 py-3 {{ (optional($ps)->installmental_allow_status) ? 'd-none' : '' }}" id="full_payment_note">
                <p class="text-success small mb-0 d-flex align-items-center">
                    <i class="ti ti-info-square-rounded-filled me-2"></i> Only full payments will be accepted for this fee.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    .rounded-4 { border-radius: 1rem !important; }
    .select2-container--default .select2-selection--multiple {
        background-color: #f8fafc !important;
        border: none !important;
        padding: 4px 8px !important;
        border-radius: 8px !important;
    }
    .installment-section { transition: all 0.3s ease-in-out; }
</style>
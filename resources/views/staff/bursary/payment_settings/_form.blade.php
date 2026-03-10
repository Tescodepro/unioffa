{{--
Shared partial for payment setting form fields.
Used by both create.blade.php and edit.blade.php.
Requires: $faculties, $departments, $entryModes, $sessions
Optional: $paymentSetting (for edit mode)
--}}

@php
    $ps = $paymentSetting ?? null;
    $selectedLevels = $ps?->level ?? [];
    $selectedTypes = $ps?->student_type ?? [];
    $selectedModes = $ps?->entry_mode ?? [];
    $percentages = $ps?->list_instalment_percentage ?? [];
@endphp

{{-- ── SECTION 1: Payment Details ───────────────────────────────────── --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-bold"><i class="ti ti-currency-naira text-primary me-2"></i>Payment Details</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Payment Type <span class="text-danger">*</span></label>
                <input type="text" name="payment_type" class="form-control @error('payment_type') is-invalid @enderror"
                    value="{{ old('payment_type', $ps?->payment_type) }}" placeholder="e.g. tuition, acceptance"
                    required>
                @error('payment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Amount (₦) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">₦</span>
                    <input type="number" step="0.01" name="amount"
                        class="form-control @error('amount') is-invalid @enderror"
                        value="{{ old('amount', $ps?->amount) }}" placeholder="0.00" required>
                </div>
                @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Session <span class="text-danger">*</span></label>
                <select name="session" class="form-select @error('session') is-invalid @enderror" required>
                    <option value="">-- Select Session --</option>
                    @foreach ($sessions as $sess)
                        <option value="{{ $sess }}" {{ old('session', $ps?->session) == $sess ? 'selected' : '' }}>
                            {{ $sess }}
                        </option>
                    @endforeach
                </select>
                @error('session') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Semester</label>
                <select name="semester" class="form-select">
                    <option value="" {{ !($ps?->semester) ? 'selected' : '' }}>All Semesters (Full Session)</option>
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem->code }}" {{ old('semester', $ps?->semester) == $sem->code ? 'selected' : '' }}>
                            {{ $sem->name }} ({{ $sem->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label">Description <span class="text-muted small fw-normal">(optional)</span></label>
                <textarea name="description" class="form-control" rows="2"
                    placeholder="Brief description of this fee...">{{ old('description', $ps?->description) }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ── SECTION 2: Targeting ──────────────────────────────────────────── --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="ti ti-users text-primary me-2"></i>Who Should Pay?</h6>
        <span class="badge bg-light text-muted border fs-xs">Leave blank = applies to ALL</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Faculty</label>
                <select name="faculty_id" class="form-select">
                    <option value="">All Faculties</option>
                    @foreach ($faculties as $faculty)
                        <option value="{{ $faculty->id }}" {{ old('faculty_id', $ps?->faculty_id) == $faculty->id ? 'selected' : '' }}>
                            {{ $faculty->faculty_code }} — {{ $faculty->faculty_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $ps?->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->department_code }} — {{ $dept->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Specific Student (Matric No.)</label>
                <input type="text" name="matric_number" class="form-control"
                    value="{{ old('matric_number', $ps?->matric_number) }}" placeholder="Leave blank for all">
                <div class="form-text">Only fills one student by matric number</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Level(s)</label>
                <select name="level[]" multiple class="form-select select2-multi" id="select2-level">
                    @foreach ([100, 200, 300, 400, 500] as $lvl)
                        <option value="{{ $lvl }}" {{ in_array($lvl, $selectedLevels) ? 'selected' : '' }}>
                            {{ $lvl }} Level
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Student Type (Programme)</label>
                <select name="student_type[]" multiple class="form-select select2-multi" id="select2-student-type">
                    @foreach ($entryModes->pluck('student_type')->unique()->filter() as $prog)
                        <option value="{{ $prog }}" {{ in_array($prog, $selectedTypes) ? 'selected' : '' }}>
                            {{ $prog }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Entry Mode</label>
                <select name="entry_mode[]" multiple class="form-select select2-multi" id="select2-entry-mode">
                    @foreach ($entryModes as $mode)
                        <option value="{{ $mode->code }}" {{ in_array($mode->code, $selectedModes) ? 'selected' : '' }}>
                            {{ $mode->name }} ({{ $mode->code }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- ── SECTION 3: Installment ────────────────────────────────────────── --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-bold"><i class="ti ti-list-numbers text-primary me-2"></i>Installment Options</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Allow Installment Payment?</label>
                <select name="installmental_allow_status" id="installmental_allow_status" class="form-select">
                    <option value="0" {{ !($ps?->installmental_allow_status) ? 'selected' : '' }}>No — Full payment only
                    </option>
                    <option value="1" {{ ($ps?->installmental_allow_status) ? 'selected' : '' }}>Yes — Allow partial
                        payments</option>
                </select>
            </div>
            <div class="col-md-4 installment-section {{ !($ps?->installmental_allow_status) ? 'd-none' : '' }}">
                <label class="form-label">Number of Instalments</label>
                <input type="number" name="number_of_instalment" id="number_of_instalment" min="2" max="9"
                    class="form-control" value="{{ old('number_of_instalment', $ps?->number_of_instalment ?? 2) }}">
            </div>
            <div
                class="col-md-4 installment-section {{ !($ps?->installmental_allow_status) ? 'd-none' : '' }} d-flex align-items-end">
                <div class="alert alert-info py-2 px-3 mb-0 w-100 small" id="percentTotal">
                    <i class="ti ti-info-circle me-1"></i> Total: <strong id="totalPct">0</strong>% (must equal 100%)
                </div>
            </div>
            <div class="col-md-12 installment-section {{ !($ps?->installmental_allow_status) ? 'd-none' : '' }}">
                <label class="form-label">Instalment Percentages</label>
                <div id="instalmentPercentages" class="row g-2">
                    @foreach ($percentages as $index => $percent)
                        <div class="col-md-2 col-4">
                            <label class="text-muted small">Part {{ $index + 1 }}</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="list_instalment_percentage[]" class="form-control pct-input"
                                    value="{{ $percent }}" step="0.1" min="1" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-text">Percentages must add up to exactly 100%</div>
            </div>
        </div>
    </div>
</div>
@extends('layouts.app')

@section('title', 'Edit Late Payment Penalty')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: #f8fafc !important;
            border: none !important;
            padding: 5px 10px !important;
            border-radius: 10px !important;
        }
        .form-label-premium { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 0.5rem; }
        .rounded-4 { border-radius: 1rem !important; }
        .x-small { font-size: 0.75rem; }
    </style>
@endpush

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Edit Late Payment Penalty</h4>
                        <p class="text-muted small mb-0">Update the deadline and penalty fee for a specific payment type</p>
                    </div>
                    <a href="{{ route('bursary.late-payment-settings.index') }}" class="btn btn-light border shadow-sm px-3">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                @include('layouts.flash-message')

                <form action="{{ route('bursary.late-payment-settings.update', $latePaymentSetting->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        {{-- LEFT COLUMN: Penalty Rules --}}
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                    <div class="bg-soft-warning p-2 rounded-3 me-3">
                                        <i class="ti ti-edit fs-4 text-warning"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">Update Financial Penalty Rules</h6>
                                </div>
                                <div class="card-body bg-white pt-2">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Target Fee Type <span class="text-danger">*</span></label>
                                            <select name="payment_type" class="form-select form-select-lg border-0 bg-light @error('payment_type') is-invalid @enderror" required>
                                                <option value="">-- Select Target Fee --</option>
                                                @foreach ($paymentTypes as $type)
                                                    <option value="{{ $type }}" {{ old('payment_type', $latePaymentSetting->payment_type) == $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Initial Penalty Amount (₦) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text border-0 bg-light text-muted">₦</span>
                                                <input type="number" step="0.01" name="late_fee_amount" 
                                                    class="form-control border-0 bg-light @error('late_fee_amount') is-invalid @enderror"
                                                    value="{{ old('late_fee_amount', $latePaymentSetting->late_fee_amount) }}" required>
                                            </div>
                                        </div>

                                        <div class="col-12"><hr class="my-3 opacity-25"></div>

                                        <div class="col-md-6">
                                            <label class="form-label-premium">Tier 2 Escalation (₦) <span class="text-muted fw-normal">(Optional)</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text border-0 bg-light text-muted">₦</span>
                                                <input type="number" step="0.01" name="increment_amount" 
                                                    class="form-control border-0 bg-light @error('increment_amount') is-invalid @enderror"
                                                    value="{{ old('increment_amount', $latePaymentSetting->increment_amount) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Tier 2 Deadline <span class="text-muted fw-normal">(Optional)</span></label>
                                            <input type="datetime-local" name="increment_date" 
                                                class="form-control form-control-lg border-0 bg-light @error('increment_date') is-invalid @enderror" 
                                                value="{{ old('increment_date', $latePaymentSetting->increment_date ? \Carbon\Carbon::parse($latePaymentSetting->increment_date)->format('Y-m-d\TH:i') : '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                    <div class="bg-soft-dark p-2 rounded-3 me-3">
                                        <i class="ti ti-users-group fs-4 text-dark"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">Exclusion Rules</h6>
                                </div>
                                <div class="card-body bg-white pt-2">
                                    <div class="mb-0">
                                        <label class="form-label-premium">Excluded Matric Numbers</label>
                                        <textarea name="excluded_matric_numbers" class="form-control border-0 bg-light @error('excluded_matric_numbers') is-invalid @enderror" 
                                            rows="4" placeholder="Enter matric numbers separated by commas or new lines...">{{ old('excluded_matric_numbers', is_array($latePaymentSetting->excluded_matric_numbers) ? implode(', ', $latePaymentSetting->excluded_matric_numbers) : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Schedule & Scope --}}
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-top border-4 border-primary">
                                <div class="card-header bg-white border-0 py-3">
                                    <h6 class="mb-0 fw-bold text-primary">Deadline Control</h6>
                                </div>
                                <div class="card-body bg-white">
                                    <div class="mb-0">
                                        <label class="form-label-premium">Primary Closing Date <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="closing_date" 
                                            class="form-control border-0 bg-light @error('closing_date') is-invalid @enderror" 
                                            value="{{ old('closing_date', \Carbon\Carbon::parse($latePaymentSetting->closing_date)->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3">
                                    <h6 class="mb-0 fw-bold">Academic Scope</h6>
                                </div>
                                <div class="card-body bg-white">
                                    <div class="mb-3">
                                        <label class="form-label-premium">Campus <span class="text-danger">*</span></label>
                                        <select name="campus_id" class="form-select border-0 bg-light @error('campus_id') is-invalid @enderror" required>
                                            <option value="">-- Select Campus --</option>
                                            @foreach ($campuses as $campus)
                                                <option value="{{ $campus->id }}" {{ old('campus_id', $latePaymentSetting->campus_id) == $campus->id ? 'selected' : '' }}>
                                                    {{ $campus->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-premium">Academic Session</label>
                                        <select name="session" class="form-select border-0 bg-light">
                                            <option value="">Universal (All Sessions)</option>
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session }}" {{ old('session', $latePaymentSetting->session) == $session ? 'selected' : '' }}>
                                                    {{ $session }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Semester</label>
                                        <select name="semester" class="form-select border-0 bg-light">
                                            <option value="">Full Academic Year</option>
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester->code }}" {{ old('semester', $latePaymentSetting->semester) == $semester->code ? 'selected' : '' }}>
                                                    {{ $semester->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label-premium">Entry Modes</label>
                                        <select name="entry_mode[]" class="form-select select2-multi" multiple="multiple">
                                             @foreach ($entryModes as $mode)
                                                 <option value="{{ $mode->code }}" {{ in_array($mode->code, old('entry_mode', is_array($latePaymentSetting->entry_mode) ? $latePaymentSetting->entry_mode : [])) ? 'selected' : '' }}>
                                                     {{ $mode->code }}
                                                 </option>
                                             @endforeach
                                         </select>
                                     </div>
                                     <div class="mb-3">
                                         <label class="form-label-premium">Programme (Student Type)</label>
                                         <select name="student_type[]" class="form-select select2-multi" multiple="multiple">
                                             @foreach ($programmes as $programme)
                                                 <option value="{{ $programme }}" {{ in_array($programme, old('student_type', is_array($latePaymentSetting->student_type) ? $latePaymentSetting->student_type : [])) ? 'selected' : '' }}>
                                                     {{ $programme }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <div class="form-text x-small">Empty = All Programmes</div>
                                     </div>
                                     <div class="mb-3">
                                         <label class="form-label-premium">Target Levels</label>
                                         <select name="level[]" class="form-select select2-multi" multiple="multiple">
                                             @foreach ($levels as $level)
                                                 <option value="{{ $level }}" {{ in_array($level, old('level', is_array($latePaymentSetting->level) ? $latePaymentSetting->level : [])) ? 'selected' : '' }}>
                                                     Level {{ $level }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <div class="form-text x-small">Empty = All Levels</div>
                                     </div>
                                     <div class="mb-0">
                                         <label class="form-label-premium">Target Admission Sessions</label>
                                         <select name="admission_session[]" class="form-select select2-multi" multiple="multiple">
                                             @foreach ($sessions as $sess)
                                                 <option value="{{ $sess }}" {{ in_array($sess, old('admission_session', is_array($latePaymentSetting->admission_session) ? $latePaymentSetting->admission_session : [])) ? 'selected' : '' }}>
                                                     {{ $sess }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <div class="form-text x-small">Empty = All Admission Sessions</div>
                                     </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-3 shadow-sm rounded-3">
                                <i class="ti ti-device-floppy me-2"></i> Update Penalty Rules
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2-multi').select2({
                placeholder: 'Select (All by default)',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush

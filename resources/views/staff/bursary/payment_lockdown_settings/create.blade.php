@extends('layouts.app')

@section('title', 'Add Payment Lockdown Setting')

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
                        <h4 class="mb-0 fw-bold">Add Payment Lockdown Setting</h4>
                        <p class="text-muted small mb-0">Set a deadline to lock the payment portal for targeted students</p>
                    </div>
                    <a href="{{ route('bursary.payment-lockdown-settings.index') }}" class="btn btn-light border shadow-sm px-3">
                        <i class="ti ti-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                @include('layouts.flash-message')

                <form action="{{ route('bursary.payment-lockdown-settings.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        {{-- LEFT COLUMN: Lockdown Rules --}}
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                    <div class="bg-soft-danger p-2 rounded-3 me-3">
                                        <i class="ti ti-lock fs-4 text-danger"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">Lockdown Rules</h6>
                                </div>
                                <div class="card-body bg-white pt-2">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label-premium">Lockdown Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control form-control-lg border-0 bg-light @error('title') is-invalid @enderror" 
                                                placeholder="e.g. 2025/2026 First Semester Tuition Portal Closure" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label-premium">Target Fee Type</label>
                                            <select name="payment_type" class="form-select form-select-lg border-0 bg-light @error('payment_type') is-invalid @enderror">
                                                <option value="">Universal (Locks All Payment Types)</option>
                                                @foreach ($paymentTypes as $type)
                                                    <option value="{{ $type }}" {{ old('payment_type') == $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text x-small">Select a specific fee to lock, or leave blank to lock all payments.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label-premium">Lockdown Deadline <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="deadline" class="form-control form-control-lg border-0 bg-light @error('deadline') is-invalid @enderror" 
                                                value="{{ old('deadline') }}" required>
                                            <div class="form-text x-small">Targeted students cannot make payments after this time.</div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <div class="form-check form-switch bg-light p-3 rounded-3 d-flex align-items-center justify-content-between">
                                                <div>
                                                    <label class="form-check-label fw-bold text-dark mb-1" for="is_active">Lockdown Active</label>
                                                    <p class="text-muted extra-small mb-0">Toggle whether this lockdown rule should be active immediately.</p>
                                                </div>
                                                <input class="form-check-input fs-4" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Targeted Student Scope --}}
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-top border-4 border-primary">
                                <div class="card-header bg-white border-0 py-3">
                                    <h6 class="mb-0 fw-bold text-primary">Targeted Student Scope</h6>
                                    <p class="text-muted extra-small mb-0">Select criteria. If all criteria are left empty, lockdown applies to all students.</p>
                                </div>
                                <div class="card-body bg-white pt-1">
                                    {{-- Campus --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Campuses</label>
                                        <select name="campus_ids[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($campuses as $campus)
                                                <option value="{{ $campus->id }}" {{ in_array($campus->id, old('campus_ids', [])) ? 'selected' : '' }}>
                                                    {{ $campus->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Faculty --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Faculties</label>
                                        <select name="faculty_ids[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($faculties as $faculty)
                                                <option value="{{ $faculty->id }}" {{ in_array($faculty->id, old('faculty_ids', [])) ? 'selected' : '' }}>
                                                    {{ $faculty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Department --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Departments</label>
                                        <select name="department_ids[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ in_array($dept->id, old('department_ids', [])) ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Levels --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Levels</label>
                                        <select name="levels[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($levels as $lvl)
                                                <option value="{{ $lvl }}" {{ in_array($lvl, old('levels', [])) ? 'selected' : '' }}>
                                                    Level {{ $lvl }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Admission Session --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Admission Sessions</label>
                                        <select name="admission_sessions[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session }}" {{ in_array($session, old('admission_sessions', [])) ? 'selected' : '' }}>
                                                    {{ $session }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Gender --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Genders</label>
                                        <select name="genders[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($genders as $gender)
                                                <option value="{{ $gender }}" {{ in_array($gender, old('genders', [])) ? 'selected' : '' }}>
                                                    {{ $gender }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Entry Modes --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Entry Modes</label>
                                        <select name="entry_modes[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($entryModes as $mode)
                                                <option value="{{ $mode->code }}" {{ in_array($mode->code, old('entry_modes', [])) ? 'selected' : '' }}>
                                                    {{ $mode->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Programmes --}}
                                    <div class="mb-3">
                                        <label class="form-label-premium">Target Programmes (Student Type)</label>
                                        <select name="programmes[]" class="form-select select2-multi" multiple="multiple">
                                            @foreach ($programmes as $programme)
                                                <option value="{{ $programme }}" {{ in_array($programme, old('programmes', [])) ? 'selected' : '' }}>
                                                    {{ $programme }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm rounded-3">
                                <i class="ti ti-device-floppy me-2"></i> Save Lockdown Rule
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-multi').select2({
                placeholder: 'Universal (All)',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush

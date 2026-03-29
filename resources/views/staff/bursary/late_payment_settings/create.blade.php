@extends('layouts.app')

@section('title', 'Add Late Payment Penalty')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Add Late Payment Penalty</h4>
                        <p class="text-muted small mb-0">Set a deadline and penalty fee for a specific payment type</p>
                    </div>
                    <a href="{{ route('bursary.late-payment-settings.index') }}" class="btn btn-light border shadow-sm btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Back to Penalties
                    </a>
                </div>

                @include('layouts.flash-message')

                <form action="{{ route('bursary.late-payment-settings.store') }}" method="POST">
                    @csrf
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Penalty Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                {{-- Target Payment Type --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Target Payment Type <span class="text-danger">*</span></label>
                                    <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                                        <option value="">Select a fee this penalty applies to</option>
                                        @foreach ($paymentTypes as $type)
                                            @if ($type != 'technical')
                                                <option value="{{ $type }}" {{ old('payment_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1 d-block"><i class="ti ti-info-circle"></i> E.g., Selecting "tuition" means missing this deadline generates a "tuition_late_payment" penalty.</small>
                                </div>

                                {{-- Penalty Amount --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Penalty Amount (₦) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="late_fee_amount" id="late_fee_amount"
                                        class="form-control @error('late_fee_amount') is-invalid @enderror"
                                        value="{{ old('late_fee_amount', '0.00') }}" required>
                                    @error('late_fee_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Closing Date --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Closing Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="closing_date" class="form-control @error('closing_date') is-invalid @enderror" value="{{ old('closing_date') }}" required>
                                    @error('closing_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted mt-1 d-block">After this exact time, the original fee cannot be paid until the penalty above is paid.</small>
                                </div>
                                
                                {{-- Campus --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Campus <span class="text-danger">*</span></label>
                                    <select name="campus_id" class="form-select @error('campus_id') is-invalid @enderror" required>
                                        <option value="">Select Campus</option>
                                        @foreach ($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('campus_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Academic Session --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Session <span class="text-muted small fw-normal">(Optional)</span></label>
                                    <select name="session" class="form-select @error('session') is-invalid @enderror">
                                        <option value="">Applies to All Sessions</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}" {{ old('session', activeSession()->name ?? '') == $session ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('session')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Academic Semester --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-dark">Semester <span class="text-muted small fw-normal">(Optional)</span></label>
                                    <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                                        <option value="">Applies to All Semesters</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->code }}" {{ old('semester') == $semester->code ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Entry Mode --}}
                                <div class="col-md-12">
                                    <label class="form-label fw-medium text-dark">Target Entry Modes <span class="text-muted small fw-normal">(Optional)</span></label>
                                    <select name="entry_mode[]" class="form-select select2 @error('entry_mode') is-invalid @enderror" multiple="multiple">
                                        @foreach ($entryModes as $mode)
                                            <option value="{{ $mode->name }}" {{ in_array($mode->name, old('entry_mode', [])) ? 'selected' : '' }}>
                                                {{ $mode->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-1 d-block">Leave blank to apply to all entry modes.</small>
                                    @error('entry_mode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-5">
                        <a href="{{ route('bursary.late-payment-settings.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-2"></i> Save Penalty Rules
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Select options (leave blank for all)',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush

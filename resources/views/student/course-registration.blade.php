@extends('layouts.app')

@section('title', 'Course Registration')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('student.partials.header')


        @include('student.partials.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-4">
                        <h3 class="page-title mb-1">Course Registration</h3>
                        <p class="mb-1 text-muted">Register your courses for the current academic session.</p>
                    </div>
                    <div class="my-auto mt-3 mt-lg-0">
                        <div class="row g-2">
                            <div class="col-12">
                                <a href="{{ route('students.dashboard') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-home"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="bg-light p-3 rounded shadow-sm">
                            <p class="mb-1"><strong>Current Session:</strong>
                                {{ activeSession()->name ?? 'No active session' }}
                            </p>
                            <p class="mb-2"><strong>Current Semester:</strong>
                                {{ activeSemester()->name ?? 'No active semester' }}</p>
                            
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Semester Units:</small>
                                    <span class="badge {{ $currentSemesterUnits >= $maxSemesterUnits ? 'bg-danger' : 'bg-primary' }}">
                                        {{ $currentSemesterUnits }} / {{ $maxSemesterUnits }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Session Units:</small>
                                    <span class="badge {{ $currentSessionUnits >= $maxSessionUnits ? 'bg-danger' : 'bg-success' }}">
                                        {{ $currentSessionUnits }} / {{ $maxSessionUnits }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Late Payment Banners -->
                @if(isset($closestClosingDate) && now()->lt(\Carbon\Carbon::parse($closestClosingDate)))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-warning-subtle border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-md bg-warning text-white rounded p-2">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </span>
                                        <div>
                                            <h5 class="text-warning-dark mb-1">Upcoming Late Penalty</h5>
                                            <p class="mb-0">Pay your outstanding fees soon to avoid a late payment penalty.</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="text-warning-dark mb-1">Penalty of ₦{{ number_format($closestClosingAmount, 2) }} applies in:</h6>
                                        <h5 class="text-warning-dark fw-bold mb-0" id="creg-closing-countdown">Loading...</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($closestIncrementDate) && now()->lt(\Carbon\Carbon::parse($closestIncrementDate)))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-danger-subtle border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-md bg-danger text-white rounded p-2">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </span>
                                        <div>
                                            <h5 class="text-danger mb-1">Penalty Escalation Warning</h5>
                                            <p class="mb-0">Your late penalty will increase soon. Please clear your late fee immediately.</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="text-danger mb-1">Fee increases to ₦{{ number_format($closestIncrementAmount, 2) }} in:</h6>
                                        <h5 class="text-danger fw-bold mb-0" id="creg-increment-countdown">Loading...</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(isset($courseRegistrationSetting))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-primary-subtle border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h5 class="text-primary mb-1">Course Registration Deadline</h5>
                                        <p class="mb-0">Please register before the deadline to avoid a late fee of ₦{{ number_format($courseRegistrationSetting->late_registration_fee, 2) }}.</p>
                                    </div>
                                    <div class="text-end">
                                        @if(now()->gt($courseRegistrationSetting->closing_date))
                                            <h4 class="text-danger mb-0">Closed</h4>
                                            @if(!$hasPaidLateFee)
                                                <small class="text-danger d-block">Late registration fee applicable</small>
                                            @else
                                                <small class="text-success d-block">Late fee paid</small>
                                            @endif
                                        @else
                                            <h4 class="text-primary mb-0" id="course-countdown">
                                                Loading...
                                            </h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!now()->gt($courseRegistrationSetting->closing_date))
                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const deadline = new Date("{{ \Carbon\Carbon::parse($courseRegistrationSetting->closing_date)->toIso8601String() }}").getTime();
                            const countdownEl = document.getElementById('course-countdown');
                            setInterval(() => {
                                const now = new Date().getTime();
                                const distance = deadline - now;
                                if (distance < 0) {
                                    countdownEl.innerHTML = "Closed";
                                    countdownEl.classList.remove('text-primary');
                                    countdownEl.classList.add('text-danger');
                                    return;
                                }
                                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                countdownEl.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                            }, 1000);
                        });
                    </script>
                @endpush
                @endif
                @endif

                @include('layouts.flash-message')

                @if (!$payment_status['allCleared'] && (isset($payment_status['status']['tuition']) && $payment_status['status']['tuition']['percentage_paid'] >= 60 && strtolower($currentSemester ?? '') === '1st'))
                    {{-- Tuition ≥ 60% and it’s first semester --}}
                    @include('student.partials.filter-form')
                    @include('student.partials.available-courses')

                @elseif (!$payment_status['allCleared'])

                    @include('student.partials.payment-warning')

                @else
                    {{-- All cleared --}}
                    @include('student.partials.filter-form')
                    @include('student.partials.available-courses')
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($closestClosingDate) && now()->lt(\Carbon\Carbon::parse($closestClosingDate)))
                const cregClosingDeadline = new Date("{{ \Carbon\Carbon::parse($closestClosingDate)->toIso8601String() }}").getTime();
                const cregClosingEl = document.getElementById('creg-closing-countdown');
                setInterval(() => {
                    const distance = cregClosingDeadline - new Date().getTime();
                    if (distance < 0) {
                        if (cregClosingEl) cregClosingEl.innerHTML = "Penalty Active";
                        return;
                    }
                    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);
                    if (cregClosingEl) cregClosingEl.innerHTML = `${d}d ${h}h ${m}m ${s}s`;
                }, 1000);
            @endif

            @if(isset($closestIncrementDate) && now()->lt(\Carbon\Carbon::parse($closestIncrementDate)))
                const cregIncDeadline = new Date("{{ \Carbon\Carbon::parse($closestIncrementDate)->toIso8601String() }}").getTime();
                const cregIncEl = document.getElementById('creg-increment-countdown');
                setInterval(() => {
                    const distance = cregIncDeadline - new Date().getTime();
                    if (distance < 0) {
                        if (cregIncEl) cregIncEl.innerHTML = "Fee Increased";
                        return;
                    }
                    const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);
                    if (cregIncEl) cregIncEl.innerHTML = `${d}d ${h}h ${m}m ${s}s`;
                }, 1000);
            @endif
        });
    </script>
@endpush
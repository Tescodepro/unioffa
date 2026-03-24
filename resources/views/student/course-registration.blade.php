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
@endpush
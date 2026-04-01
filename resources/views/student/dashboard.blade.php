@extends('layouts.app')

@section('title', 'Student Dashboard')

@push('styles')
    <!-- Add any additional stylesheets or CSS files here -->
@endpush

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        @include('student.partials.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('student.partials.sidebar')
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Student Dashboard</h3>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
                    <div class="col-xxl-12 d-flex">
                        <div class="row flex-fill">

                            <!-- Profile Card -->

                            @include('student.partials.profile-card')
                            <!-- /Profile Card -->

                            @if(isset($courseRegistrationSetting))
                                <div class="col-xl-12 d-flex mb-4">
                                    <div class="card bg-primary-subtle border-0 flex-fill">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                <div>
                                                    <h5 class="text-primary mb-1">Course Registration Deadline</h5>
                                                    <p class="mb-0">Please register before the deadline to avoid a late fee of
                                                        ₦{{ number_format($courseRegistrationSetting->late_registration_fee, 2) }}.
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    @if(now()->gt($courseRegistrationSetting->closing_date))
                                                        <h4 class="text-danger mb-0">Closed</h4>
                                                        <small class="text-danger">Late registration fee applicable</small>
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
                                @if(!now()->gt($courseRegistrationSetting->closing_date))
                                    @push('scripts')
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
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

                            @if(isset($closestClosingDate) && now()->lt(\Carbon\Carbon::parse($closestClosingDate)))
                                <div class="col-xl-12 d-flex mb-4">
                                    <div class="card bg-warning-subtle border-0 flex-fill">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="avatar avatar-md bg-warning text-white rounded">
                                                        <i class="ti ti-clock-exclamation fs-16"></i>
                                                    </span>
                                                    <div>
                                                        <h5 class="text-warning-dark mb-1">Upcoming Late Penalty</h5>
                                                        <p class="mb-0">Pay your outstanding fees soon to avoid a late payment penalty.</p>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h6 class="text-warning-dark mb-1">Penalty of ₦{{ number_format($closestClosingAmount, 2) }} applies in:</h6>
                                                    <h5 class="text-warning-dark fw-bold mb-2" id="penalty-closing-countdown">Loading...</h5>
                                                    <a href="{{ route('students.load_payment') }}" class="btn btn-warning">Pay Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(isset($hasLatePenalty) && $hasLatePenalty)
                                <div class="col-xl-12 d-flex mb-4">
                                    <div class="card bg-danger-subtle border-0 flex-fill">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="avatar avatar-md bg-danger text-white rounded">
                                                        <i class="ti ti-alert-triangle fs-16"></i>
                                                    </span>
                                                    <div>
                                                        <h5 class="text-danger mb-1">Late Payment Penalty</h5>
                                                        <p class="mb-0">Please pay your late payment penalty to restore standard payment features.</p>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if(isset($closestIncrementDate) && now()->lt(\Carbon\Carbon::parse($closestIncrementDate)))
                                                        <h6 class="text-danger mb-1">Fee increases to ₦{{ number_format($closestIncrementAmount, 2) }} in:</h6>
                                                        <h5 class="text-danger fw-bold mb-2" id="penalty-increment-countdown">Loading...</h5>
                                                    @endif
                                                    <a href="{{ route('students.load_payment') }}" class="btn btn-danger">Pay Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Quick Actions -->

                            <div class="col-xl-12 d-flex">
                                <div class="row flex-fill">
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.course.registration') }}"
                                            class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i
                                                            class="ti ti-hexagonal-prism-plus fs-16"></i></span>
                                                    <h6>Course Registration</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.load_payment') }}"
                                            class="card border-0 border-bottom border-primary border-2 flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i
                                                            class="ti ti-report-money fs-16"></i></span>
                                                    <h6>School Fees</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.results') }}"
                                            class="card border-0 border-bottom border-warning flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-warning me-2"><i
                                                            class="ti ti-file-text fs-16"></i></span>
                                                    <h6>Check Results</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.transcript') }}"
                                            class="card border-0 border-bottom border-primary flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i
                                                            class="ti ti-file-stack fs-16"></i></span>
                                                    <h6>Request Transcript</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.payment.history') }}"
                                            class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i
                                                            class="ti ti-history fs-16"></i></span>
                                                    <h6>Payment History</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href=""
                                            class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i
                                                            class="ti ti-building fs-16"></i></span>
                                                    <h6 class="mb-0">Apply for Hostel</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Quick Actions -->

                            <!-- Profile Management -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Profile Management</h4>
                                        <a href="" class="fw-medium">View All</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4">
                                                <a href="{{ route('students.profile') }}"
                                                    class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i
                                                            class="ti ti-user fs-16"></i></span>
                                                    <h6 class="mb-0">View Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="{{ route('students.profile') }}"
                                                    class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i
                                                            class="ti ti-edit fs-16"></i></span>
                                                    <h6 class="mb-0">Edit Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="{{ route('students.profile') }}"
                                                    class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i
                                                            class="ti ti-lock fs-16"></i></span>
                                                    <h6 class="mb-0">Change Password</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Profile Management -->

                            <!-- Logout -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Account</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <a href="{{ route('students.logout') }}"
                                                    class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-danger me-2"><i
                                                            class="ti ti-logout fs-16"></i></span>
                                                    <h6 class="mb-0">Logout</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Logout -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->

    @if(isset($hasLatePenalty) && $hasLatePenalty)
        <!-- Late Penalty Modal -->
        <div class="modal fade" id="latePenaltyModal" tabindex="-1" aria-labelledby="latePenaltyModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title" id="latePenaltyModalLabel"><i class="ti ti-alert-circle me-2"></i> Action Required: Late Payment Penalty</h5>
                    </div>
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <span class="avatar avatar-xl bg-danger-transparent rounded-circle">
                                <i class="ti ti-alert-triangle text-danger fs-24"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Outstanding Penalty Detected</h5>
                        <p class="mb-0 text-muted">You have an active late payment penalty that requires immediate attention. You must clear this penalty to restore full access to your account and continue with standard transactions.</p>
                        @if(isset($closestIncrementDate) && now()->lt(\Carbon\Carbon::parse($closestIncrementDate)))
                            <div class="mt-3 p-3 bg-white rounded border border-danger-transparent text-start shadow-sm">
                                <h6 class="text-danger mb-1"><i class="ti ti-clock-stop me-1"></i> Time Before Fee Increase</h6>
                                <p class="small mb-1 text-muted">The penalty will increase to <strong>₦{{ number_format($closestIncrementAmount, 2) }}</strong>.</p>
                                <h4 class="text-danger fw-bold mb-0" id="modal-penalty-increment-countdown">Loading timer...</h4>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                        <a href="{{ route('students.load_payment') }}" class="btn btn-danger w-100">Pay Penalty Now</a>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var latePenaltyModal = new bootstrap.Modal(document.getElementById('latePenaltyModal'));
                    latePenaltyModal.show();
                        @if(isset($closestIncrementDate) && now()->lt(\Carbon\Carbon::parse($closestIncrementDate)))
                            const incDeadline = new Date("{{ \Carbon\Carbon::parse($closestIncrementDate)->toIso8601String() }}").getTime();
                            const incEls = [
                                document.getElementById('penalty-increment-countdown'),
                                document.getElementById('modal-penalty-increment-countdown')
                            ];
                            
                            setInterval(() => {
                                const now = new Date().getTime();
                                const distance = incDeadline - now;
                                
                                if (distance < 0) {
                                    incEls.forEach(el => {
                                        if (el) el.innerHTML = "Fee Increased";
                                    });
                                    return;
                                }
                                
                                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                const timeStr = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                                
                                incEls.forEach(el => {
                                    if (el) el.innerHTML = timeStr;
                                });
                            }, 1000);
                        @endif
                    });
                </script>
            @endpush
    @endif

@endsection

@push('scripts')
    <!-- Build Tier 1 Penalty Target Timer -->
    @if(isset($closestClosingDate) && now()->lt(\Carbon\Carbon::parse($closestClosingDate)))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const closingDeadline = new Date("{{ \Carbon\Carbon::parse($closestClosingDate)->toIso8601String() }}").getTime();
                const closingEl = document.getElementById('penalty-closing-countdown');
                
                setInterval(() => {
                    const now = new Date().getTime();
                    const distance = closingDeadline - now;
                    
                    if (distance < 0) {
                        if (closingEl) closingEl.innerHTML = "Penalty Active";
                        return;
                    }
                    
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    const timeStr = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    
                    if (closingEl) closingEl.innerHTML = timeStr;
                }, 1000);
            });
        </script>
    @endif
@endpush
@extends('layouts.app')

@section('title', 'Summer Registration')

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
                        <h3 class="page-title mb-1">Summer Registration</h3>
                        <p class="mb-1 text-muted">Register for summer courses. The summer application fee is ₦30,000, and each course costs ₦20,000.</p>
                    </div>
                    <div class="my-auto mt-3 mt-lg-0">
                        <div class="row g-2">
                            <div class="col-12 text-end">
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
                            <p class="mb-0"><strong>Current Semester:</strong>
                                Summer
                            </p>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                @include('layouts.flash-message')

                @if($summerRegistration && in_array($summerRegistration->status, ['pending_vc_approval', 'pending_payment', 'registered']))
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Current Registration Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <p class="text-muted mb-1">Status</p>
                                    <h5 class="fw-bold">
                                        @if($summerRegistration->status == 'pending_vc_approval')
                                            <span class="text-warning">Pending VC Approval</span>
                                        @elseif($summerRegistration->status == 'pending_payment')
                                            <span class="text-primary">Pending Payment</span>
                                        @elseif($summerRegistration->status == 'registered')
                                            <span class="text-success">Registered</span>
                                        @endif
                                    </h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-1">Total Courses</p>
                                    <h5 class="fw-bold">{{ is_array($summerRegistration->courses) ? count($summerRegistration->courses) : 0 }}</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-1">Total Fee</p>
                                    <h5 class="fw-bold">₦{{ number_format($summerRegistration->total_fee, 2) }}</h5>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-1">Payment Status</p>
                                    <h5 class="fw-bold text-capitalize {{ $summerRegistration->payment_status == 'paid' ? 'text-success' : 'text-danger' }}">
                                        {{ $summerRegistration->payment_status }}
                                    </h5>
                                </div>
                            </div>

                            @if($summerRegistration->status == 'pending_payment' && $summerRegistration->payment_status == 'pending')
                                <div class="mt-4">
                                    <a href="{{ route('student.summer.payment', $summerRegistration->id) }}" class="btn btn-primary">
                                        Proceed to Payment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($summerRegistration->status == 'registered' && !empty($registeredCourses))
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Registered Summer Courses</h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Course Code</th>
                                                <th>Title</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($registeredCourses as $reg)
                                                <tr>
                                                    <td>{{ $reg->course_code }}</td>
                                                    <td>{{ $reg->course_title }}</td>
                                                    <td>{{ $reg->course_unit }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                @else

                    <div class="card shadow-sm">
                        <form action="{{ route('student.summer.store') }}" method="POST" id="summerRegistrationForm">
                            @csrf
                            <div class="card-header">
                                <h4 class="card-title mb-0">Select Courses</h4>
                            </div>
                            
                            <div class="card-body p-0">
                                <div class="alert alert-info border-0 rounded-0 mb-0">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Note:</strong> You can select up to 6 courses. If you select more than 6, your request will be sent to the Vice Chancellor for approval before you can make payment.
                                </div>

                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                            <tr>
                                                <th style="width: 50px;">Select</th>
                                                <th>Course Code</th>
                                                <th>Course Title</th>
                                                <th>Units</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courses as $course)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" class="form-check-input course-checkbox">
                                                        </div>
                                                    </td>
                                                    <td class="fw-medium">{{ $course->course_code }}</td>
                                                    <td class="text-muted">{{ $course->course_title }}</td>
                                                    <td class="text-muted">{{ $course->course_unit }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer bg-white">
                                <div class="mb-3 d-none" id="reasonContainer">
                                    <label for="reason_for_increase" class="form-label fw-bold">
                                        Reason for more than 6 courses <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="reason_for_increase" id="reason_for_increase" rows="3" class="form-control" placeholder="Please provide a reason for requesting more than 6 courses..."></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <p class="mb-1 text-muted">Selected Courses: <strong id="courseCount" class="text-dark">0</strong></p>
                                        <p class="mb-0 text-muted">Estimated Total: <strong id="estimatedTotal" class="text-dark fs-5">₦30,000.00</strong></p>
                                    </div>
                                    <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>
                                        Proceed
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const courseCountSpan = document.getElementById('courseCount');
            const estimatedTotalSpan = document.getElementById('estimatedTotal');
            const reasonContainer = document.getElementById('reasonContainer');
            const reasonTextarea = document.getElementById('reason_for_increase');
            const submitBtn = document.getElementById('submitBtn');

            const summaryFee = 30000;
            const perCourseFee = 20000;

            function updateSummary() {
                let count = 0;
                checkboxes.forEach(cb => {
                    if(cb.checked) count++;
                });

                courseCountSpan.textContent = count;
                
                if(count > 0) {
                    const total = summaryFee + (count * perCourseFee);
                    estimatedTotalSpan.textContent = '₦' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
                    submitBtn.disabled = false;
                } else {
                    estimatedTotalSpan.textContent = '₦' + summaryFee.toLocaleString('en-US', {minimumFractionDigits: 2});
                    submitBtn.disabled = true;
                }

                if(count > 6) {
                    reasonContainer.classList.remove('d-none');
                    reasonTextarea.required = true;
                    submitBtn.textContent = 'Submit for VC Approval';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-warning');
                } else {
                    reasonContainer.classList.add('d-none');
                    reasonTextarea.required = false;
                    submitBtn.textContent = 'Proceed to Payment';
                    submitBtn.classList.remove('btn-warning');
                    submitBtn.classList.add('btn-primary');
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });
            
            updateSummary();
        });
    </script>
@endpush

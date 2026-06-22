@extends('layouts.app')

@section('title', 'Summer Registration Payment')

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
                        <h3 class="page-title mb-1">Summer Registration Payment</h3>
                        <p class="mb-1 text-muted">Complete your payment to finalize summer registration.</p>
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

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card shadow-sm">
                            <div class="card-body p-4 p-md-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                                    <h4 class="fw-bold">Payment Details</h4>
                                </div>
                                
                                <div class="bg-light p-4 rounded mb-4">
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Base Registration Fee:</span>
                                        <span class="fw-medium">₦30,000.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Course Registration Fee ({{ is_array($summerRegistration->courses) ? count($summerRegistration->courses) : 0 }} courses):</span>
                                        <span class="fw-medium">₦{{ number_format((is_array($summerRegistration->courses) ? count($summerRegistration->courses) : 0) * 20000, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 mt-2">
                                        <span class="h5 fw-bold mb-0">Total Amount:</span>
                                        <span class="h5 fw-bold text-primary mb-0">₦{{ number_format($summerRegistration->total_fee, 2) }}</span>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <form action="{{ route('student.summer.payment.simulate', $summerRegistration->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm mb-3">
                                            <i class="fas fa-lock me-2"></i> Pay ₦{{ number_format($summerRegistration->total_fee, 2) }} Now
                                        </button>
                                        <p class="text-muted small">
                                            <i class="fas fa-info-circle"></i> For demonstration purposes, this will simulate a successful payment and register you for the selected courses.
                                        </p>
                                    </form>
                                </div>
                                
                                <div class="mt-4 text-center">
                                    <a href="{{ route('student.summer.index') }}" class="text-decoration-none fw-medium">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Registration
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

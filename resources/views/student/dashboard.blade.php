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

                            <!-- Quick Actions -->    
                            <div class="col-xl-12 d-flex">
                                <div class="row flex-fill">
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.course.registration') }}" class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-hexagonal-prism-plus fs-16"></i></span>
                                                    <h6>Course Registration</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.load_payment') }}" class="card border-0 border-bottom border-primary border-2 flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-report-money fs-16"></i></span>
                                                    <h6>School Fees</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="" class="card border-0 border-bottom border-warning flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-warning me-2"><i class="ti ti-file-text fs-16"></i></span>
                                                    <h6>Check Results</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>                    
                                    
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="" class="card border-0 border-bottom border-primary flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-file-stack fs-16"></i></span>
                                                    <h6>Request Transcript</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="{{ route('students.payment.history') }}" class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-history fs-16"></i></span>
                                                    <h6>Payment History</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-sm-12 col-xl-4 d-flex">
                                        <a href="" class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-building fs-16"></i></span>
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
                                                <a href="{{ route('students.profile') }}" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-user fs-16"></i></span>
                                                    <h6 class="mb-0">View Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="{{ route('students.profile') }}" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-edit fs-16"></i></span>
                                                    <h6 class="mb-0">Edit Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="{{ route('students.profile') }}" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-lock fs-16"></i></span>
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
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-danger me-2"><i class="ti ti-logout fs-16"></i></span>
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
@endsection

@push('scripts')
<!-- Add any additional JavaScript files here -->
@endpush
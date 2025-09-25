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
                            <div class="col-xl-12 d-flex">
                                <div class="flex-fill">
                                    <div class="card bg-dark position-relative">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center row-gap-3 mb-3">
                                                
                                                <!-- Avatar -->
                                                <div class="avatar avatar-xxl rounded flex-shrink-0 me-3">
                                                    @php
                                                        $passport = $user->profile_picture && file_exists(public_path($user->profile_picture))
                                                            ? asset($user->profile_picture)
                                                            : asset('portal_assets/img/users/placeholder.jpeg');
                                                    @endphp
                                                    <img src="{{ $passport }}" alt="Passport">
                                                </div>

                                                <!-- User Info -->
                                                <div class="d-block">
                                                    <span class="badge bg-transparent-primary text-primary mb-1">
                                                        {{ $user->username }}
                                                    </span>
                                                    <h3 class="text-truncate text-white mb-1">
                                                        {{ $user->full_name }}
                                                    </h3>

                                                    <div class="d-flex align-items-center flex-wrap row-gap-2 text-gray-2">
                                                        <span class="border-end me-2 pe-2">
                                                            Faculty: {{ $user->student->department->faculty->faculty_name ?? 'N/A' }}
                                                        </span>
                                                        <span class="border-end me-2 pe-2">
                                                            Department: {{ $user->student->department->department_name ?? 'N/A' }}
                                                        </span>
                                                        <span class="border-end me-2 pe-2">
                                                            Level: {{ $user->student->level ?? 'N/A' }}
                                                        </span>
                                                        <span class="border-end me-2 pe-2">
                                                            Programme: {{ $user->student->programme ?? 'N/A' }}
                                                        </span>
                                                        <span class="me-2 pe-2">
                                                            Gender: {{ $user->student->sex ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                    <h6>Pay Fees</h6>
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
                                        <a href="" class="card border-0 border-bottom border-info flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-info me-2"><i class="ti ti-calendar fs-16"></i></span>
                                                    <h6>Academic Calendar</h6>
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
                                        <a href="" class="card border-0 border-bottom border-success flex-fill animate-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-history fs-16"></i></span>
                                                    <h6>Payment History</h6>
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
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-user fs-16"></i></span>
                                                    <h6 class="mb-0">View Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-edit fs-16"></i></span>
                                                    <h6 class="mb-0">Edit Profile</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-lock fs-16"></i></span>
                                                    <h6 class="mb-0">Change Password</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Profile Management -->

                            <!-- Hostel Services -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Hostel Services</h4>
                                        <a href="" class="fw-medium">View All</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-building fs-16"></i></span>
                                                    <h6 class="mb-0">Apply for Hostel</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-success me-2"><i class="ti ti-info-circle fs-16"></i></span>
                                                    <h6 class="mb-0">Hostel Status</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Hostel Services -->

                            <!-- Library Services -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Library Services</h4>
                                        <a href="" class="fw-medium">View All</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-info me-2"><i class="ti ti-books fs-16"></i></span>
                                                    <h6 class="mb-0">Library Access</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-info me-2"><i class="ti ti-bookmark fs-16"></i></span>
                                                    <h6 class="mb-0">Book Reservations</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Library Services -->

                            <!-- Support Services -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Support Services</h4>
                                        <a href="" class="fw-medium">View All</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-warning me-2"><i class="ti ti-alert-circle fs-16"></i></span>
                                                    <h6 class="mb-0">Submit Complaint</h6>
                                                </a>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="" class="d-flex align-items-center mb-2">
                                                    <span class="avatar avatar-md rounded bg-warning me-2"><i class="ti ti-headset fs-16"></i></span>
                                                    <h6 class="mb-0">Contact Support</h6>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Support Services -->

                            <!-- Notice Board -->
                            <div class="col-xl-12 d-flex">
                                <div class="card flex-fill">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Notice Board</h4>
                                        <a href="" class="fw-medium">View All</a>
                                    </div>
                                    <div class="card-body notice-widget">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-primary-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-books fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">New Syllabus Instructions</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 11 Mar 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-success-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-note fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">World Environment Day Program</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 21 Apr 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-danger-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-bell-check fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">Exam Preparation Notification</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 13 Mar 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-skyblue-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-notes fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">Online Classes Preparation</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 24 May 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-warning-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-package fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">Exam Time Table Release</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 24 May 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-0">
                                            <div class="d-flex align-items-center overflow-hidden me-2">
                                                <span class="bg-danger-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
                                                    <i class="ti ti-bell-check fs-16"></i>
                                                </span>
                                                <div class="overflow-hidden">
                                                    <h6 class="text-truncate mb-1">English Exam Preparation</h6>
                                                    <p><i class="ti ti-calendar me-2"></i>Added on: 23 Mar 2024</p>
                                                </div>
                                            </div>
                                            <a href=""><i class="ti ti-chevron-right fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Notice Board -->

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
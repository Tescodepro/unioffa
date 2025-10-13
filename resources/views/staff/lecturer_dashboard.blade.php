@extends('layouts.app')

@section('title', 'Administrator Dashboard')

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
    @include('staff.layouts.header')
    <!-- /Header -->

    <!-- Sidebar -->
    @include('staff.layouts.sidebar')
    <!-- /Sidebar -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-2">
                    <h3 class="page-title mb-1">Lecturer Dashboard</h3>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <!-- Applicant Stats by Campus -->
                @foreach($campusApplicants as $campus)
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card border-0 border-bottom border-primary flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-primary me-2">
                                        <i class="ti ti-users fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">{{ $campus->name }}</h6>
                                        <p class="mb-0">{{ $campus->applicants_count }} Applicants</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->

</div>
<!-- /Main Wrapper -->
@endsection

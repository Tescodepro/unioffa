@extends('layouts.app')

@section('title', 'Student Login')

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
		@include('layouts.header')
		<!-- /Header -->

		<!-- Sidebar -->
		@include('layouts.sidebar')
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
  

							<!-- Fees -->    
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
										<a href="student-fees.html" class="card border-0 border-bottom border-primary border-2 flex-fill animate-card">
											<div class="card-body">
												<div class="d-flex align-items-center">
													<span class="avatar avatar-md rounded bg-primary me-2"><i class="ti ti-report-money fs-16"></i></span>
													<h6>Pay Fees</h6>
												</div>
											</div>
										</a>
									</div>
									
									<div class="col-sm-12 col-xl-4 d-flex">
										<a href="student-time-table.html" class="card border-0 border-bottom border-warning flex-fill animate-card">
											<div class="card-body">
												<div class="d-flex align-items-center">
													<span class="avatar avatar-md rounded bg-warning me-2"><i class="ti ti-calendar fs-16"></i></span>
													<h6>Check Result</h6>
												</div>
											</div>
										</a>
									</div>										
								</div>	
							</div>
							<!-- /Fees -->    

						</div>
					</div>  

				</div>

				<div class="row">
					<!-- Notice Board -->
					{{-- <div class="col-xxl-12 col-xl-12 d-flex">
						<div class="card flex-fill">
							<div class="card-header  d-flex align-items-center justify-content-between">
								<h4 class="card-title">Notice Board</h4>
								<a href="notice-board.html" class="fw-medium">View All</a>
							</div>
							<div class="card-body">
								<div class="notice-widget">
									<div class="d-flex align-items-center justify-content-between mb-4">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-primary-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-books fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">New Syllabus Instructions</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 11 Mar 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
									<div class="d-flex align-items-center justify-content-between mb-4">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-success-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-note fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">World Environment Day Program.....!!!</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 21 Apr 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
									<div class="d-flex align-items-center justify-content-between mb-4">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-danger-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-bell-check fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">Exam Preparation Notification!</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 13 Mar 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
									<div class="d-flex align-items-center justify-content-between mb-4">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-skyblue-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-notes fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">Online Classes Preparation</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 24 May 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
									<div class="d-flex align-items-center justify-content-between mb-4">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-warning-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-package fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">Exam Time Table Release</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 24 May 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
									<div class="d-flex align-items-center justify-content-between mb-0">
										<div class="d-flex align-items-center overflow-hidden me-2">
											<span class="bg-danger-transparent avatar avatar-md me-2 rounded-circle flex-shrink-0">
												<i class="ti ti-bell-check fs-16"></i>
											</span>
											<div class="overflow-hidden">
												<h6 class="text-truncate mb-1">English Exam Preparation</h6>
												<p><i class="ti ti-calendar me-2"></i>Added on : 23 Mar 2024</p>
											</div>
										</div>
										<a href="notice-board.html"><i class="ti ti-chevron-right fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>

					</div> --}}
					<!-- /Notice Board -->
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

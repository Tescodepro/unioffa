@extends('layouts.app')

@section('title', 'Staff Login')

@push('styles')
<!-- Add any additional stylesheets or CSS files here -->
@endpush

@section('content')
	<div class="main-wrapper">
		<div class="container-fuild">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
				<div class="row">
					<!-- Left side: Staff Announcements -->
					<div class="col-lg-6">
						<div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-lg-block d-none flex-wrap vh-100 overflowy-auto">
							<div>
								<img src="{{asset('portal_assets/img/authentication/slider-2.jpg')}}" alt="Img">
							</div>
							<div class="authen-overlay-item w-100 p-4">
								<h4 class="text-white mb-3">Staff Portal Updates</h4>

								<div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
									<div>
										<h6>Faculty Meeting</h6>
										<p class="mb-0 text-truncate">Monthly faculty meeting scheduled for Sept 25th, 2025...</p>
									</div>
									<a href="javascript:void(0);"><i class="ti ti-chevrons-right"></i></a>
								</div>

								<div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
									<div>
										<h6>Grade Submission Deadline</h6>
										<p class="mb-0 text-truncate">Submit grades for Summer 2025 courses before Sept 30th...</p>
									</div>
									<a href="javascript:void(0);"><i class="ti ti-chevrons-right"></i></a>
								</div>

								<div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
									<div>
										<h6>Staff Training Workshop</h6>
										<p class="mb-0 text-truncate">Register for the upcoming professional development workshop...</p>
									</div>
									<a href="javascript:void(0);"><i class="ti ti-chevrons-right"></i></a>
								</div>

								<div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
									<div>
										<h6>Payroll Update</h6>
										<p class="mb-0 text-truncate">New payroll system will go live from October 2025...</p>
									</div>
									<a href="javascript:void(0);"><i class="ti ti-chevrons-right"></i></a>
								</div>

								<div class="d-flex align-items-center flex-row mb-0 justify-content-between p-3 br-5 gap-3 card">
									<div>
										<h6>Annual Staff Retreat</h6>
										<p class="mb-0 text-truncate">Confirm your participation for the retreat by Oct 10th...</p>
									</div>
									<a href="javascript:void(0);"><i class="ti ti-chevrons-right"></i></a>
								</div>
							</div>
						</div>
					</div>

					<!-- Right side: Staff Login Form -->
					<div class="col-lg-6 col-md-12 col-sm-12">
						<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
							<div class="col-md-8 mx-auto p-4">
								<form action="{{ route('staff.login') }}" method="POST">
									@csrf
									<div>
										<div class="mx-auto mb-5 text-center">
											<img src="{{ asset('portal_assets/img/logo/logo.svg') }}"
												class="img-fluid"
												alt="Logo"
												style="height:70px;">
											<h2> Login to your Staff Portal </h2>
										</div>

										@include('layouts.flash-message')

										<div class="card">
											<div class="card-body p-4">
												<div class="mb-4">
													<h2 class="mb-2">Welcome</h2>
													<p class="mb-0">Please enter your staff credentials to sign in</p>
												</div>

												<!-- Username -->
												<div class="mb-3">
													<label class="form-label">Email/Staff ID</label>
													<div class="input-icon mb-3 position-relative">
														<span class="input-icon-addon">
															<i class="ti ti-mail"></i>
														</span>
														<input type="email"
															name="email"
															class="form-control"
															value="{{ old('email') }}">
													</div>

													<!-- Password -->
													<label class="form-label">Password</label>
													<div class="pass-group">
														<input type="password"
															name="password"
															id="password-field"
															class="pass-input form-control"
															value="{{ old('password') }}">
														<span class="ti toggle-password ti-eye-off"></span>
													</div>
												</div>

												<!-- Forgot Password -->
												<div class="form-wrap form-wrap-checkbox mb-3">
													<div class="text-end">
														{{-- <a href="forgot-password.html" class="link-danger">Forgot Password?</a> --}}
													</div>
												</div>

												<!-- Submit -->
												<div class="mb-3">
													<button type="submit" class="btn btn-primary w-100">Sign In</button>
												</div>
											</div>
										</div>

										<!-- Footer -->
										<div class="mt-5 text-center">
											<p class="mb-0">Copyright &copy; {{ date('Y') }} - {{ config('app.name') }}</p>
										</div>
									</div>
								</form>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>

	</div>
@endsection

@push('scripts')
<!-- Add any additional JavaScript files here -->
@endpush

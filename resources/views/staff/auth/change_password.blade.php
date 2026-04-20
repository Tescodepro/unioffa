@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
	<div class="main-wrapper">
		<div class="container-fuild">
			<div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
				<div class="row">
					<!-- Left side: Welcome Message -->
					<div class="col-lg-6">
						<div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-lg-block d-none flex-wrap vh-100 overflowy-auto">
							<div>
								<img src="{{asset('portal_assets/img/authentication/slider-1.jpg')}}" alt="Img">
							</div>
							<div class="authen-overlay-item w-100 p-4">
								<h4 class="text-white mb-3">Welcome to {{ config('app.name') }} Staff Portal</h4>
                                <p class="text-white">For security reasons, we require all staff members to change their default password upon their initial login. This helps ensure that your account is protected and your credentials remain private.</p>
                                
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white rounded-circle p-2 me-3">
                                            <i class="ti ti-shield-check text-primary"></i>
                                        </div>
                                        <p class="text-white mb-0">Enhanced Account Security</p>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white rounded-circle p-2 me-3">
                                            <i class="ti ti-lock text-primary"></i>
                                        </div>
                                        <p class="text-white mb-0">Confidential Access</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white rounded-circle p-2 me-3">
                                            <i class="ti ti-user-check text-primary"></i>
                                        </div>
                                        <p class="text-white mb-0">Verified Identification</p>
                                    </div>
                                </div>
							</div>
						</div>
					</div>

					<!-- Right side: Change Password Form -->
					<div class="col-lg-6 col-md-12 col-sm-12">
						<div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
							<div class="col-md-8 mx-auto p-4">
								<form action="{{ route('staff.password.update') }}" method="POST">
									@csrf
									<div>
										<div class="mx-auto mb-5 text-center">
											<img src="{{ asset('portal_assets/img/logo/logo.svg') }}"
												class="img-fluid"
												alt="Logo"
												style="height:70px;">
											<h2> Secure Your Portal </h2>
										</div>

										@include('layouts.flash-message')

										<div class="card">
											<div class="card-body p-4">
												<div class="mb-4 text-center">
													<h2 class="mb-2">Change Password</h2>
													<p class="mb-0 text-muted">Set a strong password for your staff account</p>
												</div>

                                                @if ($errors->any())
                                                    <div class="alert alert-danger">
                                                        <ul class="mb-0">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

												<!-- New Password -->
												<div class="mb-3">
													<label class="form-label">New Password</label>
													<div class="pass-group mb-3">
														<input type="password"
															name="password"
															id="password-field"
															class="pass-input form-control"
                                                            placeholder="Enter new password"
															required>
														<span class="ti toggle-password ti-eye-off"></span>
													</div>

													<!-- Confirm Password -->
													<label class="form-label">Confirm New Password</label>
													<div class="pass-group">
														<input type="password"
															name="password_confirmation"
															id="password-field-confirm"
															class="pass-input form-control"
                                                            placeholder="Confirm new password"
															required>
														<span class="ti toggle-password ti-eye-off"></span>
													</div>
												</div>

                                                <div class="mb-4">
                                                    <small class="text-muted">
                                                        <i class="ti ti-info-circle me-1"></i>
                                                        Password must be at least 8 characters long.
                                                    </small>
                                                </div>

												<!-- Submit -->
												<div class="mb-3">
													<button type="submit" class="btn btn-primary w-100 py-2">Update Password & Login</button>
												</div>
											</div>
										</div>

										<!-- Footer -->
										<div class="mt-5 text-center">
											<p class="mb-0 text-muted">Copyright &copy; {{ date('Y') }} - {{ config('app.name') }}</p>
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

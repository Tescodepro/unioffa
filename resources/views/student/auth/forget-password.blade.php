@extends('layouts.app')

@section('title', 'Forgot Password')

@push('styles')
    <!-- Add any additional stylesheets or CSS files here -->
@endpush

@section('content')
<div class="main-wrapper">
    <div class="container-fuild">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row">
                <!-- Left Section: Instructions -->
                <div class="col-lg-6">
                    <div class="login-background position-relative d-lg-flex align-items-center justify-content-center d-lg-block d-none flex-wrap vh-100 overflowy-auto">
                        <div>
                            <img src="{{ asset('portal_assets/img/authentication/slider-2.jpg') }}" alt="Img">
                        </div>

                        <div class="authen-overlay-item w-100 p-4">
                            <h4 class="text-white mb-3">Password Reset Instructions</h4>

                            <div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
                                <div>
                                    <h6>Step 1: Enter Your Matric Number</h6>
                                    <p class="mb-0 text-truncate">Provide your matric number exactly as registered in the portal.</p>
                                </div>
                                <a href="javascript:void(0);"><i class="ti ti-user"></i></a>
                            </div>

                            <div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
                                <div>
                                    <h6>Step 2: Verify Your Matric Number</h6>
                                    <p class="mb-0 text-truncate">The system will verify your matric number and send an OTP if valid.</p>
                                </div>
                                <a href="javascript:void(0);"><i class="ti ti-key"></i></a>
                            </div>

                            <div class="d-flex align-items-center flex-row mb-3 justify-content-between p-3 br-5 gap-3 card">
                                <div>
                                    <h6>Step 3: Enter the OTP</h6>
                                    <p class="mb-0 text-truncate">Enter the OTP sent to your registered email.</p>
                                </div>
                                <a href="javascript:void(0);"><i class="ti ti-lock"></i></a>
                            </div>

                            <div class="d-flex align-items-center flex-row mb-0 justify-content-between p-3 br-5 gap-3 card">
                                <div>
                                    <h6>Step 4: Set a New Password</h6>
                                    <p class="mb-0 text-truncate">Create a strong new password and confirm to complete the reset.</p>
                                </div>
                                <a href="javascript:void(0);"><i class="ti ti-check"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Forgot Password Form -->
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap">
                        <div class="col-md-8 mx-auto p-4">
                            <form action="{{ route('students.auth.forget-password') }}" method="POST">
                                @csrf
                                <div>
                                    <div class="mx-auto mb-5 text-center">
                                        <img src="{{ asset('portal_assets/img/logo/logo.svg') }}" class="img-fluid" alt="Logo" style="height:70px;">
                                        <h1>Forgot Password</h1>
                                        <h2>Verify your account using Matric Number</h2>
                                    </div>

                                    @include('layouts.flash-message')

                                    <div class="card">
                                        <div class="card-body p-4">
                                            <div class="mb-4">
                                                <h2 class="mb-2">Forgot Your Password?</h2>
                                                <p class="mb-0">Enter your matric number to start the password reset process.</p>
                                            </div>

                                            <!-- Matric Number Field -->
                                            <div class="mb-3">
                                                <label class="form-label">Matric Number</label>
                                                <div class="input-icon mb-3 position-relative">
                                                    <span class="input-icon-addon">
                                                        <i class="ti ti-user"></i>
                                                    </span>
                                                    <input type="text" name="matric_number" class="form-control" value="{{ old('matric_number') }}" required>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary w-100">Verify Matric Number</button>
                                            </div>

                                            <!-- Back to Login -->
                                            <div class="text-center mt-3">
                                                <a href="{{ route('student.login') }}" class="link-primary">Back to Login</a>
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

<!DOCTYPE html>
<html lang="en">

@include('applications.partials.head')

<body>
    <!-- header area -->
    @include('applications.partials.menu')
    <!-- header area end -->

    <main>
    <!-- login area -->
    <div class="login-area py-120">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left side: Instructions -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="p-4">
                        <h2 class="mb-3">Reset Your Password</h2>
                        <p class="lead">
                            Enter the OTP sent to your registered email along with your new password.
                        </p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-key text-success me-2"></i> Enter your <strong>OTP</strong></li>
                            <li><i class="fas fa-lock text-success me-2"></i> Set a <strong>New Password</strong></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Confirm and update your password</li>
                        </ul>
                        <p class="mt-4">
                            Remembered your password?  
                            <a href="{{ route('application.login') }}">Back to Login</a>.
                        </p>
                    </div>
                </div>

                <!-- Right side: OTP + Update Password Form -->
                <div class="col-lg-6">
                    <div class="login-form shadow-lg p-4 rounded">
                        <div class="login-header text-center mb-4">
                            <img src="{{ asset('assets/img/logo/logo.svg') }}" alt="{{ config('app.name') }}" class="mb-3" style="max-width: 120px;">
                            <h4>Update Password</h4>
                            <p>Use the OTP from your email to reset your password</p>
                        </div>

                        @include('layouts.flash-message')

                        <form action="{{ route('password.otp.update') }}" method="POST">
                            @csrf

                            <!-- OTP -->
                            <div class="form-group mb-3">
                                <label for="otp">OTP <code>*</code></label>
                                <input id="otp" name="otp" type="text" 
                                    class="form-control @error('otp') is-invalid @enderror" 
                                    value="{{ old('otp') }}" required autofocus>
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div class="form-group mb-3">
                                <label for="password">New Password <code>*</code></label>
                                <input id="password" name="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group mb-3">
                                <label for="password_confirmation">Confirm New Password <code>*</code></label>
                                <input id="password_confirmation" name="password_confirmation" type="password" 
                                    class="form-control" required>
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="theme-btn w-100">
                                <i class="fas fa-lock"></i> Update Password
                            </button>

                            <!-- Back to login -->
                            <div class="text-center mt-3">
                                <a href="{{ route('application.login') }}">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- login area end -->
</main>


    @include('applications.partials.footer')

    <!-- scroll-top -->
    <a href="index-2.html#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->
    <!-- js -->
    @include('applications.partials.js')
</body>

</html>

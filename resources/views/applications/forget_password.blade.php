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
                
                <!-- Left side: Forgot Password Info -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="p-4">
                        <h2 class="mb-3">Forgot Your Password?</h2>
                        <p class="lead">
                            No worries! Enter your registered email address and we’ll send you a link to reset your password.
                        </p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-envelope text-success me-2"></i> Provide your <strong>Email</strong> used for registration</li>
                            <li><i class="fas fa-link text-success me-2"></i> Check your inbox for a <strong>OTP</strong></li>
                            <li><i class="fas fa-lock text-success me-2"></i> Use the OTP to set a <strong>New Password</strong></li>
                        </ul>
                        <p class="mt-4">
                            Remembered your password?  
                            <a href="{{ route('application.login') }}">Back to Login</a>.
                        </p>
                    </div>
                </div>

                <!-- Right side: Forgot Password Form -->
                <div class="col-lg-6">
                    <div class="login-form shadow-lg p-4 rounded">
                        <div class="login-header text-center mb-4">
                            <img src="{{ asset('assets/img/logo/logo.svg') }}" alt="{{ config('app.name') }}" class="mb-3" style="max-width: 120px;">
                            <h4>Reset Your Password</h4>
                            <p>We’ll email you instructions to reset your password</p>
                        </div>

                        @include('layouts.flash-message')

                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email">Registered Email <code>*</code></label>
                                <input id="email" name="email" type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            

                            <!-- Submit -->
                            <button type="submit" class="theme-btn w-100">
                                <i class="fas fa-paper-plane"></i> Send Reset Link
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
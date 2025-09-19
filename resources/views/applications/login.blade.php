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
                
                <!-- Left side: Welcome -->
                <!-- Left side: Welcome -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="p-4">
                        <h2 class="mb-3">Welcome Back to {{ config('app.name') }}</h2>
                        <p class="lead">
                            Please follow the instructions below to log in and access your dashboard.
                        </p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Enter your <strong>Email or Registration Number</strong></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Provide your <strong>Password</strong></li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Click the <strong>Login</strong> button to continue</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> If you forgot your password, use the <strong>Forgot Password</strong> link</li>
                        </ul>
                        <p class="mt-4">
                            Don’t have an account yet? 
                            <a href="{{ route('application.register') }}">Register here</a>.
                        </p>
                    </div>
                </div>


                <!-- Right side: Login form -->
                <div class="col-lg-6">
                    <div class="login-form shadow-lg p-4 rounded">
                        <div class="login-header text-center mb-4">
                            <img src="{{ asset('assets/img/logo/logo.svg') }}"  alt="{{ config('app.name') }}"  class="mb-3" style="max-width: 120px;">
                            <h4>Application Portal</h4>
                            <p>Please login to continue</p>
                        </div>

                       @include('layouts.flash-message')

                        <form action="{{ route('application.login') }}" method="POST">
                            @csrf
                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email_registration_number">Email / Registration Number <code>*</code></label>
                                <input id="email_registration_number" name="email_registration_number" type="email_registration_number" 
                                       class="form-control @error('email_registration_number') is-invalid @enderror" 
                                       value="{{ old('email_registration_number') }}" required autofocus>
                                @error('email_registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label for="password">Password <code>*</code></label>
                                <input id="password" name="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="theme-btn w-100">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>

                            <!-- Forgot password -->
                            <div class="text-center mt-3">
                                <a href="{{ route('application.forgot.password') }}">Forgot Your Password?</a>
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
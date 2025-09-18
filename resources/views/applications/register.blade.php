<!DOCTYPE html>
<html lang="en">

@include('applications.partials.head')

<body>
    <!-- header area -->
    @include('applications.partials.menu')
    <!-- header area end -->

    <main>
        <!-- register area -->
        <div class="login-area py-120">
            <div class="container">
                <div class="row align-items-center">
                    
                    <!-- Left side: Instructions -->
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="p-4">
                            <h2 class="mb-3">Welcome to {{ config('app.name') }} Application Portal</h2>
                            <p class="lead">
                                Create your account to start your application process and access all features of the portal.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li><i class="fas fa-check-circle text-success me-2"></i> Quick and easy registration</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Select your preferred <strong>Campus</strong></li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Manage your applications anytime</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Stay updated with admission notices</li>
                            </ul>
                            <p class="mt-4">
                                Already have an account? 
                                <a href="{{ route('application.login') }}">Login here</a>.
                            </p>
                        </div>
                    </div>


                    <!-- Right side: Form -->
                    <div class="col-lg-7">
                        <div class="login-form shadow-lg p-4 rounded">
                            <div class="login-header text-center mb-4">
                                <img src="{{ asset('assets/img/logo/logo.svg') }}" alt="{{ config('app.name') }}" class="mb-3" style="max-width: 120px;">
                                <h4>Application Portal</h4>
                                <p>Create your account here.</p>
                            </div>
                            {{-- alert --}}
                            @include('layouts.flash-message')
                            {{-- alert --}}
                            <form action="{{ route('application.register') }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="first_name">First Name <code>*</code></label>
                                    <input id="first_name" name="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="last_name">Last Name <code>*</code></label>
                                    <input id="last_name" name="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="middle_name">Middle Name</label>
                                    <input id="middle_name" name="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">Email <code>*</code></label>
                                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">Phone <code>*</code></label>
                                    <input id="phone" name="phone" type="text" 
                                        class="form-control @error('phone') is-invalid @enderror" 
                                        value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campus Dropdown -->
                                <div class="form-group mb-3">
                                    <label for="center">Center <code>*</code></label>
                                    <select id="center" name="center" 
                                            class="form-control @error('center') is-invalid @enderror" required>
                                        <option value=""> Select Campus </option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('center') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('center')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="form-group mb-3">
                                    <label for="password">Password <code>*</code></label>
                                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation">Password Again <code>*</code></label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                                </div>

                                <button type="submit" class="theme-btn w-100">
                                    <i class="far fa-paper-plane"></i> Register
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- register area end -->
    </main>

    @include('applications.partials.footer')

    <!-- scroll-top -->
    <a href="index-2.html#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->
    <!-- js -->
    @include('applications.partials.js')
</body>

</html>
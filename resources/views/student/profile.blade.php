@extends('layouts.app')

@section('title', 'Edit Profile')

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
                        <h3 class="page-title mb-1">Edit Profile</h3>
                    </div>
                </div>
                <!-- /Page Header -->
                
                @include('layouts.flash-message')

                <div class="row">
                    <!-- Profile Card -->
                    @include('student.partials.profile-card')
                    <!-- /Profile Card -->

                    <!-- Edit Profile Form -->
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Update Profile Information</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('students.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- Profile Picture & Email -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Profile Picture</label>
                                            <input type="file" 
                                                name="profile_picture" 
                                                class="form-control @error('profile_picture') is-invalid @enderror" 
                                                accept="image/jpeg,image/png,image/jpg">
                                            <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG. Max size: 2MB.</small>
                                            @error('profile_picture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" 
                                                name="email" 
                                                class="form-control @error('email') is-invalid @enderror" 
                                                value="{{ old('email', $user->email) }}" required>
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- Names -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                name="first_name" 
                                                class="form-control @error('first_name') is-invalid @enderror" 
                                                value="{{ old('first_name', $user->first_name) }}" required>
                                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name</label>
                                            <input type="text" 
                                                name="middle_name" 
                                                class="form-control @error('middle_name') is-invalid @enderror" 
                                                value="{{ old('middle_name', $user->middle_name) }}">
                                            @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                name="last_name" 
                                                class="form-control @error('last_name') is-invalid @enderror" 
                                                value="{{ old('last_name', $user->last_name) }}" required>
                                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- Contact -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                name="phone" 
                                                class="form-control @error('phone') is-invalid @enderror" 
                                                value="{{ old('phone', $user->phone) }}">
                                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" 
                                                name="date_of_birth" 
                                                class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- Gender & Address -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                                            <select name="sex" class="form-control @error('sex') is-invalid @enderror" required>
                                                <option value="" disabled {{ old('sex', $user->student->sex) ? '' : 'selected' }}>Select Gender</option>
                                                <option value="male" {{ old('sex', $user->student->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('sex', $user->student->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            @error('sex') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" 
                                                name="address" 
                                                class="form-control @error('address') is-invalid @enderror" 
                                                value="{{ old('address', $user->student->address) }}">
                                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- State, LGA, Nationality, Religion -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">State of Origin</label>
                                            <input type="text" 
                                                name="state_of_origin" 
                                                class="form-control @error('state_of_origin') is-invalid @enderror" 
                                                value="{{ old('state_of_origin', $user->state_of_origin) }}">
                                            @error('state_of_origin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">LGA</label>
                                            <input type="text" 
                                                name="lga" 
                                                class="form-control @error('lga') is-invalid @enderror" 
                                                value="{{ old('lga', $user->lga) }}">
                                            @error('lga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nationality</label>
                                            <input type="text" 
                                                name="nationality" 
                                                class="form-control @error('nationality') is-invalid @enderror" 
                                                value="{{ old('nationality', $user->nationality) }}">
                                            @error('nationality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Religion</label>
                                            <input type="text" 
                                                name="religion" 
                                                class="form-control @error('religion') is-invalid @enderror" 
                                                value="{{ old('religion', $user->religion) }}">
                                            @error('religion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- /Edit Profile Form -->

                    <!-- Change Password Form -->
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Change Password</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('students.change.password') }}" method="POST">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                                @error('current_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                                                @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                                <input type="password" name="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" required>
                                                @error('new_password_confirmation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-lock me-1"></i>Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Change Password Form -->
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
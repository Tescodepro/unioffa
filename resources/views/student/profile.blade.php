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
                    <div class="my-auto mb-2">
                        <a href="" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-lock me-1"></i>Change Password
                        </a>
                    </div>
                </div>
                <!-- /Page Header -->

                <div class="row">
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
                                                {{ $user->username }} ({{ $user->student->matric_no ?? 'N/A' }})
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
                                                    Programme: {{ $user->student->programme ?? 'N/A' }}
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

                    <!-- Edit Profile Form -->
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Update Profile Information</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('students.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Profile Picture -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Profile Picture</label>
                                                <input type="file" name="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                                                <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG. Max size: 2MB.</small>
                                                @error('profile_picture')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Read-Only Information -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Matric Number</label>
                                                <input type="text" class="form-control" value="{{ $user->student->matric_no ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Faculty</label>
                                                <input type="text" class="form-control" value="{{ $user->student->department->faculty->faculty_name ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Department</label>
                                                <input type="text" class="form-control" value="{{ $user->student->department->department_name ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Programme</label>
                                                <input type="text" class="form-control" value="{{ $user->student->programme ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Level</label>
                                                <input type="text" class="form-control" value="{{ $user->student->level ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Editable Information -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $user->full_name) }}" required>
                                                @error('full_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $user->phone_number) }}" required>
                                                @error('phone_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $user->student->date_of_birth) }}">
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                                <select name="sex" class="form-control @error('sex') is-invalid @enderror" required>
                                                    <option value="" disabled {{ old('sex', $user->student->sex) ? '' : 'selected' }}>Select Gender</option>
                                                    <option value="Male" {{ old('sex', $user->student->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('sex', $user->student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                                </select>
                                                @error('sex')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->student->address) }}">
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <label class="form-label">State of Origin</label>
                                                <input type="text" name="state_of_origin" class="form-control @error('state_of_origin') is-invalid @enderror" value="{{ old('state_of_origin', $user->student->state_of_origin) }}">
                                                @error('state_of_origin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-1"></i>Save Changes
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
                                    @method('PUT')

                                    <div class="row mb-3">
                                        <div class="col-md-6">
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
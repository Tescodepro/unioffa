@extends('layouts.app')

@section('title', 'Lecturer Dashboard')

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>

<div class="main-wrapper">

    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-2">
                    <h3 class="page-title mb-1">Lecturer Dashboard</h3>
                    <p class="text-muted">Welcome, {{ $user->first_name }} {{ $user->last_name }}</p>
                </div>
            </div>

            <!-- Lecturer Profile Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                 alt="Profile Picture" 
                                 class="rounded-circle" width="80" height="80">
                        @else
                            <div class="avatar avatar-xl rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 24px;">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                        <p class="mb-0 text-muted">Lecturer, {{ $department->department_name ?? 'N/A' }}</p>
                        <small class="text-muted">Staff No: {{ $staff->staff_no ?? 'N/A' }}</small><br>
                        <small class="text-muted">Status: 
                            <span class="badge bg-{{ $staff->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Row -->
            <div class="row mb-4">
                <div class="col-sm-6 col-xl-3 d-flex">
                    <div class="card border-0 border-bottom border-primary flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md rounded bg-primary me-2">
                                    <i class="ti ti-book fs-16"></i>
                                </span>
                                <div>
                                    <h3 class="mb-0">{{ $totalCourses }}</h3>
                                    <p class="mb-0 text-muted">Assigned Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 d-flex">
                    <div class="card border-0 border-bottom border-success flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md rounded bg-success me-2">
                                    <i class="ti ti-users fs-16"></i>
                                </span>
                                <div>
                                    <h3 class="mb-0">{{ $totalStudents }}</h3>
                                    <p class="mb-0 text-muted">Total Students</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 d-flex">
                    <div class="card border-0 border-bottom border-warning flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md rounded bg-warning me-2">
                                    <i class="ti ti-building fs-16"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $department->department_name ?? 'N/A' }}</h6>
                                    <p class="mb-0 text-muted">Department</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3 d-flex">
                    <div class="card border-0 border-bottom border-info flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md rounded bg-info me-2">
                                    <i class="ti ti-school fs-16"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $faculty->faculty_name ?? 'N/A' }}</h6>
                                    <p class="mb-0 text-muted">Faculty</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students by Level -->
            <h5 class="mb-3">Students in Your Department by Level</h5>
            <div class="row">
                @forelse($studentsByLevel as $levelData)
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <div class="card border-0 border-bottom border-primary flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-primary me-2">
                                        <i class="ti ti-users-group fs-16"></i>
                                    </span>
                                    <div>
                                        <h3 class="mb-0">{{ $levelData->student_count }}</h3>
                                        <p class="mb-0 text-muted">{{ $levelData->level }} Level</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No students found in your department.</p>
                    </div>
                @endforelse
            </div>

            <!-- Assigned Courses -->
            <h5 class="mb-3 mt-4">Your Assigned Courses</h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($courses->isEmpty())
                        <div class="text-center py-4">
                            <i class="ti ti-book-off fs-48 text-muted mb-3"></i>
                            <p class="text-muted">No courses assigned to you yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Title</th>
                                        <th>Units</th>
                                        <th>Level</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr>
                                            <td><strong>{{ $course->course_code }}</strong></td>
                                            <td>{{ $course->course_title }}</td>
                                            <td>{{ $course->course_unit }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $course->level ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $course->semester ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.animate-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.animate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.card-link {
    text-decoration: none;
    color: inherit;
}
</style>
@endpush
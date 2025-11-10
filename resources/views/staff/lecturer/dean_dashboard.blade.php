@extends('layouts.app')

@section('title', 'Dean Dashboard')

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
                    <h3 class="page-title mb-1">Dean Dashboard</h3>
                    <p class="text-muted">Welcome, {{ $user->first_name }} {{ $user->last_name }}</p>
                </div>
            </div>

            <!-- Dean Profile Card -->
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
                        <p class="mb-0 text-muted">Dean, {{ $faculty->faculty_name ?? 'N/A' }}</p>
                        <small class="text-muted">Staff No: {{ $staff->staff_no ?? 'N/A' }}</small><br>
                        <small class="text-muted">Status: 
                            <span class="badge bg-{{ $staff->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($staff->status) }}
                            </span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Departments Cards -->
            <h5 class="mb-3">Departments under your faculty</h5>
            <div class="row">
                @forelse($departments as $department)
                    <div class="col-sm-6 col-xl-3 d-flex">
                        <a href="{{ route('dean.department.students', $department->id) }}" class="card-link w-100">
                            <div class="card border-0 border-bottom border-primary flex-fill animate-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-md rounded bg-primary me-2">
                                            <i class="ti ti-building fs-16"></i>
                                        </span>
                                        <div>
                                            <h6 class="mb-0">{{ $department->department_name }}</h6>
                                            <p class="mb-0 text-muted">{{ $department->students_count }} Students</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No departments found under your faculty.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection
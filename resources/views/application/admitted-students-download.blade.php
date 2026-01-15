@extends('layouts.app')
@section('title', 'Admitted Students')
@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .filter-active {
            background-color: #e7f3ff !important;
            border-color: #0d6efd !important;
        }
    </style>
@endpush

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">
                <!-- Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">Admitted Students</h3>
                        <p class="text-muted mb-0">View, filter, and download admitted student records</p>
                    </div>
                    @include('layouts.flash-message')
                </div>

                <!-- Statistics -->
                <div class="row mb-3">
                    <div class="col-md-4 col-6">
                        <div class="card text-center p-3 shadow-sm border-0">
                            <h6 class="text-muted mb-1">Total Students</h6>
                            <h3 class="fw-bold text-primary">{{ number_format($totalStudents) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="card text-center p-3 shadow-sm border-0">
                            <h6 class="text-muted mb-1">Filtered Results</h6>
                            <h3 class="fw-bold text-success">{{ number_format($totalFiltered) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mt-3 mt-md-0">
                        <div class="card text-center p-3 shadow-sm border-0">
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h3 class="fw-bold text-info">{{ $departments->count() }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Filters Form -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Filter Students</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admitted-students.index') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Academic Session</label>
                                    <select name="session"
                                        class="form-select {{ isset($filters['session']) && $filters['session'] ? 'filter-active' : '' }}">
                                        <option value="">All Sessions</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session }}" {{ (isset($filters['session']) && $filters['session'] == $session) ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Department</label>
                                    <select name="department_id"
                                        class="form-select {{ isset($filters['department_id']) && $filters['department_id'] ? 'filter-active' : '' }}">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ (isset($filters['department_id']) && $filters['department_id'] == $dept->id) ? 'selected' : '' }}>
                                                {{ $dept->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Center/Campus</label>
                                    <select name="campus_id"
                                        class="form-select {{ isset($filters['campus_id']) && $filters['campus_id'] ? 'filter-active' : '' }}">
                                        <option value="">All Centers</option>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ (isset($filters['campus_id']) && $filters['campus_id'] == $campus->id) ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Entry Mode</label>
                                    <select name="entry_mode"
                                        class="form-select {{ isset($filters['entry_mode']) && $filters['entry_mode'] ? 'filter-active' : '' }}">
                                        <option value="">All Entry Modes</option>
                                        @foreach($entryModes as $mode)
                                            <option value="{{ $mode }}" {{ (isset($filters['entry_mode']) && $filters['entry_mode'] == $mode) ? 'selected' : '' }}>
                                                {{ $mode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary fs-6">{{ number_format($totalFiltered) }}</span>
                                        <span class="text-muted">students matching filters</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-filter me-1"></i>Apply Filter
                                        </button>
                                        <a href="{{ route('admitted-students.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-1"></i>Clear All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student List</h5>
                        @if($hasFilters)
                            <span class="badge bg-info">Filtered</span>
                        @endif
                    </div>
                    <div class="card-body table-responsive">
                        <table id="students-table" class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>S/N</th>
                                    <th>Matric No</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Entry Mode</th>
                                    <th>Level</th>
                                    <th>Session</th>
                                    <th>Center</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    <tr>
                                        <td>{{ $students->firstItem() + $index }}</td>
                                        <td><span class="fw-semibold">{{ $student->matric_no ?? 'Not Assigned' }}</span></td>
                                        <td>{{ $student->user?->full_name ?? 'N/A' }}</td>
                                        <td>{{ $student->user?->email ?? 'N/A' }}</td>
                                        <td>{{ $student->department?->department_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $student->entry_mode ?? 'N/A' }}</span></td>
                                        <td>{{ $student->level ?? 'N/A' }}</td>
                                        <td>{{ $student->admission_session ?? 'N/A' }}</td>
                                        <td>{{ $student->campus?->name ?? 'N/A' }}</td>
                                        <td>
                                            @if(($student->status ?? 'active') === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">No students found matching the
                                            filters</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($students->hasPages())
                        <div class="card-footer d-flex justify-content-end">
                            {{ $students->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                <!-- Download Section -->
                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="ti ti-download me-2"></i>Download Students Data</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Download the student list as Excel. Use the filters above to narrow down
                            the data before downloading.</p>
                        <form action="{{ route('admitted-students.download') }}" method="POST" class="row g-3">
                            @csrf
                            <!-- Pass current filters to download -->
                            <input type="hidden" name="session" value="{{ $filters['session'] ?? '' }}">
                            <input type="hidden" name="department_id" value="{{ $filters['department_id'] ?? '' }}">
                            <input type="hidden" name="campus_id" value="{{ $filters['campus_id'] ?? '' }}">
                            <input type="hidden" name="entry_mode" value="{{ $filters['entry_mode'] ?? '' }}">

                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="ti ti-file-spreadsheet me-2"></i>Download Excel
                                    ({{ number_format($totalFiltered) }} students)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
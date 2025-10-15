@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div id="global-loader"><div class="page-loader"></div></div>

<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <!-- Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="page-title mb-1">Manage Students</h3>
                    <p class="text-muted mb-0">View, edit, add, or delete student records</p>
                </div>
                @include('layouts.flash-message')
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ict.students.create') }}" class="btn btn-primary">
                        <i class="ti ti-user-plus"></i> Add Student
                    </a>
                    <a href="{{ route('ict.students.bulk') }}" class="btn btn-secondary">
                        <i class="ti ti-upload"></i> Bulk Upload
                    </a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-3">
                <div class="col-md-6 col-6">
                    <div class="card text-center p-3 shadow-sm border-0">
                        <h6 class="text-muted mb-1">Total Students</h6>
                        <h3 class="fw-bold">{{ number_format($stats['total']) }}</h3>
                    </div>
                </div>
                <div class="col-md-6 col-6">
                    <div class="card text-center p-3 shadow-sm border-0">
                        <h6 class="text-muted mb-1">Departments</h6>
                        <h3 class="fw-bold">{{ $departments->count() }}</h3>
                    </div>
                </div>
                <div class="col-md-12 mt-3 mt-md-0">
                    <div class="card p-3 shadow-sm border-0">
                        <h6 class="text-muted mb-2">Students by Department</h6>
                        @if($stats['by_department']->count())
                            <ul class="list-group list-group-flush small">
                                @foreach ($stats['by_department'] as $dept)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $dept->department->department_name ?? '—' }}
                                        <span class="badge bg-primary rounded-pill">{{ $dept->total }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-0">No data available</p>
                        @endif
                    </div>
                </div>
            </div>

             <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->department_name }} ({{ $dept->faculty->faculty_name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Level</label>
                            <select name="level" class="form-select">
                                <option value="">All Levels</option>
                                @foreach([100,200,300,400,500] as $lvl)
                                    <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>
                                        {{ $lvl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Search by name" value="{{ request('name') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Matric No</label>
                            <input type="text" name="matric_no" class="form-control" placeholder="Search by matric no" value="{{ request('matric_no') }}">
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Matric No</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Programme</th>
                                <th>Level</th>
                                <th>Sex</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $student->matric_no }}</td>
                                    <td>{{ $student->user->first_name }} {{ $student->user->last_name }}</td>
                                    <td>{{ $student->department->department_name ?? '—' }}</td>
                                    <td>{{ $student->programme }}</td>
                                    <td>{{ $student->level ?? '—' }}</td>
                                    <td>{{ ucfirst($student->sex ?? '—') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('ict.students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('ict.students.destroy', $student->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No students found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($students->hasPages())
                        <div class="card-footer d-flex justify-content-end">
                            {{ $students->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

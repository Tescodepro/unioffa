@extends('layouts.app')

@section('title', 'My Results')

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
                        <h3 class="page-title mb-1">My Results</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('students.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Results</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <a href="{{ route('students.transcript') }}" class="btn btn-primary">
                            <i class="ti ti-file-text me-2"></i>View Full Transcript
                        </a>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Filter Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="ti ti-filter me-2"></i>Filter Results</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('students.results') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Session</label>
                                <select name="session" class="form-select">
                                    <option value="">All Sessions</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                            {{ $session }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-select">
                                    <option value="">All Semesters</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                            {{ $semester }} Semester
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti ti-search me-2"></i>Filter
                                </button>
                                <a href="{{ route('students.results') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh me-2"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Filter Section -->

                <!-- GPA Card -->
                @if ($results->isNotEmpty())
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 border-start border-primary border-5">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg rounded bg-primary me-3">
                                            <i class="ti ti-chart-bar fs-24"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1">
                                                {{ request('session') || request('semester') ? 'Semester' : 'Cumulative' }} GPA
                                            </p>
                                            <h3 class="mb-0">{{ $semesterStats['gpa'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 border-start border-success border-5">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg rounded bg-success me-3">
                                            <i class="ti ti-school fs-24"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1">Total Units</p>
                                            <h3 class="mb-0">{{ $semesterStats['total_units'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 border-start border-info border-5">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg rounded bg-info me-3">
                                            <i class="ti ti-checks fs-24"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-1">Units Passed</p>
                                            <h3 class="mb-0">{{ $semesterStats['units_passed'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- /GPA Card -->

                <!-- Results Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ti ti-list me-2"></i>Results</h5>
                    </div>
                    <div class="card-body">
                        @if ($results->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-file-off fs-48 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No Results Found</h5>
                                <p class="text-muted">You don't have any published results yet.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Session</th>
                                            <th>Semester</th>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th class="text-center">Units</th>
                                            <th class="text-center">CA</th>
                                            <th class="text-center">Exam</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Grade</th>
                                            <th class="text-center">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $result)
                                            <tr>
                                                <td>{{ $result->session }}</td>
                                                <td>{{ $result->semester }}</td>
                                                <td><strong>{{ $result->course_code }}</strong></td>
                                                <td>{{ $result->course_title }}</td>
                                                <td class="text-center">{{ $result->course_unit }}</td>
                                                <td class="text-center">{{ $result->ca }}</td>
                                                <td class="text-center">{{ $result->exam }}</td>
                                                <td class="text-center"><strong>{{ $result->total }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge 
                                                                    @if($result->grade == 'A') bg-success
                                                                    @elseif($result->grade == 'B') bg-primary
                                                                    @elseif($result->grade == 'C') bg-info
                                                                    @elseif($result->grade == 'D') bg-warning
                                                                    @elseif($result->grade == 'E') bg-secondary
                                                                    @else bg-danger
                                                                    @endif">
                                                        {{ $result->grade }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge {{ $result->remark == 'Pass' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $result->remark }}
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
                <!-- /Results Table -->

            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->
@endsection
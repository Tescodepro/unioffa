@extends('layouts.app')

@section('title', 'My Transcript')

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
                        <h3 class="page-title mb-1">Academic Transcript</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('students.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Transcript</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <a href="{{ route('students.transcript.download') }}" class="btn btn-primary me-2">
                            <i class="ti ti-download me-2"></i>Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="ti ti-printer me-2"></i>Print
                        </button>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Student Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Student Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-semibold" style="width: 150px;">Name:</td>
                                        <td>{{ $student->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Matric Number:</td>
                                        <td>{{ $student->matric_no }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Department:</td>
                                        <td>{{ $student->department->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Faculty:</td>
                                        <td>{{ $student->department->faculty->name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Academic Summary</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-semibold" style="width: 150px;">Programme:</td>
                                        <td>{{ $student->programme }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Entry Mode:</td>
                                        <td>{{ $student->entry_mode }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Current Level:</td>
                                        <td>{{ $student->level }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">CGPA:</td>
                                        <td><h4 class="mb-0 text-primary">{{ $cgpa }}</h4></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Student Info Card -->

                <!-- Results by Session -->
                @forelse($resultsBySession as $session => $results)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $session }} Academic Session</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $sessionResults = collect($results);
                                $firstSemester = $sessionResults->where('semester', 'First');
                                $secondSemester = $sessionResults->where('semester', 'Second');
                            @endphp

                            @if($firstSemester->isNotEmpty())
                                <h6 class="text-secondary mt-3 mb-2">First Semester</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
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
                                            @foreach($firstSemester as $result)
                                                <tr>
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
                                                        <span class="badge {{ $result->remark == 'Pass' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $result->remark }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if($secondSemester->isNotEmpty())
                                <h6 class="text-secondary mt-4 mb-2">Second Semester</h6>
<div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
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
                                            @foreach($secondSemester as $result)
                                                <tr>
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
                                                        <span class="badge {{ $result->remark == 'Pass' ? 'bg-danger' }}">
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
                @empty
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ti ti-file-off fs-48 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">No Results Found</h5>
                            <p class="text-muted">You don't have any published results yet.</p>
                        </div>
                    </div>
                @endforelse
                <!-- /Results by Session -->

            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->
@endsection

@push('styles')
<style>
    @media print {
        .page-header,
        .sidebar,
        .btn,
        .breadcrumb,
        .d-print-none {
            display: none !important;
        }
        
        .page-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
        }
    }
</style>
@endpush

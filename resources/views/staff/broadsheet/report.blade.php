@extends('layouts.app')

@section('title', ($type === 'sessional' ? 'Result Broadsheet' : 'Semester Result') . ' — ' . $department->department_name)

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
                        <h3 class="page-title mb-1">
                            {{ $type === 'sessional' ? 'Result Broadsheet' : 'Semester Result' }}
                        </h3>
                        <p class="text-muted">
                            {{ $department->department_name }} &mdash;
                            {{ $level }} Level &mdash;
                            {{ $session->name }}
                            @if($semester)
                                &mdash; {{ $semester->name }} Semester
                            @endif
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Back
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="ti ti-printer me-1"></i> Print / Export PDF
                        </button>
                    </div>
                </div>

                <!-- ===== PRINTABLE AREA ===== -->
                <div id="printable-area">

                    <!-- School Info Header -->
                    <div class="text-center mb-3">
                        <h5 class="fw-bold text-uppercase mb-0">{{ $department->faculty->faculty_name ?? '' }}</h5>
                        <h6 class="fw-bold mb-0">{{ $department->department_name }}</h6>
                        <p class="mb-0">
                            Broadsheet {{ $level }} Level &mdash; {{ $session->name }}
                            @if($semester)
                                &mdash; {{ $semester->name }} Semester
                            @endif
                        </p>
                    </div>

                    @if(count($studentsData) === 0)
                        <div class="alert alert-info">
                            No students found for the selected parameters.
                        </div>
                    @else

                        <!-- Broadsheet Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm broadsheet-table">
                                <thead class="table-secondary">
                                    <tr>
                                        <th style="min-width:170px;">Student Info</th>
                                        <th>Uploaded Course Details</th>
                                        <th style="min-width:140px;">Outstanding Course Details</th>
                                        <th style="min-width:130px;">Present {{ $semester ? 'Semester' : 'Session' }}</th>
                                        <th style="min-width:130px;">Final Cumulative</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsData as $data)
                                        <tr>
                                            <!-- Student Info -->
                                            <td class="align-top">
                                                <strong>{{ $data['student']->matric_no }}</strong><br>
                                                {{ strtoupper($data['student']->user->last_name ?? '') }}
                                                {{ $data['student']->user->first_name ?? '' }}
                                                {{ $data['student']->user->middle_name ?? '' }}
                                            </td>

                                            <!-- Uploaded Courses (grouped by semester) -->
                                            <td class="p-0">
                                                @if(empty($data['courses_by_semester']))
                                                    <div class="p-2 text-muted text-center">No courses registered</div>
                                                @else
                                                    <table class="table table-sm mb-0 inner-table">
                                                        <thead>
                                                            <tr class="fw-bold bg-light">
                                                                <td>Course Code</td>
                                                                <td>Unit</td>
                                                                <td>Status</td>
                                                                <td>CA</td>
                                                                <td>Exam</td>
                                                                <td>Total</td>
                                                                <td>Remark</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($data['courses_by_semester'] as $semesterName => $courses)
                                                                <tr>
                                                                    <td colspan="7"
                                                                        class="fst-italic fw-semibold bg-light text-start ps-2 py-1">
                                                                        {{ $semesterName }} Semester Courses
                                                                    </td>
                                                                </tr>
                                                                @foreach($courses as $course)
                                                                    <tr>
                                                                        <td class="text-start ps-2">{{ $course['registration']->course_code }}
                                                                        </td>
                                                                        <td>{{ $course['registration']->course_unit }}</td>
                                                                        <td>C</td>
                                                                        <td>{{ $course['result']?->ca ?? '-' }}</td>
                                                                        <td>{{ $course['result']?->exam ?? '-' }}</td>
                                                                        <td>{{ $course['result']?->total ?? '-' }}</td>
                                                                        <td>{{ $course['result']?->grade ?? '-' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </td>

                                            <!-- Outstanding Courses -->
                                            <td class="p-0 align-top">
                                                @if($data['outstanding_courses']->isEmpty())
                                                    <div class="p-2 text-center text-muted">—</div>
                                                @else
                                                    <table class="table table-sm mb-0 inner-table">
                                                        <thead>
                                                            <tr class="fw-bold bg-light">
                                                                <td>Course Code</td>
                                                                <td>Unit</td>
                                                                <td>Status</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($data['outstanding_courses'] as $outstanding)
                                                                <tr>
                                                                    <td class="text-start ps-2">
                                                                        {{ $outstanding['registration']->course_code }}</td>
                                                                    <td>{{ $outstanding['registration']->course_unit }}</td>
                                                                    <td>AR</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </td>

                                            <!-- Period GPA -->
                                            <td class="align-top p-0">
                                                <table class="table table-sm mb-0 inner-table">
                                                    <thead>
                                                        <tr class="fw-bold bg-light">
                                                            <td>TCO</td>
                                                            <td>WGP</td>
                                                            <td>GPA</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $data['period']['tco'] }}</td>
                                                            <td>{{ $data['period']['wgp'] }}</td>
                                                            <td class="fw-bold">{{ number_format($data['period']['gpa'], 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>

                                            <!-- Cumulative CGPA -->
                                            <td class="align-top p-0">
                                                <table class="table table-sm mb-0 inner-table">
                                                    <thead>
                                                        <tr class="fw-bold bg-light">
                                                            <td>CTCO</td>
                                                            <td>CWGP</td>
                                                            <td>CGPA</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $data['cumulative']['ctco'] }}</td>
                                                            <td>{{ $data['cumulative']['cwgp'] }}</td>
                                                            <td class="fw-bold">{{ number_format($data['cumulative']['cgpa'], 2) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Result Summary -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0 fw-bold text-uppercase">Result Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                                <tr>
                                                    <td>Total Students</td>
                                                    <td><strong>{{ $stats['total_students'] }}</strong></td>
                                                    <td>100%</td>
                                                </tr>
                                                <tr>
                                                    <td>Clear Passes (all scores ≥ 40)</td>
                                                    <td>{{ $stats['clear_passes'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['clear_passes'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Students with courses to repeat (&lt; 40)</td>
                                                    <td>{{ $stats['repeats'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['repeats'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Students with Outstanding Courses</td>
                                                    <td>{{ $stats['outstanding_courses'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['outstanding_courses'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                                <tr>
                                                    <td>CGPA ≥ 4.5 (First Class)</td>
                                                    <td>{{ $stats['cgpa_classes']['first_class'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['first_class'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>1.0 ≤ CGPA ≤ 1.5 (Counselling)</td>
                                                    <td>{{ $stats['cgpa_classes']['counselling'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['counselling'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>CGPA &lt; 1.0 (Withdrawal)</td>
                                                    <td>{{ $stats['cgpa_classes']['withdrawal'] }}</td>
                                                    <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['withdrawal'] / $stats['total_students']) * 100, 1) : 0 }}%
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Signature Lines -->
                                <div class="row mt-5 text-center">
                                    <div class="col-4">
                                        <hr class="border-dark">
                                        <p class="mb-0 fw-semibold">{{ auth()->user()->full_name }}</p>
                                        <small class="text-muted">Generated By</small>
                                    </div>
                                    <div class="col-4">
                                        <hr class="border-dark">
                                        <br>
                                        <small class="text-muted">Head of Department</small>
                                    </div>
                                    <div class="col-4">
                                        <hr class="border-dark">
                                        <br>
                                        <small class="text-muted">Dean of Faculty</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif

                </div>
                <!-- ===== END PRINTABLE AREA ===== -->

            </div>
        </div>

    </div>

    <style>
        @media print {

            /* Hide everything that's not the printable area */
            body * {
                visibility: hidden;
            }

            #printable-area,
            #printable-area * {
                visibility: visible;
            }

            #printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000 !important;
            }
        }

        .broadsheet-table td,
        .broadsheet-table th {
            vertical-align: middle;
            font-size: 0.82rem;
        }

        .inner-table td {
            border: none !important;
            padding: 2px 6px;
            font-size: 0.82rem;
        }
    </style>
@endsection
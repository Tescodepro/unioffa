@extends('layouts.app')

@section('title', $type === 'sessional' ? 'Result Broadsheet' : 'Semester Result')

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
                    <p class="text-muted">Select the parameters below to generate the academic result report.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- FILTER FORM -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-adjustments me-2"></i>Filter Parameters
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ $type === 'sessional' ? route('broadsheet.sessional') : route('broadsheet.semester') }}" method="GET">
                        <div class="row g-3">

                            <div class="col-md-{{ $type === 'semester' ? '3' : '4' }}">
                                <label class="form-label">Department <span class="text-danger">*</span></label>
                                @if(auth()->user()->hasUserType('hod'))
                                    <input type="hidden" name="department_id" value="{{ $departments->first()->id ?? '' }}">
                                    <input type="text" class="form-control"
                                        value="{{ $departments->first()->department_name ?? 'No Department Assigned' }}" readonly>
                                @else
                                    <select name="department_id" class="form-select" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('department_id', request('department_id')) === $dept->id ? 'selected' : '' }}>
                                                {{ $dept->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-{{ $type === 'semester' ? '3' : '4' }}">
                                <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                <select name="session_id" class="form-select" required>
                                    <option value="">-- Select Session --</option>
                                    @foreach($sessions as $sess)
                                        <option value="{{ $sess->id }}"
                                            {{ request('session_id') === $sess->id ? 'selected' : '' }}>
                                            {{ $sess->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-{{ $type === 'semester' ? '3' : '4' }}">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="">-- Select Level --</option>
                                    @foreach([100,200,300,400,500,600] as $lvl)
                                        <option value="{{ $lvl }}"
                                            {{ request('level') == $lvl ? 'selected' : '' }}>
                                            {{ $lvl }} Level
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($type === 'semester')
                                <div class="col-md-3">
                                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester_id" class="form-select" required>
                                        <option value="">-- Select Semester --</option>
                                        @foreach($semesters as $sem)
                                            <option value="{{ $sem->id }}"
                                                {{ request('semester_id') === $sem->id ? 'selected' : '' }}>
                                                {{ $sem->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-file-report me-1"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($studentsData !== null)
                @if(count($studentsData) === 0)
                    <div class="alert alert-warning">
                        <i class="ti ti-info-circle me-2"></i>
                        No students or results found for the selected parameters.
                    </div>
                @else

                <!-- Toolbar -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">
                            {{ $type === 'sessional' ? 'Sessional Broadsheet' : 'Semester Result' }}
                        </h5>
                        <p class="text-muted mb-0 small">
                            {{ $department->department_name }}
                            &mdash; {{ $level }} Level
                            &mdash; {{ $session->name }}
                            @if($semester) &mdash; {{ $semester->name }} Semester @endif
                            &mdash; <strong>{{ count($studentsData) }}</strong> student(s)
                        </p>
                    </div>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="ti ti-printer me-1"></i> Print / Download PDF
                    </button>
                </div>

                <!-- BROADSHEET TABLE -->
                <div id="broadsheet-report">

                    <div class="text-center mb-3">
                        <h5 class="fw-bold text-uppercase mb-0">{{ $department->faculty->faculty_name ?? '' }}</h5>
                        <h6 class="fw-semibold mb-0">{{ $department->department_name }}</h6>
                        <p class="mb-0 text-muted small">
                            {{ $type === 'sessional' ? 'Result Broadsheet' : 'Semester Result' }}
                            &mdash; {{ $level }} Level &mdash; {{ $session->name }}
                            @if($semester) &mdash; {{ $semester->name }} Semester @endif
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm broadsheet-table">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="min-width:190px;">Student Info</th>
                                    <th>Uploaded Course Details</th>
                                    <th style="min-width:150px;white-space:nowrap;">
                                        Present {{ $semester ? 'Semester' : 'Session' }}
                                    </th>
                                    <th style="min-width:150px;white-space:nowrap;">Final Cumulative</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentsData as $data)
                                    <tr>
                                        <!-- Student Info -->
                                        <td class="align-top">
                                            <div class="fw-bold text-primary">{{ $data['student']->matric_no }}</div>
                                            {{ strtoupper($data['student']->user->last_name ?? '') }},
                                            {{ $data['student']->user->first_name ?? '' }}
                                            {{ $data['student']->user->middle_name ?? '' }}
                                        </td>

                                        <!-- Uploaded Courses -->
                                        <td class="p-0">
                                            @if($data['results_by_semester']->isEmpty())
                                                <div class="p-2 text-muted text-center small">No results uploaded</div>
                                            @else
                                                <table class="table table-sm mb-0 inner-table">
                                                    <thead>
                                                        <tr class="inner-head">
                                                            <td class="ps-2">Course Code</td>
                                                            <td class="text-center">Unit</td>
                                                            <td class="text-center">CA</td>
                                                            <td class="text-center">Exam</td>
                                                            <td class="text-center">Total</td>
                                                            <td class="text-center">Grade</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data['results_by_semester'] as $semName => $semResults)
                                                            <tr>
                                                                <td colspan="6" class="sem-label ps-2 py-1">
                                                                    &raquo; {{ $semName }} Semester Courses
                                                                </td>
                                                            </tr>
                                                            @foreach($semResults as $result)
                                                                @php $g = $result->grade ?? '-'; @endphp
                                                                <tr>
                                                                    <td class="ps-2">{{ $result->course_code }}</td>
                                                                    <td class="text-center">{{ $result->course_unit }}</td>
                                                                    <td class="text-center">{{ $result->ca ?? '-' }}</td>
                                                                    <td class="text-center">{{ $result->exam ?? '-' }}</td>
                                                                    <td class="text-center fw-semibold">{{ $result->total ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        <span class="grade-badge grade-{{ strtolower($g) }}">{{ $g }}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>

                                        <!-- Period GPA -->
                                        <td class="align-top p-0">
                                            <table class="table table-sm mb-0 inner-table">
                                                <thead>
                                                    <tr class="inner-head">
                                                        <td>TCO</td><td>WGP</td><td>GPA</td>
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
                                                    <tr class="inner-head">
                                                        <td>CTCO</td><td>CWGP</td><td>CGPA</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $data['cumulative']['ctco'] }}</td>
                                                        <td>{{ $data['cumulative']['cwgp'] }}</td>
                                                        <td class="fw-bold">{{ number_format($data['cumulative']['cgpa'], 2) }}</td>
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
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0 fw-bold text-uppercase">
                                <i class="ti ti-chart-bar me-1"></i> Result Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-light">
                                            <tr><th>Category</th><th>Count</th><th>%</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Total Students</td>
                                                <td><strong>{{ $stats['total_students'] }}</strong></td>
                                                <td>100%</td>
                                            </tr>
                                            <tr>
                                                <td>Clear Passes (no failure)</td>
                                                <td>{{ $stats['clear_passes'] }}</td>
                                                <td>{{ $stats['total_students'] > 0 ? round(($stats['clear_passes']/$stats['total_students'])*100,1) : 0 }}%</td>
                                            </tr>
                                            <tr>
                                                <td>Students with Failure(s)</td>
                                                <td>{{ $stats['repeats'] }}</td>
                                                <td>{{ $stats['total_students'] > 0 ? round(($stats['repeats']/$stats['total_students'])*100,1) : 0 }}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-light">
                                            <tr><th>CGPA Band</th><th>Count</th><th>%</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>CGPA &ge; 4.5 &mdash; First Class</td>
                                                <td>{{ $stats['cgpa_classes']['first_class'] }}</td>
                                                <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['first_class']/$stats['total_students'])*100,1) : 0 }}%</td>
                                            </tr>
                                            <tr>
                                                <td>1.0 &le; CGPA &le; 1.5 &mdash; Counselling</td>
                                                <td>{{ $stats['cgpa_classes']['counselling'] }}</td>
                                                <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['counselling']/$stats['total_students'])*100,1) : 0 }}%</td>
                                            </tr>
                                            <tr>
                                                <td>CGPA &lt; 1.0 &mdash; Withdrawal</td>
                                                <td>{{ $stats['cgpa_classes']['withdrawal'] }}</td>
                                                <td>{{ $stats['total_students'] > 0 ? round(($stats['cgpa_classes']['withdrawal']/$stats['total_students'])*100,1) : 0 }}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Signature Lines -->
                            <div class="row mt-4 text-center">
                                <div class="col-4">
                                    <hr>
                                    <small class="text-muted">Generated By<br>
                                        <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong>
                                    </small>
                                </div>
                                <div class="col-4"><hr><small class="text-muted">Head of Department</small></div>
                                <div class="col-4"><hr><small class="text-muted">Dean of Faculty</small></div>
                            </div>
                        </div>
                    </div>

                </div>

                @endif
            @endif

        </div>
    </div>

</div>

<style>
    .broadsheet-table td, .broadsheet-table th { vertical-align: middle; font-size: 0.82rem; }
    .inner-table td { border: none !important; border-bottom: 1px solid #f0f0f0 !important; padding: 3px 6px; font-size: 0.82rem; }
    .inner-head td { background: #f3f4f6; font-weight: 600; font-size: 0.78rem; }
    .sem-label { font-size: 0.76rem; font-weight: 600; background: #eef2ff; color: #4a6cf7; border-left: 3px solid #4a6cf7; }
    .grade-badge { display: inline-block; border-radius: 4px; padding: 0 5px; font-weight: 700; font-size: 0.78rem; }
    .grade-a { background: #d1fae5; color: #065f46; }
    .grade-b { background: #dbeafe; color: #1e40af; }
    .grade-c { background: #fef9c3; color: #854d0e; }
    .grade-d, .grade-e { background: #f3f4f6; color: #374151; }
    .grade-f { background: #fee2e2; color: #991b1b; }
    .grade-- { background: #f3f4f6; color: #9ca3af; }

    /* ===== PRINT: show ONLY #broadsheet-report ===== */
    @media print {
        body * { visibility: hidden !important; }
        #broadsheet-report, #broadsheet-report * { visibility: visible !important; }
        #broadsheet-report {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
        }
        .grade-a { background: #d1fae5 !important; color: #065f46 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .grade-b { background: #dbeafe !important; color: #1e40af !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .grade-c { background: #fef9c3 !important; color: #854d0e !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .grade-f { background: #fee2e2 !important; color: #991b1b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .sem-label { background: #eef2ff !important; color: #4a6cf7 !important; border-left: 3px solid #4a6cf7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .inner-head td { background: #f3f4f6 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @page { margin: 10mm; size: A4 landscape; }
    }
</style>
@endsection

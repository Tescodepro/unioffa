@extends('layouts.app')

@section('title', 'Result Summary')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Departmental Result Summary</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('lecturer.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Result Summary</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                {{-- Filter Section --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-0 bg-light">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-filter me-2 text-success"></i> Filter by Department &amp; Level
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('results.summary') }}" method="GET" class="row g-3">
                            {{-- Department Dropdown --}}
                            <div class="col-md-5">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Level Dropdown --}}
                            <div class="col-md-5">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select" required>
                                    <option value="">Select Level</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>
                                            {{ $lvl }} Level
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search Button --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search me-2"></i> Generate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Results Table Section --}}
                @if(request()->has('department_id'))
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center d-print-none">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-list me-2 text-primary"></i> Summary List
                            </h5>
                            <a href="{{ route('results.printSummary', ['department_id' => request('department_id'), 'level' => request('level')]) }}" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-printer me-1"></i> Print Result
                            </a>
                        </div>

                        <div class="card-body">
                            @if(count($students) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>S/N</th>
                                                <th>Student Name</th>
                                                <th>Matric Number</th>
                                                <th class="text-center">Units Offered</th>
                                                <th class="text-center">Units Passed</th>
                                                <th class="text-center">CGPA</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $index => $student)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $student->fullname }}</td>
                                                    <td>{{ $student->username }}</td> {{-- Assuming username is matric --}}
                                                    <td class="text-center">{{ $student->units_offered }}</td>
                                                    <td class="text-center">{{ $student->units_passed }}</td>
                                                    <td
                                                        class="text-center fw-bold 
                                                                    {{ $student->cgpa >= 3.5 ? 'text-success' : ($student->cgpa < 1.5 ? 'text-danger' : 'text-dark') }}">
                                                        {{ $student->cgpa }}
                                                    </td>
                                                    <td>
                                                        {{-- Link to individual transcript --}}
                                                        <a href="{{ route('transcript.search', ['matric' => $student->username]) }}"
                                                            class="btn btn-sm btn-info text-white">
                                                            <i class="ti ti-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>


                            @else
                                <div class="alert alert-warning text-center">
                                    No students found for the selected Department and Level.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Print Styles -->
    <style>
        @media print {
            body { background-color: #fff !important; color: #000 !important; margin: 0 !important; padding: 0 !important; }
            .header, .sidebar, .page-header, .breadcrumb, .card-header, form, .alert, .d-print-none { display: none !important; }
            .main-wrapper, .page-wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; background: transparent !important; }
            .content { padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; background: transparent !important; margin: 0 !important; }
            .card-body { padding: 0 !important; position: relative; min-height: 100vh; }
            table { width: 100% !important; border-collapse: collapse !important; border-color: #000 !important; margin-bottom: 150px !important; }
            th, td { font-size: 12px !important; padding: 6px !important; color: #000 !important; vertical-align: middle !important; }
            th { border: 1px solid #000 !important; background-color: #f0f0f0 !important; font-weight: bold !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            td { border: 1px solid #000 !important; }
            .d-print-flex { display: flex !important; }
            .badge { border: none !important; color: #000 !important; background: transparent !important; font-weight: bold; padding: 0 !important; margin: 0 !important; }
            .print-content-start { margin-left: 50px; margin-right: 50px; }
            .table-responsive { overflow: visible !important; }
        }
    </style>
@endpush
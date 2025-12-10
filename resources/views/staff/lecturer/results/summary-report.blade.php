@extends('layouts.app')

@section('title', 'Result Summary')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <h3 class="mb-4">Departmental Result Summary</h3>

            {{-- Filter Section --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fa fa-filter me-2"></i> Filter by Department & Level
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
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fa fa-list-alt me-2"></i> 
                            Summary List
                        </h4>
                        {{-- Optional: Print Button --}}
                        <button onclick="window.print()" class="btn btn-light btn-sm text-primary">
                            <i class="fa fa-print"></i> Print
                        </button>
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
                                                <td class="text-center fw-bold 
                                                    {{ $student->cgpa >= 3.5 ? 'text-success' : ($student->cgpa < 1.5 ? 'text-danger' : 'text-dark') }}">
                                                    {{ $student->cgpa }}
                                                </td>
                                                <td>
                                                    {{-- Link to individual transcript --}}
                                                    <a href="{{ route('transcript.search', ['matric' => $student->username]) }}" 
                                                       class="btn btn-sm btn-info text-white">
                                                       <i class="fa fa-eye"></i> View
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
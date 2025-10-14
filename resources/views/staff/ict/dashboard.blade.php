@extends('layouts.app')

@section('title', 'ICT Dashboard')

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>

<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-2">
                    <h3 class="page-title mb-1">ICT Dashboard</h3>
                    <p class="text-muted mb-0">Overview of student statistics across faculties and departments</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card border-0 border-bottom border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md bg-primary me-2"><i class="ti ti-users"></i></span>
                                <div>
                                    <h6>Total Students</h6>
                                    <h4>{{ $totalStudents }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students by Faculty -->
            <div class="card mb-4">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">Students by Faculty</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th>Total Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsByFaculty as $faculty)
                                <tr>
                                    <td>{{ $faculty->faculty_name }}</td>
                                    <td>{{ $faculty->total_students }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Students by Department -->
            <div class="card mb-4">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">Students by Department</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Faculty</th>
                                <th>Total Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsByDept as $dept)
                                <tr>
                                    <td>{{ $dept->department_name }}</td>
                                    <td>{{ $dept->faculty->faculty_name ?? '—' }}</td>
                                    <td>{{ $dept->students_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Students by Programme -->
            <div class="card">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">Students by Programme</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Programme</th>
                                <th>Total Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentsByProgramme as $prog)
                                <tr>
                                    <td>{{ $prog->programme }}</td>
                                    <td>{{ $prog->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

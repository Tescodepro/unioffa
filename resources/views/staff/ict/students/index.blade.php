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
                    <form method="GET" class="row g-3" id="filterForm">
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

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="text" name="email" class="form-control" placeholder="Search by Email" value="{{ request('email') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="Search by phone number" value="{{ request('phone') }}">
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" id="resetFilters" class="btn btn-outline-secondary w-100">
                                <i class="ti ti-refresh"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DataTable -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Student Records</h5>
                    <div class="card-actions">
                        <button type="button" class="btn btn-success btn-sm" id="exportExcelBtn">
                            <i class="ti ti-download"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="exportPdfBtn">
                            <i class="ti ti-download"></i> Export PDF
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" id="printTableBtn">
                            <i class="ti ti-printer"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="studentsTable" class="table table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Matric No</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Programme</th>
                                <th>Level</th>
                                <th>Sex</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->matric_no }}</td>
                                    <td>{{ $student->user->first_name }} {{ $student->user->last_name }}</td>
                                    <td>{{ $student->department->department_name ?? '—' }}</td>
                                    <td>{{ $student->programme }}</td>
                                    <td>{{ $student->level ?? '—' }}</td>
                                    <td>{{ ucfirst($student->sex ?? '—') }}</td>
                                    <td>{{ $student->user->email ?? '—' }}</td>
                                    <td>{{ $student->phone ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('ict.students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('ict.students.destroy', $student->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student?')" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
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

@push('scripts')
<!-- DataTables CSS & JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#studentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="ti ti-download"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Students Data',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="ti ti-download"></i> PDF',
                className: 'btn btn-info btn-sm',
                title: 'Students Data',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'print',
                text: '<i class="ti ti-printer"></i> Print',
                className: 'btn btn-warning btn-sm',
                title: 'Students Data',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        responsive: true,
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
    });

    // Custom button handlers
    $('#exportExcelBtn').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    $('#exportPdfBtn').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });

    $('#printTableBtn').on('click', function() {
        table.button('.buttons-print').trigger();
    });

    // Remove default DataTable buttons from DOM and use custom ones
    table.buttons().container().addClass('d-none');

    // Reset filters functionality
    $('#resetFilters').on('click', function() {
        $('#filterForm').find('select, input').val('');
        $('#filterForm').submit();
    });

    // Apply server-side filter values to DataTable search
    @if(request()->anyFilled(['name', 'matric_no', 'email', 'phone']))
        // If any text filters are applied, search in DataTable
        @if(request('name'))
            table.column(1).search('{{ request('name') }}');
        @endif
        @if(request('matric_no'))
            table.column(0).search('{{ request('matric_no') }}');
        @endif
        @if(request('email'))
            table.column(6).search('{{ request('email') }}');
        @endif
        @if(request('phone'))
            table.column(7).search('{{ request('phone') }}');
        @endif
        table.draw();
    @endif
});
</script>

<style>
.dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
}

.dataTables_wrapper .dataTables_length {
    float: left;
}

.dataTables_wrapper .dataTables_paginate {
    float: right;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .card-actions {
        flex-direction: column;
        width: 100%;
        margin-top: 1rem;
    }
    
    .card-actions .btn {
        width: 100%;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        float: none;
        text-align: left;
    }
    
    /* Make filter form responsive */
    #filterForm .col-md-3,
    #filterForm .col-md-2,
    #filterForm .col-md-1 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush
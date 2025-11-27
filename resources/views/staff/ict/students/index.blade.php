@extends('layouts.app')
@section('title', 'Student Management')
@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
@endpush
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
                <div class="d-flex flex-wrap gap-2" id="export-buttons">
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
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="text" name="email" class="form-control" placeholder="Search by Email" value="{{ request('email') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="Search by phone number" value="{{ request('phone') }}">
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
                    <table id="students-table" class="table table-striped align-middle">
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
@push('scripts')
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#students-table').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-success me-1',
                exportOptions: {
                    columns: ':visible', // Exclude hidden columns if needed
                    modifier: {
                        page: 'current' // Export only current page; change to 'all' if you load all data
                    }
                }
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-info me-1',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-danger me-1',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-secondary',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        paging: false, // Disable DataTables pagination to avoid conflict with Laravel's server-side pagination
        searching: true, // Enable client-side search (applies to current page only)
        ordering: true,
        info: false, // Hide info since Laravel handles total count
        lengthChange: true,
        order: [[1, 'asc']], // Default sort by name
        language: {
            search: "Search current page:",
            lengthMenu: "Show _MENU_ entries"
        },
        columnDefs: [
            { orderable: false, targets: 6 } // Disable sorting on Action column
        ]
    });

    // Append buttons to the custom container near the Add Student button
    table.buttons().container().appendTo('#export-buttons');
});
</script>
@endpush
@endsection
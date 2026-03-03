@extends('layouts.app')

@section('title', 'Manage Departments')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Academic Setup - Departments</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('ict.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Departments</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        @if(auth()->user()->hasPermission('manage_departments'))
                            <div class="mb-2">
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                    <i class="ti ti-plus me-1"></i>Add Department
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped custom-table datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Department Name</th>
                                                <th>Code</th>
                                                <th>Faculty</th>
                                                <th>Qualification</th>
                                                <th>Total Students</th>
                                                @if(auth()->user()->hasPermission('manage_departments'))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($departments as $key => $department)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td class="fw-bold">{{ $department->department_name }}</td>
                                                    <td><span
                                                            class="badge bg-secondary">{{ $department->department_code }}</span>
                                                    </td>
                                                    <td>{{ $department->faculty->faculty_name ?? 'N/A' }}</td>
                                                    <td>{{ $department->qualification }}</td>
                                                    <td>{{ $department->students->count() }}</td>
                                                    @if(auth()->user()->hasPermission('manage_departments'))
                                                        <td class="text-end">
                                                            <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                                data-bs-target="#editDepartmentModal{{ $department->id }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteDepartmentModal{{ $department->id }}">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </a>
                                                        </td>
                                                    @endif
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    <!-- Add Modal -->
                                    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('ict.departments.store') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Add New Department</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Faculty <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" name="faculty_id" required>
                                                                <option value="">Select Faculty</option>
                                                                @foreach ($faculties as $faculty)
                                                                    <option value="{{ $faculty->id }}">
                                                                        {{ $faculty->faculty_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Department Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="department_name"
                                                                placeholder="e.g., Computer Science" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Department Code <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="department_code"
                                                                placeholder="e.g., CSC" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Qualification <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="qualification"
                                                                placeholder="e.g., B.Sc" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control" name="department_description"
                                                                rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Add
                                                            Department</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                @foreach ($departments as $department)
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('ict.departments.update', $department->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Department</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Faculty <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" name="faculty_id" required>
                                                                <option value="">Select Faculty</option>
                                                                @foreach ($faculties as $faculty)
                                                                    <option value="{{ $faculty->id }}" {{ $department->faculty_id == $faculty->id ? 'selected' : '' }}>
                                                                        {{ $faculty->faculty_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Department Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="department_name"
                                                                value="{{ $department->department_name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Department Code <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="department_code"
                                                                value="{{ $department->department_code }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Qualification <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="qualification"
                                                                value="{{ $department->qualification }}"
                                                                placeholder="e.g., B.Sc" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control" name="department_description"
                                                                rows="3">{{ $department->department_description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteDepartmentModal{{ $department->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('ict.departments.destroy', $department->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the department
                                                            <strong>{{ $department->department_name }}</strong>?</p>
                                                        <p class="text-warning small"><i class="ti ti-alert-triangle"></i> This
                                                            action cannot be undone. Ensure there are no students or payment
                                                            configurations tied to this department before deleting.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
@endsection
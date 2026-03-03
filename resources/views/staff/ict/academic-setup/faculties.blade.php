@extends('layouts.app')
@section('title', 'Manage Faculties')

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
                        <h3 class="page-title mb-1">Academic Setup - Faculties</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('ict.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Faculties</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        @if(auth()->user()->hasPermission('manage_faculties'))
                            <div class="mb-2">
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFacultyModal">
                                    <i class="ti ti-plus me-1"></i>Add Faculty
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
                                                <th>Faculty Name</th>
                                                <th>Code</th>
                                                <th>Description</th>
                                                <th>Total Departments</th>
                                                <th>Total Students</th>
                                                @if(auth()->user()->hasPermission('manage_faculties'))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($faculties as $key => $faculty)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td class="fw-bold">{{ $faculty->faculty_name }}</td>
                                                    <td><span class="badge bg-secondary">{{ $faculty->faculty_code }}</span>
                                                    </td>
                                                    <td>{{ Str::limit($faculty->description, 50) }}</td>
                                                    <td>{{ $faculty->departments->count() }}</td>
                                                    <td>{{ $faculty->students->count() }}</td>
                                                    @if(auth()->user()->hasPermission('manage_faculties'))
                                                        <td class="text-end">
                                                            <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                                data-bs-target="#editFacultyModal{{ $faculty->id }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteFacultyModal{{ $faculty->id }}">
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
                <div class="modal fade" id="addFacultyModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('ict.faculties.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Faculty</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Faculty Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="faculty_name"
                                            placeholder="e.g., Faculty of Science" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Faculty Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="faculty_code" placeholder="e.g., SCI"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add Faculty</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @foreach ($faculties as $faculty)
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editFacultyModal{{ $faculty->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('ict.faculties.update', $faculty->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Faculty</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Faculty Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="faculty_name"
                                                value="{{ $faculty->faculty_name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Faculty Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="faculty_code"
                                                value="{{ $faculty->faculty_code }}" required>
                                            <small class="text-muted">e.g., SCI, ENG, ART</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description"
                                                rows="3">{{ $faculty->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save
                                            Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteFacultyModal{{ $faculty->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('ict.faculties.destroy', $faculty->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the faculty
                                            <strong>{{ $faculty->faculty_name }}</strong>?
                                        </p>
                                        <p class="text-warning small"><i class="ti ti-alert-triangle"></i> This action cannot
                                            be undone. Ensure there are no departments tied to this
                                            faculty before deleting.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Yes,
                                            Delete</button>
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
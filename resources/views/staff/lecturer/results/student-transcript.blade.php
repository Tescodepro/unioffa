@extends('layouts.app')

@section('title', 'Student Transcript')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <h3 class="mb-4">Student Transcript</h3>
                {{-- Search Section --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-search me-2"></i> Search Student by Matric Number
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transcript.search') }}" method="GET" class="row g-2">
                            <div class="col-md-10">
                                <input type="text" name="matric" class="form-control" placeholder="Enter Matric Number"
                                    value="{{ request('matric') }}" required>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button class="btn btn-primary btn-sm">
                                    <i class="fa fa-search me-2"></i>
                                    Search Result
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @isset($student)
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fa fa-file-alt me-2"></i>
                                Transcript for {{ $student->name }} ({{ $student->username }})
                            </h4>
                        </div>

                        <div class="card-body">
                            @forelse($resultsBySession as $session => $results)
                                <h5 class="text-secondary mt-3 mb-2">Session: {{ $session }}</h5>

                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Title</th>
                                            <th>Unit</th>
                                            <th>CA</th>
                                            <th>Exam</th>
                                            <th>Total</th>
                                            <th style="width: 130px;">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($results as $result)
                                            <tr>
                                                <td>{{ $result->course_code }}</td>
                                                <td>{{ $result->course_title }}</td>
                                                <td>{{ $result->course_unit }}</td>
                                                <td>{{ $result->ca }}</td>
                                                <td>{{ $result->exam }}</td>
                                                <td>{{ $result->total }}</td>
                                                <td>{{ $result->status }}</td>

                                                <td>
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $result->id }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $result->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- EDIT MODAL -->
                                            <div class="modal fade" id="editModal{{ $result->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-warning">
                                                            <h5 class="modal-title">Edit Result</h5>
                                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <form action="{{ route('results.update', $result->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="modal-body">

                                                                <div class="mb-3">
                                                                    <label class="form-label">CA Score</label>
                                                                    <input type="number" name="ca" class="form-control"
                                                                        value="{{ $result->ca }}" required>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Exam Score</label>
                                                                    <input type="number" name="exam" class="form-control"
                                                                        value="{{ $result->exam }}" required>
                                                                </div>

                                                            </div>

                                                            <div class="modal-footer">
                                                                <button class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- DELETE MODAL -->
                                            <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Delete Record</h5>
                                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Are you sure you want to delete
                                                            <strong>{{ $result->course_code }}</strong>
                                                            for this student?
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>

                                                            <form action="{{ route('results.delete', $result->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>


                            @empty
                                <p class="text-muted">No results found for this student.</p>
                            @endforelse
                        </div>
                    </div>
                @endisset

            </div>
        </div>
    </div>
@endsection

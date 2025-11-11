@extends('layouts.app')

@section('title', 'Course Assignment')

@section('content')
    <div class="main-wrapper">

        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Course Assignment</h3>
                        <p class="text-muted mb-0">Assign courses to lecturers and manage existing assignments.</p>
                    </div>
                </div>

                @include('layouts.flash-message')

                <!-- Assignment Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Assign Course to Lecturer</div>
                    <div class="card-body">
                        <form action="{{ route('staff.course.assign') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label for="course_id" class="form-label">Select Course</label>
                                <select id="course_id" name="course_id" class="form-select select2" required data-placeholder="Select a course">
    <option value=""></option>
    @foreach($courses as $course)
        <option value="{{ $course->id }}">
            {{ $course->course_code }} - {{ $course->course_title }}
        </option>
    @endforeach
</select>

                            </div>

                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Select Lecturer</label>
                               <select id="user_id" name="user_id" class="form-select select2" required data-placeholder="Select a lecturer">
    <option value=""></option>
    @foreach($lecturers as $lecturer)
        <option value="{{ $lecturer->id }}">
            {{ $lecturer->first_name }} {{ $lecturer->last_name }}
        </option>
    @endforeach
</select>

                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-success">Assign Course</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Assigned Courses Table -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">Assigned Courses</div>
                    <div class="card-body">
                        <table id="assignmentTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Title</th>
                                    <th>Lecturer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignments as $index => $assignment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assignment->course_code }}</td>
                                        <td>{{ $assignment->course_title }}</td>
                                        <td>{{ $assignment->first_name }} {{ $assignment->last_name }}</td>
                                        <td>
                                            <form action="{{ route('staff.course.assign.delete', $assignment->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this assignment?')">
                                                    Delete
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
<!-- DataTables & Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#assignmentTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true
    });

    // Initialize Select2 for searchable dropdowns
    $('#course_id, #user_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: function(){
            $(this).data('placeholder');
        },
        allowClear: true
    });
});
</script>
@endpush


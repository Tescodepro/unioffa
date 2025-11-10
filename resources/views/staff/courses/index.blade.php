@extends('layouts.app')

@section('title', 'Courses')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <div class="d-flex justify-content-between mb-3">
                    <h3>Course List</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add
                        Course</button>
                </div>

                @include('layouts.flash-message')

                <table id="coursesTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Title</th>
                            <th>Course Code</th>
                            <th>Unit</th>
                            <th>Department</th>
                            <th>Other Departments</th>
                            <th>Level</th>
                            <th>Semester</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $index => $course)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $course->course_title }}</td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_unit }}</td>
                                <td>{{ $course->department->department_name ?? 'N/A' }}</td>

                                <!-- ✅ Other Departments Column -->
                                <td>
                            @if(!empty($course->other_departments))
                                @php
                                    $otherDeptIds = json_decode($course->other_departments, true) ?? [];
                                    $otherDeptNames = \App\Models\Department::whereIn('id', $otherDeptIds)
                                                        ->pluck('department_name')
                                                        ->toArray();
                                @endphp
                                @if(count($otherDeptNames) > 0)
                                    <span>{{ implode(', ', $otherDeptNames) }}</span>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>

                                <td>{{ $course->level }}</td>
                                <td>
                                    @if ($course->semester == '1st')
                                        First Semester
                                    @elseif($course->semester == '2nd')
                                        Second Semester
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $course->active_for_register ? 'Yes' : 'No' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editCourseModal{{ $course->id }}">Edit</button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCourseModal{{ $course->id }}">Delete</button>
                                </td>
                            </tr>

                            @include('staff.courses.modals.edit_course_modal', ['course' => $course])
                            @include('staff.courses.modals.delete_course_modal', ['course' => $course])
                        @endforeach
                    </tbody>
                </table>


                @include('staff.courses.modals.add_course_modal')

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            const table = $('#coursesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-secondary'
                    }
                ],
                responsive: true
            });

            $('#filterDepartment').on('change', function() {
                table.column(4).search(this.value).draw();
            });
            $('#filterSemester').on('change', function() {
                table.column(6).search(this.value).draw();
            });
            $('#filterActive').on('change', function() {
                table.column(7).search(this.value).draw();
            });
        });
    </script>
@endpush

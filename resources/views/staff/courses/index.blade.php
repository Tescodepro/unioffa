@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header" style="display: inline">
                    <div class="row align-items-center mb-4">
                        <div class="col-sm-6">
                            <h3 class="page-title"><i class="fas fa-book-open text-primary me-2"></i>Course Management</h3>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                <i class="fas fa-plus me-1"></i> Add New Course
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                @include('layouts.flash-message')

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom-0 pb-0 pt-4 px-4">
                                <h5 class="card-title mb-0">List of Courses</h5>
                            </div>
                            <!-- Filters -->
                            <div class="card-body px-4 pb-0 pt-3">
                                <div class="row g-3">
                                    <div class="col-md-3 col-sm-6">
                                        <input type="text" id="filterCourseTitle" class="form-control" placeholder="Search Course Title">
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <input type="text" id="filterCourseCode" class="form-control" placeholder="Search Course Code">
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <select id="filterDepartment" class="form-select">
                                            <option value="">All Departments</option>
                                            @foreach(\App\Models\Department::orderBy('department_name')->get() as $dept)
                                                <option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <select id="filterLevel" class="form-select">
                                            <option value="">All Levels</option>
                                            <option value="100L">100 Level</option>
                                            <option value="200L">200 Level</option>
                                            <option value="300L">300 Level</option>
                                            <option value="400L">400 Level</option>
                                            <option value="500L">500 Level</option>
                                            <option value="600L">600 Level</option>
                                            <option value="700L">700 Level</option>
                                            <option value="800L">800 Level</option>
                                            <option value="900L">900 Level</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <select id="filterSemester" class="form-select">
                                            <option value="">All Semesters</option>
                                            <option value="First">First Semester</option>
                                            <option value="Second">Second Semester</option>
                                            <option value="Third">Third Semester</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-4 pb-4 mt-2">
                                <div class="table-responsive">
                                    <table id="coursesTable" class="table table-hover table-center mb-0 w-100 align-middle">
                                        <thead class="table-light">
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
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($courses as $index => $course)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="fw-semibold">{{ $course->course_title }}</td>
                                                    <td><span class="badge bg-light text-dark border">{{ $course->course_code }}</span></td>
                                                    <td>{{ $course->course_unit }}</td>
                                                    <td>{{ $course->department->department_name ?? 'N/A' }}</td>
                                                    
                                                    <!-- ✅ Other Departments Column -->
                                                    <td>
                                                        @php
                                                            $otherDeptIds = $course->other_departments ?? [];
                                                            $otherDeptNames = \App\Models\Department::whereIn('id', $otherDeptIds)
                                                                ->pluck('department_name')
                                                                ->toArray();
                                                        @endphp

                                                        @if(count($otherDeptNames) > 0)
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach($otherDeptNames as $deptName)
                                                                    <span class="badge bg-secondary rounded-pill fw-normal">{{ $deptName }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-muted fst-italic">None</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ $course->level }}L</td>
                                                    <td>
                                                        @if ($course->semester == '1st')
                                                            <span class="badge bg-success-light text-success"><i class="fas fa-sun me-1"></i> First</span>
                                                        @elseif($course->semester == '2nd')
                                                            <span class="badge bg-info-light text-info"><i class="fas fa-leaf me-1"></i> Second</span>
                                                        @else
                                                            <span class="badge bg-secondary-light text-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($course->active_for_register)
                                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Yes</span>
                                                        @else
                                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> No</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="actions d-flex justify-content-end gap-2">
                                                            <button class="btn btn-sm bg-warning-light text-warning" data-bs-toggle="modal"
                                                                data-bs-target="#editCourseModal{{ $course->id }}" title="Edit Course">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm bg-danger-light text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteCourseModal{{ $course->id }}" title="Delete Course">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @include('staff.courses.modals.edit_course_modal', ['course' => $course])
                                                @include('staff.courses.modals.delete_course_modal', ['course' => $course])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('staff.courses.modals.add_course_modal')

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables & Buttons -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#coursesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true
            });

            $('#filterCourseTitle').on('keyup', function () {
                table.column(1).search(this.value).draw();
            });
            $('#filterCourseCode').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });
            $('#filterDepartment').on('change', function () {
                table.column(4).search(this.value).draw();
            });
            $('#filterLevel').on('change', function () {
                table.column(6).search(this.value).draw();
            });
            $('#filterSemester').on('change', function () {
                table.column(7).search(this.value).draw();
            });
            $('#filterActive').on('change', function () {
                table.column(8).search(this.value).draw();
            });
        });
    </script>
@endpush
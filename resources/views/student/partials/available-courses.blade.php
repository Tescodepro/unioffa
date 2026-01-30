<!-- Available Courses -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
        <h4 class="mb-3">Available Courses</h4>
    </div>
    <div class="card-body p-0 py-3">
        <form method="POST" action="{{ route('students.course.registration') }}" id="courseForm">
            @csrf
            <div class="custom-datatable-filter table-responsive">
                <!-- Search Box Removed -->

                <table class="table datatable align-middle" id="availableCoursesTable">
                    <thead class="thead-light">
                        <tr>
                            <th class="no-sort">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th>Course Code</th>
                            <th>Title</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input course-checkbox" type="checkbox" name="courses[]"
                                            value="{{ $course->id }}">
                                    </div>
                                </td>
                                <td class="searchable-cell">{{ $course->course_code }}</td>
                                <td class="searchable-cell">{{ $course->course_title }}</td>
                                <td>{{ $course->course_unit }}</td>
                                <td>{{ $course->course_status ?? 'N/A' }}</td>
                                <td>
                                    @if ($course->semester == '1st')
                                        <span class="badge bg-primary">First Semester</span>
                                    @else
                                        <span class="badge bg-info">Second Semester</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No courses available for your department and level.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success">
                    <i class="ti ti-check me-1"></i> Register Selected Courses
                </button>
            </div>
        </form>
    </div>
</div>
<!-- /Available Courses -->

<!-- Registered Courses -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
        <h4 class="mb-3">Registered Courses</h4>
        <a href="{{ route('students.course.download') }}" class="btn btn-primary mb-4" target="_blank">
            <i class="ti ti-printer me-2"></i> Download Course Form
        </a>
    </div>
    <div class="card-body p-0 py-3">
        <div class="custom-datatable-filter table-responsive">
            <!-- Search Box Removed -->

            <table class="table datatable align-middle" id="registeredCoursesTable">
                <thead class="thead-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Semester</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registeredCourses as $course)
                        <tr>
                            <td class="searchable-cell">{{ $course->course->course_code }}</td>
                            <td class="searchable-cell">{{ $course->course->course_title }}</td>
                            <td>{{ $course->course->course_unit }}</td>
                            <td>
                                <span
                                    class="badge bg-{{ $course->status === 'approved' ? 'success' : ($course->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($course->semester == '1st')
                                    <span class="badge bg-primary">First Semester</span>
                                @else
                                    <span class="badge bg-info">Second Semester</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-course-btn"
                                    data-course-id="{{ $course->id }}" data-course-code="{{ $course->course->course_code }}"
                                    data-course-title="{{ $course->course->course_title }}"
                                    data-course-unit="{{ $course->course->course_unit }}" data-bs-toggle="modal"
                                    data-bs-target="#removeConfirmModal">
                                    <i class="ti ti-trash me-1"></i> Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                You have not registered any course yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /Registered Courses -->

<!-- Remove Course Confirmation Modal -->
<div class="modal fade" id="removeConfirmModal" tabindex="-1" aria-labelledby="removeConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="removeConfirmLabel">
                    <i class="ti ti-alert-triangle me-2"></i> Remove Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Are you sure you want to remove this course from your registration?</p>

                <div class="alert alert-light border-2 border-danger">
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Course Code:</strong>
                        </div>
                        <div class="col-7">
                            <span id="modalCourseCode">-</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Course Title:</strong>
                        </div>
                        <div class="col-7">
                            <span id="modalCourseTitle">-</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <strong>Course Unit:</strong>
                        </div>
                        <div class="col-7">
                            <span id="modalCourseUnit" class="badge bg-primary">-</span>
                        </div>
                    </div>
                </div>

                <p class="text-danger mb-0">
                    <i class="ti ti-info-circle me-1"></i> This action cannot be undone. You will need to register
                    again if you change your mind.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i> Cancel
                </button>
                <form id="removeForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i> Remove Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Remove Course Confirmation Modal -->

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTables
            var availableTable = $('#availableCoursesTable').DataTable({
                paging: true,
                searching: true, // Enable DataTables search
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });

            var registeredTable = $('#registeredCoursesTable').DataTable({
                paging: true,
                searching: true, // Enable DataTables search
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: 5
                }]
            });

            // Custom search for available courses - REMOVED

            // Custom search for registered courses - REMOVED

            // Select all checkbox
            $('#select-all').on('click', function () {
                var isChecked = this.checked;
                $('#availableCoursesTable tbody .course-checkbox').prop('checked', isChecked);
            });

            // Update select-all checkbox based on individual checkboxes
            $(document).on('change', '#availableCoursesTable tbody .course-checkbox', function () {
                var totalCheckboxes = $('#availableCoursesTable tbody .course-checkbox').length;
                var checkedCheckboxes = $('#availableCoursesTable tbody .course-checkbox:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes >
                    0);
            });

            // Remove course modal handler
            $('.remove-course-btn').on('click', function () {
                var courseId = $(this).data('course-id');
                var courseCode = $(this).data('course-code');
                var courseTitle = $(this).data('course-title');
                var courseUnit = $(this).data('course-unit');

                // Populate modal with course details
                $('#modalCourseCode').text(courseCode);
                $('#modalCourseTitle').text(courseTitle);
                $('#modalCourseUnit').text(courseUnit);

                // Set form action
                var form = $('#removeForm');
                form.attr('action', '{{ route('students.course.remove', ':id') }}'.replace(':id',
                    courseId));
            });

            // Form submission validation
            $('#courseForm').on('submit', function (e) {
                var checkedCount = $('input[name="courses[]"]:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one course before registering.');
                    return false;
                }
            });
        });
    </script>
@endpush
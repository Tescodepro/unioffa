<!-- Available Courses -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
        <h4 class="mb-3">Available Courses</h4>
    </div>
    <div class="card-body p-0 py-3">
        @if(isset($courseRegistrationSetting) && now()->gt($courseRegistrationSetting->closing_date) && !$hasPaidLateFee)
            <div class="p-4 text-center">
                <div class="mb-3">
                    <i class="ti ti-alert-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-danger">Registration Period Closed</h5>
                <p class="text-muted mb-4">The deadline for course registration has passed. You are required to pay a late registration fee of <strong>₦{{ number_format($courseRegistrationSetting->late_registration_fee, 2) }}</strong> to unlock course registration.</p>
                
                <form method="POST" action="{{ route('application.payment.process') }}">
                    @csrf
                    <input type="hidden" name="fee_type" value="late_course_registration">
                    <input type="hidden" name="amount" value="{{ $courseRegistrationSetting->late_registration_fee }}">
                    
                    <div class="d-flex justify-content-center mb-3">
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input" type="radio" name="payment_method" id="paystack" value="paystack" checked>
                            <label class="form-check-label fw-bold" for="paystack">Pay with Paystack</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_method" id="monnify" value="monnify">
                            <label class="form-check-label fw-bold" for="monnify">Pay with Monnify</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="ti ti-credit-card me-2"></i> Pay Late Registration Fee
                    </button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('students.course.registration') }}" id="courseForm">
                @csrf
                <div class="custom-datatable-filter table-responsive">
                    <!-- Search Box Removed -->

                    <table class="table align-middle" id="availableCoursesTable">
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
                                @php
                                    $isCarryOver = in_array($course->id, $failedCourseIds ?? []);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input course-checkbox" type="checkbox" name="courses[]"
                                                value="{{ $course->id }}" {{ $isCarryOver ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="searchable-cell">
                                        {{ $course->course_code }}
                                        @if($isCarryOver)
                                            <span class="badge bg-danger ms-1">Carry Over</span>
                                        @endif
                                    </td>
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
                <div class="mt-3 text-end px-3">
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i> Register Selected Courses
                    </button>
                </div>
            </form>
        @endif
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

            <table class="table align-middle" id="registeredCoursesTable">
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
                            <td>{{ $course->course->course_status ?? 'N/A' }}</td>
                            <td>
                                @if ($course->semester == '1st')
                                    <span class="badge bg-primary">First Semester</span>
                                @else
                                    <span class="badge bg-info">Second Semester</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-course-btn"
                                    onclick="confirmDelete('{{ $course->id }}', '{{ $course->course->course_code }}', '{{ $course->course->course_title }}', '{{ $course->course->course_unit }}')">
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
            // Initialize DataTables only if not already initialized
            if (!$.fn.DataTable.isDataTable('#availableCoursesTable')) {
                $('#availableCoursesTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    columnDefs: [{
                        orderable: false,
                        targets: 0
                    }]
                });
            }

            if (!$.fn.DataTable.isDataTable('#registeredCoursesTable')) {
                $('#registeredCoursesTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    columnDefs: [{
                        orderable: false,
                        targets: 5
                    }]
                });
            }

            // Select all checkbox
            $('#select-all').on('click', function () {
                var isChecked = this.checked;
                $('.course-checkbox').prop('checked', isChecked);
            });

            // Update select-all checkbox based on individual checkboxes
            $(document).on('change', '.course-checkbox', function () {
                var totalCheckboxes = $('.course-checkbox').length;
                var checkedCheckboxes = $('.course-checkbox:checked').length;
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
            });

            // Form submission validation
            $('#courseForm').on('submit', function (e) {
                var checkedCount = $('.course-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select at least one course before registering.',
                    });
                    return false;
                }
            });
        });

        function confirmDelete(id, code, title, unit) {
            $('#modalCourseCode').text(code);
            $('#modalCourseTitle').text(title);
            $('#modalCourseUnit').text(unit);

            var url = "{{ route('students.course.remove', ':id') }}";
            url = url.replace(':id', id);
            $('#removeForm').attr('action', url);

            var modalEl = document.getElementById('removeConfirmModal');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    </script>

@endpush
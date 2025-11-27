<!-- Available Courses -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
        <h4 class="mb-3">Available Courses</h4>
    </div>
    <div class="card-body p-0 py-3">
        <form method="POST" action="{{ route('students.course.registration') }}">
            @csrf
            <div class="custom-datatable-filter table-responsive">
                <table class="table datatable align-middle">
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
                                        <input class="form-check-input" type="checkbox" name="courses[]"
                                            value="{{ $course->id }}">
                                    </div>
                                </td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_title }}</td>
                                <td>{{ $course->course_unit }}</td>
                                <td>{{ $course->course_status ?? 'N/A' }}</td>
                                <td>
                                    @if ($course->semester == '1st')
                                        First Semester
                                    @else
                                        Second Semester
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
        <a href="{{ route('students.course.download') }}" class="btn btn-primary btn-icon mb-4"
            target="_blank">
            <i class="ti ti-printer me-2"></i> Download Course Form
        </a>
    </div>
    <div class="card-body p-0 py-3">
        <div class="custom-datatable-filter table-responsive">
            <table class="table datatable align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Title</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registeredCourses as $course)
                        <tr>
                            <td>{{ $course->course->course_code }}</td>
                            <td>{{ $course->course->course_title }}</td>
                            <td>{{ $course->course->course_unit }}</td>
                            <td>{{ $course->course->course_status ?? 'N/A' }}</td>
                            <td>
                                    @if ($course->semester == '1st')
                                        First Semester
                                    @else
                                        Second Semester
                                    @endif
                                </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
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

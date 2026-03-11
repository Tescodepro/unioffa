<div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg form-style-modern">
            <form action="{{ route('staff.courses.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark border-bottom-0 pb-3">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-edit me-2"></i>Edit Course - {{ $course->course_code }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @include('staff.courses.partials.course_form', ['course' => $course])
                </div>
                <div class="modal-footer bg-light border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-dark">
                        <i class="fas fa-save me-1"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

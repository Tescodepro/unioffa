<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg form-style-modern">
            <form action="{{ route('staff.courses.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-bottom-0 pb-3">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-plus-circle me-2"></i>Add New Course
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @include('staff.courses.partials.course_form')
                </div>
                <div class="modal-footer bg-light border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1"></i> Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

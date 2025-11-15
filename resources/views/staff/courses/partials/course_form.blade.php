<div class="row g-3">

    <!-- Course Title & Course Code -->
    <div class="col-md-6">
        <label for="course_title" class="form-label">Course Title</label>
        <input type="text" id="course_title" name="course_title" class="form-control"
            value="{{ $course->course_title ?? '' }}" required>
    </div>
    <div class="col-md-6">
        <label for="course_code" class="form-label">Course Code</label>
        <input type="text" id="course_code" name="course_code" class="form-control"
            value="{{ $course->course_code ?? '' }}" required>
    </div>

    <!-- Course Unit & Status -->
    <div class="col-md-6">
        <label for="course_unit" class="form-label">Course Unit</label>
        <input type="number" id="course_unit" name="course_unit" class="form-control"
            value="{{ $course->course_unit ?? '' }}" required>
    </div>
    <div class="col-md-6">
        <label for="course_status" class="form-label">Course Status</label>
        <input type="text" id="course_status" name="course_status" class="form-control"
            value="{{ $course->course_status ?? '' }}">
    </div>

    <!-- Primary Department & Other Departments -->
    <div class="col-md-6">
        <label for="department_id" class="form-label">Department (Primary)</label>
        <select id="department_id" name="department_id" class="form-select" required>
            <option value="">Select Department</option>
            @foreach (\App\Models\Department::all() as $dept)
                <option value="{{ $dept->id }}"
                    {{ isset($course) && $course->department_id == $dept->id ? 'selected' : '' }}>
                    {{ $dept->department_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="other_departments" class="form-label">Open to Other Departments (Optional)</label>
        <select id="other_departments" name="other_departments[]" class="form-select" multiple>
            @foreach (\App\Models\Department::all() as $dept)
                <option value="{{ $dept->id }}" @if (isset($course) && in_array($dept->id, $course->other_departments ?? [])) selected @endif>
                    {{ $dept->department_name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold CTRL (or CMD on Mac) to select multiple departments</small>
    </div>


    <!-- Level & Semester -->
    <div class="col-md-6">
        <label for="level" class="form-label">Level</label>
        <input type="number" id="level" name="level" class="form-control" value="{{ $course->level ?? '' }}"
            required>
    </div>
    <div class="col-md-6">
        <label for="semester" class="form-label">Semester</label>
        <select id="semester" name="semester" class="form-select" required>
            @foreach (['1st', '2nd', '3rd', '4th', '5th', '6th'] as $sem)
                <option value="{{ $sem }}"
                    {{ isset($course) && $course->semester == $sem ? 'selected' : '' }}>
                    {{ ucfirst($sem) }} Semester
                </option>
            @endforeach
        </select>
    </div>

    <!-- Active for Registration -->
    <div class="col-md-6">
        <label for="active_for_register" class="form-label">Active for Registration</label>
        <select id="active_for_register" name="active_for_register" class="form-select" required>
            <option value="1" {{ isset($course) && $course->active_for_register ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ isset($course) && !$course->active_for_register ? 'selected' : '' }}>No</option>
        </select>
    </div>

</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('students.course.registration') }}" method="GET" id="filterForm">
            <div class="row align-items-end g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">Department</label>
                    <select name="department_id" class="form-select select2" onchange="this.form.submit()">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Level</label>
                    <select name="level" class="form-select" onchange="this.form.submit()">
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}" {{ $selectedLevel == $lvl ? 'selected' : '' }}>
                                {{ $lvl }} Level
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('students.course.registration') }}" class="btn btn-light w-100">
                        <i class="ti ti-refresh me-1"></i> Reset Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- /Filter Form -->

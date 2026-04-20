@php
    $caller = auth()->user();
    $isDean = $caller->hasRole('dean');
    $isHOD = $caller->hasRole('hod');
    $isAdmin = $caller->hasRole('administrator');
    
    // Determine pre-selected values
    $selectedFacultyId = isset($staff) ? $staff->faculty_id : ($isDean ? $caller->staff->faculty_id : null);
    $selectedDepartmentId = isset($staff) ? $staff->department_id : ($isHOD ? $caller->staff->department_id : null);
@endphp


<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $staff->user->first_name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $staff->user->last_name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control" value="{{ old('username', $staff->user->username ?? '') }}"  required>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $staff->user->email ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->user->phone ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">User Type <span class="text-danger">*</span></label>
            <select name="user_type_id" class="form-select" required>
                <option value="">Select User Type</option>
                @foreach($userTypes as $type)
                    <option value="{{ $type->id }}" {{ old('user_type_id', $staff->user->user_type_id ?? '') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Faculty <span class="text-danger">*</span></label>
            <select name="faculty_id" class="form-select" required {{ ($isDean || $isHOD) ? 'disabled' : '' }}>
                <option value="">Select Faculty</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ old('faculty_id', $selectedFacultyId) == $faculty->id ? 'selected' : '' }}>
                        {{ $faculty->faculty_name }}
                    </option>
                @endforeach
            </select>
            @if($isDean || $isHOD)
                <input type="hidden" name="faculty_id" value="{{ $selectedFacultyId }}">
            @endif
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
            <select name="department_id" class="form-select" required {{ $isHOD ? 'disabled' : '' }}>
                <option value="">Select Department</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $selectedDepartmentId) == $department->id ? 'selected' : '' }}>
                        {{ $department->department_name }}
                    </option>
                @endforeach
            </select>
            @if($isHOD)
                <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
            @endif
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                <option value="active" {{ old('status', $staff->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $staff->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</div>

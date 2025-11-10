<div class="row g-3">

    <!-- First Name -->
    <div class="col-md-6">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" id="first_name" name="first_name" class="form-control form-control-sm" 
               value="{{ $staff->user->first_name ?? '' }}" placeholder="Enter first name" required>
    </div>

    <!-- Last Name -->
    <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" id="last_name" name="last_name" class="form-control form-control-sm" 
               value="{{ $staff->user->last_name ?? '' }}" placeholder="Enter last name" required>
    </div>

    <!-- Username -->
    <div class="col-md-6">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control form-control-sm"
               value="{{ $staff->user->username ?? '' }}" placeholder="Enter username" required>
    </div>

    <!-- Email -->
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control form-control-sm" 
               value="{{ $staff->user->email ?? '' }}" placeholder="Enter email address" required>
    </div>

    <!-- Phone -->
    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" id="phone" name="phone" class="form-control form-control-sm" 
               value="{{ $staff->user->phone ?? '' }}" placeholder="Enter phone number" required>
    </div>

    <!-- Faculty -->
    <div class="col-md-6">
        <label for="faculty_id" class="form-label">Faculty</label>
        <select id="faculty_id" name="faculty_id" class="form-select form-select-sm" required>
            <option value="">Select Faculty</option>
            @foreach(\App\Models\Faculty::all() as $faculty)
                <option value="{{ $faculty->id }}" 
                    {{ isset($staff) && $staff->faculty_id == $faculty->id ? 'selected' : '' }}>
                    {{ $faculty->faculty_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Department -->
    <div class="col-md-6">
        <label for="department_id" class="form-label">Department</label>
        <select id="department_id" name="department_id" class="form-select form-select-sm" required>
            <option value="">Select Department</option>
            @foreach(\App\Models\Department::all() as $department)
                <option value="{{ $department->id }}" 
                    {{ isset($staff) && $staff->department_id == $department->id ? 'selected' : '' }}>
                    {{ $department->department_name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- User Type -->
    <div class="col-md-6">
        <label for="user_type_id" class="form-label">User Type</label>
        <select id="user_type_id" name="user_type_id" class="form-select form-select-sm" required>
            <option value="">Select User Type</option>
            @foreach(\App\Models\UserType::whereIn('name', ['hod','lecturer','dean'])->get() as $type)
                <option value="{{ $type->id }}" 
                    {{ isset($staff) && $staff->user->user_type_id == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Status -->
    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select form-select-sm" required>
            <option value="active" {{ isset($staff) && $staff->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ isset($staff) && $staff->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

</div>

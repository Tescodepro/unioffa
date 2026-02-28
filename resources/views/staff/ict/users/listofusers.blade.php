@extends('layouts.app')
@section('title', 'List of Users - ICT Management')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                @include('layouts.flash-message')

                <!-- Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">List of Users</h3>
                        <p class="text-muted mb-0">View, filter, and manage all user accounts.</p>
                    </div>
                    @if(auth()->user()->userType->name == 'ict')
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="ti ti-plus"></i> Add User
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Pre-filter Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('ict.staff.users.index') }}" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="user_type_id" class="form-select">
                                    <option value="">All Roles</option>
                                    @foreach ($userTypes as $type)
                                        <option value="{{ $type->id }}" {{ request('user_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ ucfirst($type->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Campus / Center</label>
                                <select name="campus_id" class="form-select">
                                    <option value="">All Campuses</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('ict.staff.users.index') }}" class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle" id="users-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Campus / Center</th>
                                    <th>Status</th>
                                    @if(auth()->user()->userType->name == 'ict')
                                        <th class="text-end">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? '—' }}</td>
                                        <td>{{ $user->userType->name ?? '—' }}</td>
                                        <td>{{ $user->campus->name ?? '—' }}</td>
                                        <td>
                                            @if ($user->trashed())
                                                <span class="badge bg-danger">Disabled</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->userType->name == 'ict')
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary me-1 editUserBtn"
                                                    data-id="{{ $user->id }}"
                                                    data-username="{{ $user->username }}"
                                                    data-first_name="{{ $user->first_name }}"
                                                    data-middle_name="{{ $user->middle_name }}"
                                                    data-last_name="{{ $user->last_name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}"
                                                    data-user_type_id="{{ $user->user_type_id }}"
                                                    data-campus_id="{{ $user->campus_id }}"
                                                    data-app_types="{{ $user->assignedApplicationTypes ? json_encode($user->assignedApplicationTypes->pluck('application_setting_id')->toArray()) : '[]' }}"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>

                                                @if ($user->trashed())
                                                    <button type="button" class="btn btn-sm btn-outline-success me-1 confirm-action-btn"
                                                        data-url="{{ route('ict.staff.users.enable', $user->id) }}"
                                                        data-method="POST"
                                                        data-type="enable"
                                                        data-title="Enable User Account"
                                                        data-name="{{ $user->first_name }} {{ $user->last_name }} ({{ $user->username }})"
                                                        data-message="This user will be re-activated and will be able to log in and use the system again."
                                                        data-btn-class="btn-success"
                                                        data-btn-text="Yes, Enable"
                                                        title="Enable user">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-warning me-1 confirm-action-btn"
                                                        data-url="{{ route('ict.staff.users.disable', $user->id) }}"
                                                        data-method="POST"
                                                        data-type="disable"
                                                        data-title="Disable User Account"
                                                        data-name="{{ $user->first_name }} {{ $user->last_name }} ({{ $user->username }})"
                                                        data-message="This user will be suspended and will no longer be able to log in. Their data will be preserved and the account can be re-enabled at any time."
                                                        data-btn-class="btn-warning"
                                                        data-btn-text="Yes, Disable"
                                                        title="Disable user">
                                                        <i class="ti ti-ban"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-danger confirm-action-btn"
                                                    data-url="{{ route('ict.staff.users.destroy', $user->id) }}"
                                                    data-method="DELETE"
                                                    data-type="delete"
                                                    data-title="Permanently Delete User"
                                                    data-name="{{ $user->first_name }} {{ $user->last_name }} ({{ $user->username }})"
                                                    data-message="<ul class='mb-0 mt-1 ps-3'><li>The user account will be <strong>wiped from the database</strong> entirely.</li><li>All login access will be <strong>immediately revoked</strong>.</li><li>Any linked records assignments, activity logs, payments, all records may be affected.</li><li>This action <strong>cannot be undone</strong>.</li></ul>"
                                                    data-btn-class="btn-danger"
                                                    data-btn-text="Yes, Delete Permanently"
                                                    title="Delete permanently">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="editUserForm">
                @csrf
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="editUserModalLabel">
                            <i class="ti ti-edit fs-5"></i> Edit User Account
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="edit_user_id" name="user_id">

                        {{-- Login credentials --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Login Credentials</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                            <div class="form-text">Used to log in to the system.</div>
                        </div>

                        <hr class="my-3">

                        {{-- Full name --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Personal Details</p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Access control --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Access & Assignment</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="user_type_id" id="user_type_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach ($userTypes as $type)
                                        <option value="{{ $type->id }}">{{ ucfirst($type->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Determines what this user can access.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Campus / Center</label>
                                <select name="campus_id" id="edit_campus_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Required for center-director role.</div>
                            </div>
                            
                            {{-- Programme Director Application Types --}}
                            <div class="col-md-12 prog-dir-app-types" style="display: none;">
                                <label class="form-label fw-semibold mt-2">Assigned Application Types <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-2">
                                        @foreach (\App\Models\ApplicationSetting::all() as $appType)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input edit-app-type-cb" type="checkbox" name="application_types[]" value="{{ $appType->id }}" id="edit_app_type_{{ $appType->id }}">
                                                    <label class="form-check-label" for="edit_app_type_{{ $appType->id }}">
                                                        {{ $appType->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-text text-muted mt-2">Check the entry modes this Programme Director is allowed to manage.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>Save Changes
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('ict.staff.users.index') }}">
                @csrf
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="addUserModalLabel">
                            <i class="ti ti-user-plus fs-5"></i> Add New User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Login credentials --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Login Credentials</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                            <div class="form-text">Used to log in. Must be unique across the system.</div>
                        </div>

                        <hr class="my-3">

                        {{-- Full name --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Personal Details</p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Access control --}}
                        <p class="text-muted small fw-semibold text-uppercase mb-2">Access & Assignment</p>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="user_type_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach ($userTypes as $type)
                                        <option value="{{ $type->id }}">{{ ucfirst($type->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Determines what this user can access.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Campus / Center</label>
                                <select name="campus_id" class="form-select" id="add_campus_id">
                                    <option value="">— None —</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Required for center-director role.</div>
                            </div>

                            {{-- Programme Director Application Types --}}
                            <div class="col-md-12 prog-dir-app-types" style="display: none;">
                                <label class="form-label fw-semibold mt-2">Assigned Application Types <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row g-2">
                                        @foreach (\App\Models\ApplicationSetting::all() as $appType)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="application_types[]" value="{{ $appType->id }}" id="add_app_type_{{ $appType->id }}">
                                                    <label class="form-check-label" for="add_app_type_{{ $appType->id }}">
                                                        {{ $appType->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-text text-muted mt-2">Check the entry modes this Programme Director is allowed to manage.</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-start gap-2 mb-0 py-2">
                            <i class="ti ti-lock fs-5 mt-1 flex-shrink-0"></i>
                            <div>
                                <strong>Default password:</strong> <code>password123</code><br>
                                <small class="text-muted">The user should change this immediately after their first login.</small>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-user-plus me-1"></i>Add User
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="confirmationForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodInput" value="POST">

                    <div class="modal-header" id="confirmationModalHeader">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i id="confirmationModalIcon" class="ti ti-help-circle fs-5"></i>
                            <span id="confirmationModalTitle">Confirm Action</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="fw-semibold mb-1" id="confirmationModalName"></p>
                        <div id="confirmationModalBody" class="text-muted small">Are you sure you want to proceed?</div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="confirmationModalBtn">Confirm</button>
                    </div>
                </form>
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
            $('#users-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                searching: true,
                ordering: true,
                info: true,
            });
        });

        document.addEventListener('DOMContentLoaded', function () {

            // Edit User Modal Logic
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));

            // Helper to toggle the Application Types box based on role name
            function toggleAppTypes(roleSelect, containerClass) {
                const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
                const container = document.querySelector(containerClass);
                if (selectedText === 'programme-director') {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            }

            // On Add Modal role change
            const addUserTypeSelect = document.querySelector('#addUserModal select[name="user_type_id"]');
            if (addUserTypeSelect) {
                 addUserTypeSelect.addEventListener('change', function() {
                     toggleAppTypes(this, '#addUserModal .prog-dir-app-types');
                 });
            }

            // On Edit Modal role change
            const editUserTypeSelect = document.getElementById('user_type_id');
            if(editUserTypeSelect) {
                editUserTypeSelect.addEventListener('change', function() {
                     toggleAppTypes(this, '#editUserModal .prog-dir-app-types');
                });
            }

            document.querySelectorAll('.editUserBtn').forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('edit_user_id').value   = this.dataset.id;
                    document.getElementById('username').value       = this.dataset.username;
                    document.getElementById('first_name').value     = this.dataset.first_name;
                    document.getElementById('middle_name').value    = this.dataset.middle_name  || '';
                    document.getElementById('last_name').value      = this.dataset.last_name;
                    document.getElementById('email').value          = this.dataset.email;
                    document.getElementById('phone').value          = this.dataset.phone        || '';
                    document.getElementById('user_type_id').value   = this.dataset.user_type_id;
                    document.getElementById('edit_campus_id').value = this.dataset.campus_id    || '';

                    // Trigger change to show/hide application types area
                    if(editUserTypeSelect) {
                        editUserTypeSelect.dispatchEvent(new Event('change'));
                    }

                    // Reset all application type checkboxes
                    document.querySelectorAll('.edit-app-type-cb').forEach(cb => cb.checked = false);

                    // Check assigned application types
                    if (this.dataset.app_types) {
                        const assignedTypes = JSON.parse(this.dataset.app_types);
                        assignedTypes.forEach(typeId => {
                            const cb = document.getElementById('edit_app_type_' + typeId);
                            if (cb) cb.checked = true;
                        });
                    }

                    const routeTemplate = @json(route('ict.staff.users.update', ['id' => 0]));
                    document.getElementById('editUserForm').action = routeTemplate.replace('/0', '/' + this.dataset.id);

                    editModal.show();
                });
            });

            // Confirmation Modal Logic
            const confirmationModal  = new bootstrap.Modal(document.getElementById('confirmationModal'));
            const confirmationForm   = document.getElementById('confirmationForm');
            const methodInput        = document.getElementById('methodInput');
            const modalHeader        = document.getElementById('confirmationModalHeader');
            const modalIcon          = document.getElementById('confirmationModalIcon');
            const modalTitle         = document.getElementById('confirmationModalTitle');
            const modalName          = document.getElementById('confirmationModalName');
            const modalBody          = document.getElementById('confirmationModalBody');
            const modalBtn           = document.getElementById('confirmationModalBtn');

            const typeStyles = {
                enable:  { headerClass: 'bg-success text-white', btnClose: 'btn-close-white', icon: 'ti-circle-check',  closeClass: '' },
                disable: { headerClass: 'bg-warning text-dark',  btnClose: '',                icon: 'ti-ban',            closeClass: '' },
                delete:  { headerClass: 'bg-danger text-white',  btnClose: 'btn-close-white', icon: 'ti-alert-triangle', closeClass: '' },
            };

            document.querySelectorAll('.confirm-action-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const type   = this.dataset.type   || 'default';
                    const styles = typeStyles[type] || { headerClass: '', btnClose: '', icon: 'ti-help-circle' };

                    // Update form action & method
                    confirmationForm.action = this.dataset.url;
                    methodInput.value = ['DELETE', 'PUT', 'PATCH'].includes(this.dataset.method) ? this.dataset.method : 'POST';

                    // Style the header
                    modalHeader.className = 'modal-header ' + styles.headerClass;
                    const closeBtn = modalHeader.querySelector('.btn-close');
                    closeBtn.className = 'btn-close ' + styles.btnClose;

                    // Icon
                    modalIcon.className = 'ti ' + styles.icon + ' fs-5';

                    // Content
                    modalTitle.textContent = this.dataset.title;
                    modalName.textContent  = this.dataset.name || '';
                    modalBody.innerHTML    = this.dataset.message;

                    // Button
                    modalBtn.textContent = this.dataset.btn_text || 'Confirm';
                    modalBtn.className   = 'btn ' + (this.dataset.btn_class || 'btn-primary');

                    confirmationModal.show();
                });
            });
        });
    </script>
@endpush
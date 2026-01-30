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
                <!-- Filter Form -->
                <form method="GET" action="{{ route('ict.staff.users.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" name="username" value="{{ request('username') }}" class="form-control"
                                placeholder="Username">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                                placeholder="Name">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="email" value="{{ request('email') }}" class="form-control"
                                placeholder="Email">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="phone" value="{{ request('phone') }}" class="form-control"
                                placeholder="Phone">
                        </div>
                        <div class="col-md-3">
                            <select name="user_type_id" class="form-select">
                                <option value="">All User Types</option>
                                @foreach ($userTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('user_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('ict.staff.users.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Table -->
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>User Type</th>
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
                                        @if(auth()->user()->userType->name == 'ict')
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-primary me-1 editUserBtn"
                                                    data-id="{{ $user->id }}" data-username="{{ $user->username }}"
                                                    data-first_name="{{ $user->first_name }}"
                                                    data-middle_name="{{ $user->middle_name }}"
                                                    data-last_name="{{ $user->last_name }}" data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone }}" data-user_type_id="{{ $user->user_type_id }}"
                                                    title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>

                                                @if ($user->trashed())
                                                    <button type="button" class="btn btn-sm btn-outline-success me-1 confirm-action-btn"
                                                        data-url="{{ route('ict.staff.users.enable', $user->id) }}" data-method="POST"
                                                        data-title="Enable User"
                                                        data-message="Are you sure you want to enable <strong>{{ $user->username }}</strong>?"
                                                        data-btn-class="btn-success" data-btn-text="Enable" title="Enable">
                                                        <i class="ti ti-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-warning me-1 confirm-action-btn"
                                                        data-url="{{ route('ict.staff.users.disable', $user->id) }}" data-method="POST"
                                                        data-title="Disable User"
                                                        data-message="Are you sure you want to disable <strong>{{ $user->username }}</strong>?"
                                                        data-btn-class="btn-warning" data-btn-text="Disable" title="Disable">
                                                        <i class="ti ti-ban"></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-danger confirm-action-btn"
                                                    data-url="{{ route('ict.staff.users.destroy', $user->id) }}"
                                                    data-method="DELETE" data-title="Delete User Permanently"
                                                    data-message="Are you sure you want to <strong>PERMANENTLY DELETE</strong> {{ $user->username }}? This action cannot be undone."
                                                    data-btn-class="btn-danger" data-btn-text="Delete Permanently"
                                                    title="Delete Permanently">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($users->hasPages())
                            <div class="card-footer d-flex justify-content-end">
                                {{ $users->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editUserForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <select name="user_type_id" id="user_type_id" class="form-select" required>
                                <option value="">Select User Type</option>
                                @foreach ($userTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('ict.staff.users.index') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <select name="user_type_id" class="form-select" required>
                                <option value="">Select User Type</option>
                                @foreach ($userTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
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
                    <!-- Method spoofing will be handled by JS if needed, or we can use a hidden input -->
                    <input type="hidden" name="_method" id="methodInput" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmationModalTitle">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmationModalBody">Are you sure you want to proceed?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="confirmationModalBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit User Modal Logic
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));

            document.querySelectorAll('.editUserBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const username = this.dataset.username;
                    const firstName = this.dataset.first_name;
                    const middleName = this.dataset.middle_name;
                    const lastName = this.dataset.last_name;
                    const email = this.dataset.email;
                    const phone = this.dataset.phone;
                    const userTypeId = this.dataset.user_type_id;

                    // Fill input fields
                    document.getElementById('username').value = username;
                    document.getElementById('first_name').value = firstName;
                    document.getElementById('middle_name').value = middleName;
                    document.getElementById('last_name').value = lastName;
                    document.getElementById('email').value = email;
                    document.getElementById('phone').value = phone;
                    document.getElementById('user_type_id').value = userTypeId;

                    // Set form action
                    const routeTemplate = @json(route('ict.staff.users.update', ['id' => 0]));
                    const form = document.getElementById('editUserForm');
                    form.action = routeTemplate.replace('/0', '/' + id);

                    editModal.show();
                });
            });

            // Confirmation Modal Logic
            const confirmationModalEl = document.getElementById('confirmationModal');
            const confirmationModal = new bootstrap.Modal(confirmationModalEl);
            const confirmationForm = document.getElementById('confirmationForm');
            const methodInput = document.getElementById('methodInput');
            const modalTitle = document.getElementById('confirmationModalTitle');
            const modalBody = document.getElementById('confirmationModalBody');
            const modalBtn = document.getElementById('confirmationModalBtn');

            document.querySelectorAll('.confirm-action-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const url = this.dataset.url;
                    const method = this.dataset.method; // POST, DELETE, etc.
                    const title = this.dataset.title;
                    const message = this.dataset.message;
                    const btnClass = this.dataset.btn_class || 'btn-primary';
                    const btnText = this.dataset.btn_text || 'Confirm';

                    // Set Form Action
                    confirmationForm.action = url;

                    // Set Method
                    if (method === 'DELETE' || method === 'PUT' || method === 'PATCH') {
                        methodInput.value = method;
                    } else {
                        methodInput.value = 'POST';
                    }

                    // Update Content
                    modalTitle.textContent = title;
                    modalBody.innerHTML = message;
                    modalBtn.textContent = btnText;

                    // Update Button Class
                    modalBtn.className = 'btn ' + btnClass;

                    confirmationModal.show();
                });
            });
        });
    </script>
@endpush
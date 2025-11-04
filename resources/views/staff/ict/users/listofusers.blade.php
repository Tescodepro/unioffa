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

                <!-- Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">List of Users</h3>
                        <p class="text-muted mb-0">View, filter, and manage all user accounts.</p>
                    </div>
                    @include('layouts.flash-message')
                </div>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('ict.staff.users.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" name="username" value="{{ request('username') }}" class="form-control"
                                placeholder="Username">
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
                                    <option value="{{ $type->id }}"
                                        {{ request('user_type_id') == $type->id ? 'selected' : '' }}>
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
                                    <th class="text-end">Action</th>
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
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1 editUserBtn"
                                                data-id="{{ $user->id }}" data-first_name="{{ $user->first_name }}"
                                                data-middle_name="{{ $user->middle_name }}"
                                                data-last_name="{{ $user->last_name }}" data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone }}">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </td>
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
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));

    document.querySelectorAll('.editUserBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const firstName = this.dataset.first_name;
            const middleName = this.dataset.middle_name;
            const lastName = this.dataset.last_name;
            const email = this.dataset.email;
            const phone = this.dataset.phone;

            // Fill input fields
            document.getElementById('first_name').value = firstName;
            document.getElementById('middle_name').value = middleName;
            document.getElementById('last_name').value = lastName;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;

            // Generate base route (using dummy id=0)
            const routeTemplate = @json(route('ict.staff.users.update', ['id' => 0]));
            const form = document.getElementById('editUserForm');
            form.action = routeTemplate.replace('/0', '/' + id);

            editModal.show();
        });
    });
});

    </script>
@endpush

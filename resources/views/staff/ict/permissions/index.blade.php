@extends('layouts.app')
@section('title', 'Permissions')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">Permissions</h3>
                        <p class="text-muted mb-0">Create and manage system permissions</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('ict.user-types.index') }}">System</a></li>
                                <li class="breadcrumb-item active">Permissions</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                        <a href="{{ route('ict.permissions.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>New Permission
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Identifier</th>
                                    <th>Assigned To (User Types)</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td class="fw-medium">{{ $permission->name }}</td>
                                        <td><code class="text-primary">{{ $permission->identifier }}</code></td>
                                        <td>
                                            @foreach($permission->userTypes as $type)
                                                <span class="badge bg-light text-dark border me-1">{{ ucfirst($type->name) }}</span>
                                            @endforeach
                                            @if($permission->userTypes->isEmpty())
                                                <span class="text-muted small">— unassigned</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('ict.permissions.edit', $permission) }}"
                                                class="btn btn-sm btn-outline-primary me-1">
                                                <i class="ti ti-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('ict.permissions.destroy', $permission) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete this permission? It will be removed from all user types.')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No permissions defined yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
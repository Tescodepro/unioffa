@extends('layouts.app')
@section('title', 'Edit Permission')

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
                        <h3 class="page-title mb-1">Edit Permission</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('ict.permissions.index') }}">Permissions</a>
                                </li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('ict.permissions.update', $permission) }}" method="POST">
                                    @csrf @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Permission Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $permission->name) }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Identifier</label>
                                        <input type="text" name="identifier"
                                            class="form-control @error('identifier') is-invalid @enderror"
                                            value="{{ old('identifier', $permission->identifier) }}">
                                        @error('identifier')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            ⚠️ Changing the identifier will break any <code>route_permissions</code> or
                                            <code>menu_items</code> rows that reference the old value.
                                            Update those tables too after renaming.
                                        </small>
                                    </div>

                                    {{-- Show which user types have this permission --}}
                                    @if($permission->userTypes->isNotEmpty())
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">Currently assigned to</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($permission->userTypes as $type)
                                                    <span class="badge bg-light text-dark border">{{ ucfirst($type->name) }}</span>
                                                @endforeach
                                            </div>
                                            <small class="text-muted">
                                                Manage assignments via
                                                <a href="{{ route('ict.user-types.index') }}">User Types → Permissions</a>.
                                            </small>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i>Save Changes
                                        </button>
                                        <a href="{{ route('ict.permissions.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
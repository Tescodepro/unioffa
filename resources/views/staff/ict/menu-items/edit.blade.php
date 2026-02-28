@extends('layouts.app')
@section('title', 'Edit Menu Item')

@section('content')
<div id="global-loader"><div class="page-loader"></div></div>
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')
    <div class="page-wrapper">
        <div class="content">

            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="page-title mb-1">Edit Menu Item</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('ict.menu-items.index') }}">Menu Management</a></li>
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
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('ict.menu-items.update', $menuItem) }}" method="POST">
                                @csrf @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Section <span class="text-danger">*</span></label>
                                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror"
                                               value="{{ old('section', $menuItem->section) }}">
                                        @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Label <span class="text-danger">*</span></label>
                                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                                               value="{{ old('label', $menuItem->label) }}">
                                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Icon <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="{{ $menuItem->icon }}"></i></span>
                                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                                                   value="{{ old('icon', $menuItem->icon) }}">
                                        </div>
                                        @error('icon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        <small class="text-muted"><a href="https://tabler.io/icons" target="_blank">Browse Tabler Icons</a></small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Route Name <span class="text-danger">*</span></label>
                                        <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror"
                                               value="{{ old('route_name', $menuItem->route_name) }}">
                                        @error('route_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Route Pattern</label>
                                        <input type="text" name="route_pattern" class="form-control"
                                               value="{{ old('route_pattern', $menuItem->route_pattern) }}"
                                               placeholder="e.g. staff/burser/transactions*">
                                        <small class="text-muted">For active sidebar highlighting. Wildcards (* ) allowed.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Permission Required</label>
                                        <select name="permission_identifier" class="form-select">
                                            <option value="">— Any authenticated staff —</option>
                                            @foreach($permissions as $perm)
                                                <option value="{{ $perm->identifier }}"
                                                    {{ old('permission_identifier', $menuItem->permission_identifier) === $perm->identifier ? 'selected' : '' }}>
                                                    {{ $perm->name }} ({{ $perm->identifier }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">User Type Scope</label>
                                        <input type="text" name="user_type_scope" class="form-control"
                                               value="{{ old('user_type_scope', $menuItem->user_type_scope) }}"
                                               placeholder="blank = show to all">
                                        <small class="text-muted">E.g. <code>vice-chancellor</code> to show only to VC users.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Sort Order <span class="text-danger">*</span></label>
                                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                                               value="{{ old('sort_order', $menuItem->sort_order) }}" min="0">
                                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                   value="1" {{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active (visible in sidebar)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>Save Changes
                                    </button>
                                    <a href="{{ route('ict.menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
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

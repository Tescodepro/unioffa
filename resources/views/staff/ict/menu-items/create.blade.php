@extends('layouts.app')
@section('title', 'Add Menu Item')

@section('content')
<div id="global-loader"><div class="page-loader"></div></div>
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')
    <div class="page-wrapper">
        <div class="content">

            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="page-title mb-1">Add Menu Item</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('ict.menu-items.index') }}">Menu Management</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('ict.menu-items.store') }}" method="POST">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Section <span class="text-danger">*</span></label>
                                        <input type="text" name="section" class="form-control @error('section') is-invalid @enderror"
                                               value="{{ old('section') }}"
                                               placeholder="e.g. Finance, Student Management">
                                        @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">This becomes the sidebar group heading.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Label <span class="text-danger">*</span></label>
                                        <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                                               value="{{ old('label') }}" placeholder="e.g. Transactions">
                                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Icon <span class="text-danger">*</span></label>
                                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                                               value="{{ old('icon', 'ti ti-circle') }}"
                                               placeholder="e.g. ti ti-credit-card">
                                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Use <a href="https://tabler.io/icons" target="_blank">Tabler Icons</a> class name.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Route Name <span class="text-danger">*</span></label>
                                        <input type="text" name="route_name" class="form-control @error('route_name') is-invalid @enderror"
                                               value="{{ old('route_name') }}"
                                               placeholder="e.g. bursary.transactions">
                                        @error('route_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Laravel named route (must exist).</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Route Pattern</label>
                                        <input type="text" name="route_pattern" class="form-control"
                                               value="{{ old('route_pattern') }}"
                                               placeholder="e.g. staff/burser/transactions*">
                                        <small class="text-muted">Used to highlight the active sidebar link. Supports wildcards (*).</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Permission Required</label>
                                        <select name="permission_identifier" class="form-select">
                                            <option value="">— Any authenticated staff —</option>
                                            @foreach($permissions as $perm)
                                                <option value="{{ $perm->identifier }}"
                                                    {{ old('permission_identifier') === $perm->identifier ? 'selected' : '' }}>
                                                    {{ $perm->name }} ({{ $perm->identifier }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Leave blank to show to all staff.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">User Type Scope</label>
                                        <input type="text" name="user_type_scope" class="form-control"
                                               value="{{ old('user_type_scope') }}"
                                               placeholder="e.g. vice-chancellor  (blank = all)">
                                        <small class="text-muted">Restrict to a single user type even if they have the permission.</small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Sort Order <span class="text-danger">*</span></label>
                                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                                               value="{{ old('sort_order', 10) }}" min="0">
                                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <small class="text-muted">Lower numbers appear first within the section.</small>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                   value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active (visible in sidebar)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>Create Menu Item
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

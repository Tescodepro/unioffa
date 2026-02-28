@extends('layouts.app')
@section('title', 'Create Permission')

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
                        <h3 class="page-title mb-1">Create Permission</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('ict.permissions.index') }}">Permissions</a>
                                </li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('ict.permissions.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Permission Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" placeholder="e.g. View Reports">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Human-readable label shown in the UI.</small>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Identifier</label>
                                        <input type="text" name="identifier"
                                            class="form-control @error('identifier') is-invalid @enderror"
                                            value="{{ old('identifier') }}"
                                            placeholder="e.g. view_reports  (auto-generated if left blank)">
                                        @error('identifier')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Used in <code>route_permissions</code> and
                                            <code>menu_items</code> tables. Leave blank to auto-generate from the
                                            name.</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i>Create Permission
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
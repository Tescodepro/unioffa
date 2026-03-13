@extends('layouts.app')
@section('title', 'User Types')

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
                        <h3 class="page-title mb-1">User Types</h3>
                        <p class="text-muted mb-0">Manage system user roles and permissions</p>
                    </div>
                    @include('layouts.flash-message')
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user-types.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Add New User Type
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                @endif

                <!-- Table -->
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userTypes as $type)
                                    <tr>
                                        <td>{{ ucfirst($type->name) }}</td>
                                        <td>
                                            <a href="{{ route('user-types.permissions', $type->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="ti ti-lock"></i> Manage Permissions
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')
@section('title', 'Manage Permissions')

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
                        <h3 class="page-title mb-1">Manage Permissions</h3>
                        <p class="text-muted mb-0">Assign permissions for {{ ucfirst($userType->name) }}</p>
                    </div>
                    <a href="{{ route('user-types.index') }}" class="btn btn-secondary">Back</a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('user-types.permissions.update', $userType->id) }}" method="POST">
                            @csrf
                            <h5 class="card-title mb-4">Select Permissions</h5>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                                {{ $userType->permissions->contains($permission->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 border-top pt-3">
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

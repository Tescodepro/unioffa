@extends('layouts.app')

@section('title', 'Scholarship Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Scholarship Settings</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Scholarship Settings</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="mb-2">
                            <a href="{{ route('scholarship-settings.create') }}" class="btn btn-primary"><i class="ti ti-plus me-2"></i>New Setting</a>
                            <a href="{{ route('scholarship-settings.applications') }}" class="btn btn-secondary ms-2"><i class="ti ti-list me-2"></i>View Applications</a>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Academic Session</th>
                                        <th>Application Type</th>
                                        <th>Min JAMB Score</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                        <tr>
                                            <td>{{ $setting->academic_session }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $setting->application_type)) }}</td>
                                            <td>{{ $setting->min_jamb_score }}</td>
                                            <td>
                                                @if($setting->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('scholarship-settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('scholarship-settings.destroy', $setting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($settings->isEmpty())
                                        <tr>
                                            <td colspan="5" class="text-center">No scholarship settings found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

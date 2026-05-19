@extends('layouts.app')

@section('title', 'Scholarship Applications')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Scholarship Applications</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('scholarship-settings.index') }}">Settings</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Applications</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <div class="mb-2">
                            <a href="{{ route('scholarship-settings.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left me-2"></i>Back to Settings</a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Applicant Name</th>
                                        <th>Matric/Reg No</th>
                                        <th>Academic Session</th>
                                        <th>Requested %</th>
                                        <th>Form Details</th>
                                        <th>Status</th>
                                        <th>Date Applied</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td>
                                                {{ $app->user->first_name ?? '' }} {{ $app->user->last_name ?? '' }}
                                            </td>
                                            <td>{{ $app->user->username ?? 'N/A' }}</td>
                                            <td>{{ $app->setting->academic_session ?? 'N/A' }}</td>
                                            <td>{{ $app->requested_percentage }}%</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#appModal{{ $app->id }}">
                                                    View Responses
                                                </button>
                                                
                                                <!-- Modal -->
                                                <div class="modal fade" id="appModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Application Responses</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if($app->form_data && is_array($app->form_data))
                                                                    <table class="table table-bordered">
                                                                        <tbody>
                                                                            @foreach($app->form_data as $key => $value)
                                                                                <tr>
                                                                                    <th style="width: 40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                                                                    <td>{{ $value }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                @else
                                                                    <p>No additional responses.</p>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $app->status == 'pending' ? 'warning' : ($app->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($app->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $app->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                    @if($applications->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center">No applications received yet.</td>
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

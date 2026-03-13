@extends('layouts.app')
@section('title', 'Center Director — Dashboard')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Dashboard</h3>
                        <p class="text-muted mb-0">
                            <i class="ti ti-map-pin me-1"></i>
                            {{ $campus?->name ?? 'No Campus Assigned' }}
                        </p>
                    </div>
                    <div class="text-muted small">
                        Session: <strong>{{ $latestSession ?? '—' }}</strong>
                    </div>
                </div>

                {{-- KPI Cards --}}
                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-xl-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3 p-4">
                                <div
                                    class="avatar avatar-lg bg-primary-subtle rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-users fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Total Applicants</p>
                                    <h3 class="mb-0 fw-bold">{{ $totalApplicants }}</h3>
                                    <p class="text-muted mb-0 small">Submitted applications at your centre</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3 p-4">
                                <div
                                    class="avatar avatar-lg bg-success-subtle rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-circle-check fs-4 text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Admitted</p>
                                    <h3 class="mb-0 fw-bold">{{ $totalAdmitted }}</h3>
                                    <p class="text-muted mb-0 small">Admission granted this session</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3 p-4">
                                <div
                                    class="avatar avatar-lg bg-warning-subtle rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-clock fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small">Awaiting Admission</p>
                                    <h3 class="mb-0 fw-bold">{{ $totalPending }}</h3>
                                    <p class="text-muted mb-0 small">Submitted but not yet admitted</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('admission.applicants') }}" class="btn btn-primary">
                        <i class="ti ti-list me-1"></i> View All Applicants
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
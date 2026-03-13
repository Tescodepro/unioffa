@extends('layouts.app')
@section('title', 'Registrar Dashboard')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Registrar Dashboard</h3>
                        <p class="text-muted mb-0">{{ $schoolName }} · Academic Session:
                            <strong>{{ $latestSession }}</strong>
                        </p>
                    </div>
                </div>

                {{-- KPI Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-primary me-3"><i class="ti ti-users fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Total Applicants</p>
                                        <h4 class="mb-0">{{ number_format($totalApplicants) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-success me-3"><i class="ti ti-check fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Admitted</p>
                                        <h4 class="mb-0">{{ number_format($totalAdmitted) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-danger h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-danger me-3"><i
                                            class="ti ti-alert-circle fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Awaiting Admission</p>
                                        <h4 class="mb-0">{{ number_format($pendingAdmissions) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-warning me-3"><i class="ti ti-clock fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">All Pending</p>
                                        <h4 class="mb-0">{{ number_format($totalPending) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-info h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-info me-3"><i class="ti ti-building fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Centres</p>
                                        <h4 class="mb-0">{{ $totalCentres }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card border-0 border-bottom border-secondary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-secondary me-3"><i class="ti ti-list fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Entry Modes</p>
                                        <h4 class="mb-0">{{ $totalEntryModes }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Action Alert --}}
                @if($pendingAdmissions > 0)
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="ti ti-alert-circle me-2 fs-5"></i>
                        <div>
                            <strong>{{ $pendingAdmissions }} applicant(s)</strong> have submitted their applications and are
                            awaiting admission.
                            <a href="{{ route('admission.applicants') }}" class="alert-link ms-2">Process Now →</a>
                        </div>
                    </div>
                @endif

                {{-- Quick Navigation --}}
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Admission Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('admission.applicants') }}"
                                    class="btn btn-outline-primary w-100 py-3">
                                    <i class="ti ti-users d-block fs-3 mb-1"></i>
                                    Admission &amp; Applicants
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admission.applicants') }}"
                                    class="btn btn-outline-danger w-100 py-3">
                                    <i class="ti ti-users d-block fs-3 mb-1"></i>
                                    Applicants
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
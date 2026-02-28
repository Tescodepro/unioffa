@extends('layouts.app')
@section('title', 'Programme Director Dashboard')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Programme Director Dashboard</h3>
                        <p class="text-muted mb-0">{{ activeSession()->name ?? $latestSession }}</p>
                    </div>
                </div>

                {{-- Dashboard Info Alert --}}
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="ti ti-info-circle me-2 fs-4"></i>
                    <div>
                        <strong>Assigned Scope:</strong> You can only view and manage applicants for the application types
                        assigned to you by ICT.
                    </div>
                </div>

                {{-- KPI Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-primary me-3"><i class="ti ti-users fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Total Scoped Applicants</p>
                                        <h4 class="mb-0">{{ number_format($totalApplicants) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-6">
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
                    <div class="col-xl-4 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-warning me-3"><i class="ti ti-clock fs-4"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Awaiting Admission</p>
                                        <h4 class="mb-0">{{ number_format($totalPending) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Navigation --}}
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Admission Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="{{ route('programme-director.admission.applicants') }}"
                                    class="btn btn-outline-primary w-100 py-3">
                                    <i class="ti ti-users d-block fs-3 mb-1"></i>
                                    Scoped Applicants
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
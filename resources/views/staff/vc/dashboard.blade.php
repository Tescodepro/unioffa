@extends('layouts.app')
@section('title', 'Vice-Chancellor Dashboard')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Vice-Chancellor Dashboard</h3>
                        <p class="text-muted mb-0">{{ $schoolName }} &middot; Academic Session:
                            <strong>{{ $latestSession }}</strong>
                        </p>
                    </div>
                </div>

                {{-- ── Row 1: Institutional KPIs ───────────────────────────────── --}}
                <div class="mb-2">
                    <p class="text-muted fw-semibold small text-uppercase mb-2">Institutional Overview</p>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-primary me-3"><i class="ti ti-school fs-3"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Enrolled Students</p>
                                        <h3 class="mb-0">{{ number_format($totalStudents) }}</h3>
                                        <small class="text-muted">All sessions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-success me-3"><i
                                            class="ti ti-users-group fs-3"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Teaching & Admin Staff</p>
                                        <h3 class="mb-0">{{ number_format($totalStaff) }}</h3>
                                        <small class="text-muted">On record</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-info h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-info me-3"><i
                                            class="ti ti-building-community fs-3"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Faculties</p>
                                        <h3 class="mb-0">{{ $totalFaculties }}</h3>
                                        <small class="text-muted">Schools / Colleges</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="card border-0 border-bottom border-warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-warning me-3"><i
                                            class="ti ti-category fs-3"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Departments</p>
                                        <h3 class="mb-0">{{ $totalDepartments }}</h3>
                                        <small class="text-muted">Academic programmes</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Row 2: Admission Snapshot ──────────────────────────────── --}}
                <div class="mb-2">
                    <p class="text-muted fw-semibold small text-uppercase mb-2">Admission Snapshot &mdash;
                        {{ $latestSession }}
                    </p>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6 col-sm-6">
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
                    <div class="col-xl-3 col-md-6 col-sm-6">
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
                    <div class="col-xl-3 col-md-6 col-sm-6">
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
                    @if($totalApplicants > 0)
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="card border-0 border-bottom border-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-md bg-danger me-3"><i
                                                class="ti ti-percentage fs-4"></i></span>
                                        <div>
                                            <p class="text-muted small mb-0">Admission Rate</p>
                                            <h4 class="mb-0">{{ round(($totalAdmitted / $totalApplicants) * 100, 1) }}%</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── Row 3: Finance Snapshot ─────────────────────────────────── --}}
                <div class="mb-2">
                    <p class="text-muted fw-semibold small text-uppercase mb-2">Finance &mdash; {{ $latestSession }}</p>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 border-bottom border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-success me-3"><i class="ti ti-cash fs-3"></i></span>
                                    <div>
                                        <p class="text-muted small mb-0">Total Revenue Collected</p>
                                        <h3 class="mb-0">&#8358;{{ number_format($totalRevenue, 2) }}</h3>
                                        <small class="text-muted">Successful payments this session</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Quick Navigation ─────────────────────────────────────────── --}}
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('admission.applicants') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="ti ti-users d-block fs-3 mb-1"></i>
                                    Admission &amp; Applicants
                                </a>
                            </div>
                            @can('view_transactions')
                                <div class="col-md-4">
                                    <a href="{{ route('bursary.transactions') }}" class="btn btn-outline-success w-100 py-3">
                                        <i class="ti ti-receipt d-block fs-3 mb-1"></i>
                                        Transactions
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
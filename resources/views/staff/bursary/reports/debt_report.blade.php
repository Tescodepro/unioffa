@extends('layouts.app')
@section('title', 'Student Debt Report')

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
                <div class="d-md-flex d-block align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Student Debt Report</h3>
                        <p class="text-muted mb-0">Track and manage outstanding payments across all sessions</p>
                    </div>
                    @include('layouts.flash-message')
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Department</label>
                                <select name="department_id" class="form-select border-0 bg-light shadow-none rounded-3">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted text-uppercase">Level</label>
                                <select name="level" class="form-select border-0 bg-light shadow-none rounded-3">
                                    <option value="">All Levels</option>
                                    @foreach([100, 200, 300, 400, 500, 600, 700] as $lvl)
                                        <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>
                                            Level {{ $lvl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Matric No</label>
                                <input type="text" name="matric_no" class="form-control border-0 bg-light shadow-none rounded-3" 
                                    placeholder="Search by matric no" value="{{ request('matric_no') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3">
                                    <i class="ti ti-search me-1"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0 small fw-bold text-muted text-uppercase">Student</th>
                                        <th class="py-3 border-0 small fw-bold text-muted text-uppercase">Department</th>
                                        <th class="py-3 border-0 small fw-bold text-muted text-uppercase text-center">Level</th>
                                        <th class="py-3 border-0 small fw-bold text-muted text-uppercase text-end">Total Owed</th>
                                        <th class="pe-4 py-3 border-0 small fw-bold text-muted text-uppercase text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($debtors as $debtor)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                        <i class="ti ti-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $debtor['student']->user->first_name }} {{ $debtor['student']->user->last_name }}</h6>
                                                        <p class="text-muted small mb-0">{{ $debtor['student']->matric_no }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark small fw-semibold">{{ $debtor['student']->department->department_name ?? '—' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-soft-dark text-dark rounded-pill">{{ $debtor['student']->level }}</span>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="mb-0 fw-bold text-danger">₦{{ number_format($debtor['debt_amount'], 2) }}</h6>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('bursary.reports.student', ['search_term' => $debtor['student']->matric_no]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    View History
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <img src="{{ asset('assets/img/illustrations/no-debt.svg') }}" alt="No Debt" class="img-fluid mb-3" style="max-height: 150px;">
                                                <p class="text-muted">No students with outstanding debts found matching your criteria.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-dark { background-color: rgba(33, 37, 41, 0.1); }
        .rounded-4 { border-radius: 1rem !important; }
    </style>
@endsection

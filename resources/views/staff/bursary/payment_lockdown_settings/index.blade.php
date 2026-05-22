@extends('layouts.app')

@section('title', 'Payment Lockdown Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Payment Lockdown Settings</h4>
                        <p class="text-muted small mb-0">Manage payment portal deadlines and student lock blocks</p>
                    </div>
                    <a href="{{ route('bursary.payment-lockdown-settings.create') }}" class="btn btn-primary px-3">
                        <i class="ti ti-plus me-1"></i> Add New Lockdown
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white p-2 rounded-2 me-3">
                                <i class="ti ti-circle-check fs-5"></i>
                            </div>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Premium Filter Panel --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('bursary.payment-lockdown-settings.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Fee Type</label>
                                    <select name="payment_type" class="form-select border-0 bg-light">
                                        <option value="">All Payment Types</option>
                                        @foreach ($paymentTypes as $type)
                                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Status</label>
                                    <select name="is_active" class="form-select border-0 bg-light">
                                        <option value="">All Statuses</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark w-100 py-2">
                                        <i class="ti ti-adjustments me-1"></i> Apply
                                    </button>
                                    <a href="{{ route('bursary.payment-lockdown-settings.index') }}" class="btn btn-light border py-2 px-3" title="Clear Filters">
                                        <i class="ti ti-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Data Card Table --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light border-0">
                                <tr>
                                    <th class="py-3 ps-4 text-muted small text-uppercase fw-bold">Lockdown Title</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Targeted Fee</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Lockdown Deadline</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Status</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Scope Badges</th>
                                    <th class="py-3 text-end pe-4 text-muted small text-uppercase fw-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lockdowns as $lockdown)
                                    <tr>
                                        {{-- Lockdown Title --}}
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                                    <i class="ti ti-lock fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $lockdown->title }}</div>
                                                    <div class="text-muted extra-small">Created {{ $lockdown->created_at->format('M d, Y') }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Targeted Fee --}}
                                        <td>
                                            @if(is_array($lockdown->payment_types) && count($lockdown->payment_types) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($lockdown->payment_types as $ptype)
                                                        <span class="badge bg-soft-info text-info border-0 rounded-pill px-2 py-1">
                                                            {{ ucfirst($ptype) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="badge bg-soft-info text-info border-0 rounded-pill px-3 py-1">
                                                    All Payments
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Lockdown Deadline --}}
                                        <td>
                                            @if($lockdown->deadline->isPast())
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 border border-danger-subtle rounded-pill">
                                                    <i class="ti ti-clock-x me-1"></i> Locked {{ $lockdown->deadline->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-2 py-1 border border-success-subtle rounded-pill">
                                                    <i class="ti ti-clock-check me-1"></i> Lockdown {{ $lockdown->deadline->format('d M, Y') }}
                                                </span>
                                            @endif
                                            <div class="text-muted extra-small mt-1 ps-1">{{ $lockdown->deadline->format('h:i A') }}</div>
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            @if($lockdown->is_active)
                                                <span class="badge bg-success px-2 py-1 text-white rounded-pill">Active</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1 text-white rounded-pill">Disabled</span>
                                            @endif
                                        </td>

                                        {{-- Scope Badges --}}
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 max-width-300">
                                                @if(is_array($lockdown->campus_ids) && count($lockdown->campus_ids))
                                                    <span class="badge bg-light text-dark rounded px-2">Campuses ({{ count($lockdown->campus_ids) }})</span>
                                                @endif
                                                @if(is_array($lockdown->faculty_ids) && count($lockdown->faculty_ids))
                                                    <span class="badge bg-light text-dark rounded px-2">Faculties ({{ count($lockdown->faculty_ids) }})</span>
                                                @endif
                                                @if(is_array($lockdown->department_ids) && count($lockdown->department_ids))
                                                    <span class="badge bg-light text-dark rounded px-2">Depts ({{ count($lockdown->department_ids) }})</span>
                                                @endif
                                                @if(is_array($lockdown->levels) && count($lockdown->levels))
                                                    <span class="badge bg-light text-dark rounded px-2">Levels ({{ count($lockdown->levels) }})</span>
                                                @endif
                                                @if(is_array($lockdown->admission_sessions) && count($lockdown->admission_sessions))
                                                    <span class="badge bg-light text-dark rounded px-2">Sessions ({{ count($lockdown->admission_sessions) }})</span>
                                                @endif
                                                @if(is_array($lockdown->genders) && count($lockdown->genders))
                                                    <span class="badge bg-light text-dark rounded px-2">Genders ({{ count($lockdown->genders) }})</span>
                                                @endif
                                                @if(is_array($lockdown->entry_modes) && count($lockdown->entry_modes))
                                                    <span class="badge bg-light text-dark rounded px-2">Modes ({{ count($lockdown->entry_modes) }})</span>
                                                @endif
                                                @if(is_array($lockdown->programmes) && count($lockdown->programmes))
                                                    <span class="badge bg-light text-dark rounded px-2">Progs ({{ count($lockdown->programmes) }})</span>
                                                @endif

                                                @if(
                                                    empty($lockdown->campus_ids) && empty($lockdown->faculty_ids) && empty($lockdown->department_ids) &&
                                                    empty($lockdown->levels) && empty($lockdown->admission_sessions) && empty($lockdown->genders) &&
                                                    empty($lockdown->entry_modes) && empty($lockdown->programmes)
                                                )
                                                    <span class="text-muted extra-small italic">Universal Scope (All Students)</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm rounded-3">
                                                <a href="{{ route('bursary.payment-lockdown-settings.edit', $lockdown->id) }}"
                                                    class="btn btn-white btn-sm px-3 border-end" title="Edit Configuration">
                                                    <i class="ti ti-edit text-warning"></i>
                                                </a>
                                                <form action="{{ route('bursary.payment-lockdown-settings.destroy', $lockdown->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Delete this payment lockdown setting?')"
                                                        class="btn btn-white btn-sm px-3" title="Delete">
                                                        <i class="ti ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="py-4">
                                                <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                                    <i class="ti ti-lock fs-1 text-muted"></i>
                                                </div>
                                                <h5 class="fw-bold">No Lockdown Rules Found</h5>
                                                <p class="text-muted mb-4">You haven't configured any payment portal lockdown deadlines yet.</p>
                                                <a href="{{ route('bursary.payment-lockdown-settings.create') }}" class="btn btn-primary px-4">
                                                    <i class="ti ti-plus me-1"></i> Create First Lockdown
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($lockdowns->hasPages())
                        <div class="card-footer bg-white py-3 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Showing {{ $lockdowns->firstItem() }} to {{ $lockdowns->lastItem() }} of {{ $lockdowns->total() }} settings</div>
                                {{ $lockdowns->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <style>
        .extra-small { font-size: 0.7rem; }
        .btn-white { background: #fff; color: #475569; }
        .btn-white:hover { background: #f8fafc; }
        .max-width-300 { max-width: 300px; }
    </style>
@endsection

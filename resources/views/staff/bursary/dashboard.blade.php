@extends('layouts.app')

@section('title', $title)

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">

        <!-- Header -->
        @include('staff.layouts.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('staff.layouts.sidebar')
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Bursary Dashboard</h3>
                        <p class="text-muted mb-0">Overview of all student payments and financial activity</p>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        <form method="GET" action="{{ route('burser.dashboard') }}" class="d-flex align-items-center">
                            <label for="session" class="me-2 fw-medium text-muted mb-0">Session:</label>
                            <select name="session" id="session" class="form-select form-select-sm w-auto"
                                onchange="this.form.submit()">
                                @foreach($sessions as $session)
                                    <option value="{{ $session }}" {{ $selectedSession === $session ? 'selected' : '' }}>
                                        {{ $session }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Stats Row -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 border-bottom border-success flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-success me-2">
                                        <i class="ti ti-cash fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">Total Collected</h6>
                                        <h5 class="mb-0">₦{{ number_format($stats['total_collected'], 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 border-bottom border-warning flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-warning me-2">
                                        <i class="ti ti-hourglass fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">Pending Payments</h6>
                                        <h5 class="mb-0">{{ $stats['pending_payments'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 border-bottom border-danger flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-danger me-2">
                                        <i class="ti ti-alert-circle fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">Failed Transactions</h6>
                                        <h5 class="mb-0">{{ $stats['failed_payments'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 border-bottom border-primary flex-fill animate-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md rounded bg-primary me-2">
                                        <i class="ti ti-receipt fs-16"></i>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">Total Successful Transactions</h6>
                                        <h5 class="mb-0">{{ $stats['total_transactions'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Stats Row -->

                <!-- Payments by Type -->
                <div class="card mb-4">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Payments by Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Payment Type</th>
                                        <th>Total Transactions</th>
                                        <th>Total Amount (₦)</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentsByType as $type)
                                        <tr>
                                            <td>{{ ucfirst($type->payment_type) }}</td>
                                            <td>{{ $type->total }}</td>
                                            <td>{{ number_format($type->total_amount, 2) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('bursary.transactions', ['payment_type' => $type->payment_type, 'session' => $selectedSession]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-eye"></i> View Transactions
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- === Per-Center Revenue Cards === --}}
                @if(count($campusBreakdown) > 0)
                    @php
                        $centerAccents = ['primary', 'success', 'warning', 'info', 'danger', 'secondary'];
                        $centerIdx = 0;
                    @endphp

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="ti ti-building me-2 text-primary"></i> Revenue by Center
                        </h5>
                        <span class="badge bg-light text-muted border">Successful payments only</span>
                    </div>

                    <div class="row g-4 mb-4">
                    <div class="accordion accordion-flush" id="campusAccordion">
                        @foreach($campusBreakdown as $campusName => $centers)
                            @php
                                $campusSlug = Str::slug($campusName);
                            @endphp
                            <div class="accordion-item border-0 mb-3 shadow-sm rounded overflow-hidden">
                                <h2 class="accordion-header" id="heading-{{ $campusSlug }}">
                                    <button class="accordion-button bg-light fw-bold text-dark py-3" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $campusSlug }}" 
                                        aria-expanded="true" aria-controls="collapse-{{ $campusSlug }}">
                                        <i class="ti ti-building-community me-2 text-primary"></i>
                                        {{ $campusName }} Section
                                    </button>
                                </h2>
                                <div id="collapse-{{ $campusSlug }}" class="accordion-collapse collapse show" 
                                    aria-labelledby="heading-{{ $campusSlug }}" data-bs-parent="#campusAccordion">
                                    <div class="accordion-body bg-white p-4">
                                        <div class="row g-4">
                                            @foreach($centers as $centerLabel => $types)
                                                @php
                                                    $accent = $centerAccents[$centerIdx % count($centerAccents)];
                                                    $centerIdx++;
                                                    $campusTotal = collect($types['types'])->sum('amount');
                                                    $campusTxns = collect($types['types'])->sum('total');
                                                @endphp
                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="card h-100 border-0 shadow-sm overflow-hidden border-start border-4 border-{{ $accent }}">
                                                        {{-- Header --}}
                                                        <div class="bg-{{ $accent }} bg-opacity-10 px-4 py-3 d-flex align-items-center justify-content-between border-bottom">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="avatar avatar-xs rounded-circle bg-{{ $accent }} text-white">
                                                                    <i class="ti ti-map-pin fs-12"></i>
                                                                </span>
                                                                <h6 class="mb-0 fw-bold">{{ $centerLabel }}</h6>
                                                            </div>
                                                            <span class="badge bg-{{ $accent }} text-white fs-11 px-2 py-1">
                                                                {{ $campusTxns }} txn{{ $campusTxns != 1 ? 's' : '' }}
                                                            </span>
                                                        </div>

                                                        {{-- Total --}}
                                                        <div class="px-4 py-2 d-flex align-items-center justify-content-between">
                                                            <span class="text-muted small fw-medium">Total Collected</span>
                                                            <span class="fw-bold text-{{ $accent }}">₦{{ number_format($campusTotal, 2) }}</span>
                                                        </div>

                                                        {{-- Table --}}
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr class="small">
                                                                        <th class="ps-4">Type</th>
                                                                        <th class="text-center">Count</th>
                                                                        <th class="text-end pe-4">Amount (₦)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($types['types'] as $pt => $cell)
                                                                        <tr class="small">
                                                                            <td class="ps-4 py-2 text-muted">{{ ucfirst($pt) }}</td>
                                                                            <td class="text-center">{{ $cell['total'] }}</td>
                                                                            <td class="text-end pe-4 fw-semibold">₦{{ number_format($cell['amount'], 2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot class="border-top">
                                                                    <tr>
                                                                        <td colspan="3" class="text-center py-2">
                                                                            <a href="{{ route('bursary.transactions', ['campus_id' => $types['campus_id'], 'programme' => $types['programme'], 'session' => $selectedSession]) }}"
                                                                                class="btn btn-sm btn-link text-{{ $accent }} p-0 text-decoration-none small">
                                                                                <i class="ti ti-eye"></i> View All Transactions
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                        {{-- Unassigned / No Center card --}}
                        @if(count($unassignedBreakdown) > 0)
                            @php
                                $unassignedTotal = collect($unassignedBreakdown)->sum('amount');
                                $unassignedTxns = collect($unassignedBreakdown)->sum('total');
                            @endphp
                            <div class="col-xl-6 col-lg-6">
                                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                    {{-- Dark header for unassigned --}}
                                    <div class="px-4 py-4 d-flex align-items-center justify-content-between"
                                        style="background: #4b5563;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="avatar avatar-md rounded-circle text-white"
                                                style="background:rgba(255,255,255,0.15);">
                                                <i class="ti ti-map-pin-off fs-18"></i>
                                            </span>
                                            <h5 class="mb-0 text-white fw-bold">Unassigned / No Center</h5>
                                        </div>
                                        <span class="badge text-white fs-12 px-3 py-2" style="background:rgba(255,255,255,0.15);">
                                            {{ $unassignedTxns }} transaction{{ $unassignedTxns != 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    {{-- Grand total strip --}}
                                    <div class="px-4 py-3 d-flex align-items-center justify-content-between border-bottom"
                                        style="background: rgba(75,85,99,0.08);">
                                        <span class="text-muted fw-semibold">Total Collected</span>
                                        <span class="fs-4 fw-bold"
                                            style="color:#4b5563;">₦{{ number_format($unassignedTotal, 2) }}</span>
                                    </div>

                                    {{-- Per-type breakdown --}}
                                    <div class="card-body p-0">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4 py-3">Payment Type</th>
                                                    <th class="text-center py-3">Transactions</th>
                                                    <th class="text-end py-3">Amount (₦)</th>
                                                    <th class="text-end pe-4 py-3">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($unassignedBreakdown as $pt => $cell)
                                                    <tr>
                                                        <td class="ps-4 py-3">
                                                            <span class="badge border px-3 py-2 fs-12"
                                                                style="background:rgba(75,85,99,0.1);color:#4b5563;border-color:rgba(75,85,99,0.3)!important;">
                                                                {{ ucfirst($pt) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center fw-semibold">{{ $cell['total'] }}</td>
                                                        <td class="text-end fw-bold">₦{{ number_format($cell['amount'], 2) }}</td>
                                                        <td class="text-end pe-4">
                                                            <a href="{{ route('bursary.transactions', ['campus_id' => 'unassigned', 'payment_type' => $pt, 'session' => $selectedSession]) }}"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="ti ti-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Manual Transactions card --}}
                        @if(count($manualBreakdown) > 0)
                            @php
                                $manualTotal = collect($manualBreakdown)->sum('amount');
                                $manualTxns = collect($manualBreakdown)->sum('total');
                            @endphp
                            <div class="col-xl-6 col-lg-6">
                                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                    {{-- Orange/amber header for manual --}}
                                    <div class="px-4 py-4 d-flex align-items-center justify-content-between"
                                        style="background: #b45309;">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="avatar avatar-md rounded-circle text-white"
                                                style="background:rgba(255,255,255,0.15);">
                                                <i class="ti ti-pencil fs-18"></i>
                                            </span>
                                            <h5 class="mb-0 text-white fw-bold">Manual Transactions</h5>
                                        </div>
                                        <span class="badge text-white fs-12 px-3 py-2" style="background:rgba(255,255,255,0.15);">
                                            {{ $manualTxns }} transaction{{ $manualTxns != 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    {{-- Total strip --}}
                                    <div class="px-4 py-3 d-flex align-items-center justify-content-between border-bottom"
                                        style="background: rgba(180,83,9,0.08);">
                                        <span class="text-muted fw-semibold">Total Collected</span>
                                        <span class="fs-4 fw-bold"
                                            style="color:#b45309;">₦{{ number_format($manualTotal, 2) }}</span>
                                    </div>

                                    {{-- Per-type breakdown --}}
                                    <div class="card-body p-0">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4 py-3">Payment Type</th>
                                                    <th class="text-center py-3">Transactions</th>
                                                    <th class="text-end py-3">Amount (₦)</th>
                                                    <th class="text-end pe-4 py-3">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($manualBreakdown as $pt => $cell)
                                                    <tr>
                                                        <td class="ps-4 py-3">
                                                            <span class="badge border px-3 py-2 fs-12"
                                                                style="background:rgba(180,83,9,0.1);color:#b45309;border-color:rgba(180,83,9,0.3)!important;">
                                                                {{ ucfirst($pt) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center fw-semibold">{{ $cell['total'] }}</td>
                                                        <td class="text-end fw-bold">₦{{ number_format($cell['amount'], 2) }}</td>
                                                        <td class="text-end pe-4">
                                                            <a href="{{ route('bursary.transactions', ['payment_type' => $pt, 'session' => $selectedSession]) }}"
                                                                class="btn btn-sm btn-outline-warning">
                                                                <i class="ti ti-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>{{-- end .row --}}
                @endif


                <!-- Recent Transactions -->
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Type</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions as $txn)
                                        <tr>
                                            <td>{{ $txn->refernce_number }}</td>
                                            <td>{{ $txn->user?->full_name ?? 'Unknown' }}</td>
                                            <td>₦{{ number_format($txn->amount, 2) }}</td>
                                            <td>
                                                @if($txn->payment_status == 1)
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($txn->payment_status == 2)
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($txn->payment_type) }}</td>
                                            <td>{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No recent transactions found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
@endsection
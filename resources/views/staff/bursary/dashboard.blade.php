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
                                    <h6 class="mb-0">Total Transactions</h6>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentsByType as $type)
                                <tr>
                                    <td>{{ ucfirst($type->payment_type) }}</td>
                                    <td>{{ $type->total }}</td>
                                    <td>{{ number_format($type->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
                                    <td>{{ $txn->user->name ?? 'Unknown' }}</td>
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

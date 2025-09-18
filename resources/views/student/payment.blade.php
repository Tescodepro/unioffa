@extends('layouts.app')

@section('title', 'Payments')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>

<div class="main-wrapper">
    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-4">
                    <h3 class="page-title mb-1">Payment Dashboard</h3>
                    <p class="mb-1 text-muted">View your required payments and completed transactions.</p>

                    <div class="bg-light p-3 rounded shadow-sm">
                        <p class="mb-1"><strong>Current Session:</strong> {{ activeSession()->name ?? 'No active session' }}</p>
                        <p class="mb-0"><strong>Current Semester:</strong> {{ activeSemester()->name ?? 'No active semester' }}</p>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            @include('layouts.flash-message')

            <!-- Required Payments -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                    <h4 class="mb-3">Required Payments</h4>
                </div>
                <div class="card-body p-0 py-3">
                    <div class="table-responsive">
                        <table class="table datatable align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Payment Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentSettings as $setting)
                                    @php
                                        $paid = $transactions
                                            ->where('payment_type', $setting->payment_type)
                                            ->where('payment_status', 'success')
                                            ->sum('amount');

                                        $balance = $setting->amount - $paid;
                                    @endphp
                                    <tr>
                                        <td>{{ ucfirst($setting->payment_type ?? '---') }}</td>
                                        <td>{{ $setting->description ?? '---' }}</td>
                                        <td>{{ number_format($setting->amount ?? 0, 2) }}</td>
                                        <td>{{ number_format($paid ?? 0, 2) }}</td>
                                        <td>{{ number_format($balance ?? 0, 2) }}</td>
                                        <td>
                                            @if(($balance ?? 0) > 0)
                                                @if(($setting->payment_type ?? '') == 'tuition')
                                                    <a href="#" class="btn btn-sm btn-primary">Pay Installment</a>
                                                @else
                                                    <a href="#" class="btn btn-sm btn-success">Pay Now</a>
                                                @endif
                                            @else
                                                <span class="badge bg-success">Completed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No required payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Required Payments -->

            <!-- Transactions -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                    <h4 class="mb-3">Payment History</h4>
                </div>
                <div class="card-body p-0 py-3">
                    <div class="table-responsive">
                        <table class="table datatable align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Session</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $tx)
                                    <tr>
                                        <td>{{ $tx->reference_number ?? '---' }}</td>
                                        <td>{{ ucfirst($tx->payment_type ?? '---') }}</td>
                                        <td>{{ number_format($tx->amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(($tx->payment_status ?? '') === 'success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif(($tx->payment_status ?? '') === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ $tx->payment_method ?? '---' }}</td>
                                        <td>{{ $tx->session ?? '---' }}</td>
                                        <td>{{ $tx->created_at?->format('d M, Y h:i A') ?? '---' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Transactions -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('.datatable').DataTable({
            "order": [], // Optional: disable initial sorting
        });
    });
</script>
@endpush

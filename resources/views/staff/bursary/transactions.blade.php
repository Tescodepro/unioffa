@extends('layouts.app')

@section('title', 'Transactions Management')

@section('content')
<div class="main-wrapper">

    <!-- Header -->
    @include('staff.layouts.header')
    <!-- /Header -->

    <!-- Sidebar -->
    @include('staff.layouts.sidebar')
    <!-- /Sidebar -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header d-md-flex d-block align-items-center justify-content-between mb-3">
                <h3 class="page-title mb-2">Transactions Management</h3>
                <div class="btn-group">
                    <!-- Download Options -->
                    <a href="{{ route('bursary.transactions.export', ['format' => 'excel'] + request()->all()) }}" 
                       class="btn btn-success">
                        <i class="ti ti-file-spreadsheet"></i> Export Excel
                    </a>
                    <a href="{{ route('bursary.transactions.export', ['format' => 'pdf'] + request()->all()) }}" 
                       class="btn btn-danger">
                        <i class="ti ti-file-text"></i> Export PDF
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('bursary.transactions') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference" value="{{ request('reference') }}" class="form-control" placeholder="e.g. TXN-1234ABC">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="tuition" {{ request('payment_type') == 'tuition' ? 'selected' : '' }}>Tuition</option>
                                <option value="application" {{ request('payment_type') == 'application' ? 'selected' : '' }}>Application</option>
                                <option value="acceptance" {{ request('payment_type') == 'acceptance' ? 'selected' : '' }}>Acceptance</option>
                                <option value="accommodation" {{ request('payment_type') == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Student Name</label>
                            <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Filters -->
            {{--  --}}
            <!-- Transactions Table -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Reference</th>
                                    <th>Student</th>
                                    <th>Matric No / Student ID</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $index => $txn)
                                    <tr>
                                        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                                        <td><strong>{{ $txn->refernce_number }}</strong></td>
                                        <td>{{ optional($txn->user)->first_name }} {{ optional($txn->user)->last_name }}</td>
                                        <td>{{ optional($txn->user)->username }}</td>
                                        <td>{{ ucfirst($txn->payment_type) }}</td>
                                        <td>{{ number_format($txn->amount, 2) }}</td>
                                        <td>
                                            @if($txn->payment_status == 1)
                                                <span class="badge bg-success">Successful</span>
                                            @elseif($txn->payment_status == 2)
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $txn->created_at->format('d M, Y h:i A') }}</td>
                                        <td>
                                            @if($txn->payment_status != 1)
                                                <a href="{{ route('bursary.transactions.verify', $txn->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-refresh"></i> Verify
                                                </a>
                                            @else
                                                <span class="text-success fw-semibold">
                                                    <i class="ti ti-check"></i> Verified
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted p-4">
                                            No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($transactions->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

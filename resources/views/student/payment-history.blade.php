@extends('layouts.app')

@section('title', 'Payment History')

@push('styles')
<link rel="stylesheet" href="{{ asset('portal_assets/css/bootstrap.min.css') }}"> <!-- Adjust path as needed -->
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .badge-completed {
        background-color: #28a745; /* Green for completed */
    }
</style>
@endpush

@section('content')
<div id="global-loader">
    <div class="page-loader"></div>
</div>

<div class="main-wrapper">
    @include('student.partials.header')
    @include('student.partials.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-4">
                    <h3 class="page-title mb-1">Payment History</h3>
                    <p class="mb-1 text-muted">View your completed transactions for the {{ $currentSession ?? 'N/A' }} session.</p>

                    <div class="bg-light p-3 rounded shadow-sm">
                        <p class="mb-1"><strong>Current Session:</strong> {{ $currentSession ?? 'No active session' }}</p>
                        <p class="mb-0"><strong>Current Semester:</strong> {{ activeSemester()->name ?? 'No active semester' }}</p>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            @include('layouts.flash-message')

            <!-- Transactions -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                    <h4 class="mb-3">Payment History ({{ $currentSession ?? 'N/A' }})</h4>
                </div>
                <div class="card-body p-0 py-3">
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->refernce_number }}</td>
                                        <td>{{ $transaction->payment_type }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-completed">Completed</span>
                                        </td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td>{{ $transaction->session }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No completed transactions found for the current session.</td>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
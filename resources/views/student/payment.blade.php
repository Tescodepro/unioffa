@extends('layouts.app')

@section('title', 'Required Payments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> <!-- Adjust path as needed -->
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
                    <h3 class="page-title mb-1">Required Payments</h3>
                    <p class="mb-1 text-muted">View your required payments for the {{ $currentSession ?? 'N/A' }} session.</p>

                    <div class="bg-light p-3 rounded shadow-sm">
                        <p class="mb-1"><strong>Current Session:</strong> {{ $currentSession ?? 'No active session' }}</p>
                        <p class="mb-0"><strong>Current Semester:</strong> {{ activeSemester()->name ?? 'No active semester' }}</p>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            @include('layouts.flash-message')

            <!-- Required Payments -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap pb-0">
                    <h4 class="mb-3">Required Payments ({{ $currentSession ?? 'N/A' }})</h4>
                </div>
                <div class="card-body p-0 py-3">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Payment Type</th>
                                    <th>Description</th>
                                    <th>Amount to Pay</th>
                                    <th>Amount Paid</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentSettings as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ $payment->description ?? 'N/A' }}</td>
                                        <td>{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td>{{ number_format($payment->balance, 2) }}</td>
                                        <td>
                                            @if ($payment->balance > 0)
                                                @if ($payment->payment_type === 'tuition' && $payment->installment_count >= 3)
                                                    <span class="badge bg-danger">Installment Limit Reached</span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $payment->id }}">
                                                        Pay Now
                                                    </button>
                                                    <!-- Payment Confirmation Modal -->
                                                    <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $payment->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="paymentModalLabel{{ $payment->id }}">Confirm Payment</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('application.payment.process') }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="text-center">
                                                                            <div class="mb-3">
                                                                                <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                                                                            </div>
                                                                            <h3>Payment Confirmation</h3>
                                                                            <p>Are you sure you want to make a payment for <strong>{{ $payment->payment_type }}</strong>?</p>
                                                                            <div class="alert alert-info">
                                                                                <small><i class="fas fa-info-circle"></i> You will be redirected to our secure payment gateway to complete this transaction.</small>
                                                                            </div>
                                                                            @if ($payment->payment_type === 'tuition')
                                                                                <div class="mb-3">
                                                                                    <label for="amount{{ $payment->id }}" class="form-label">Select Amount to Pay</label>
                                                                                    <select name="amount" id="amount{{ $payment->id }}" class="form-select" required>
                                                                                        <option value="" disabled selected>Choose amount</option>
                                                                                        @if ($payment->balance >= $payment->amount / 3)
                                                                                            <option value="{{ $payment->amount / 3 }}">{{ number_format($payment->amount / 3, 2) }}</option>
                                                                                        @endif
                                                                                        @if ($payment->balance >= $payment->amount * 2 / 3)
                                                                                            <option value="{{ $payment->amount * 2 / 3 }}">{{ number_format($payment->amount * 2 / 3, 2) }}</option>
                                                                                        @endif
                                                                                        <option value="{{ $payment->amount }}">{{ number_format($payment->amount, 2) }}</option>
                                                                                    </select>
                                                                                    <p class="text-muted mt-2">Installments used: {{ $payment->installment_count }}/3</p>
                                                                                </div>
                                                                            @else
                                                                                <p>Amount: <strong>{{ number_format($payment->amount, 2) }}</strong></p>
                                                                                <input type="hidden" name="amount" value="{{ $payment->amount }}">
                                                                            @endif
                                                                            <input type="hidden" name="fee_type" value="{{ $payment->payment_type }}">
                                                                            <input type="hidden" name="gateway" value="oneapp">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary">Confirm Payment</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge badge-completed">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No required payments found for the current session.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Required Payments -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
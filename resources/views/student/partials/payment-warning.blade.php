<div class="alert alert-warning border-0 shadow-sm rounded-3 p-4" role="alert">
    <div class="d-flex align-items-center mb-3">
        <i class="ti ti-alert-triangle me-2 fs-3 text-danger"></i>
        <h4 class="mb-0">Payment Status: Pending</h4>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-borderless mb-3">
            <thead class="text-muted">
                <tr>
                    <th>Payment Type</th>
                    <th>Status</th>
                    <th>Paid</th>
                    <th>Outstanding Balance</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment_status['status'] as $pay)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $pay['payment_type'])) }}</td>
                        <td>
                            <span class="badge bg-{{ $pay['status'] === 'PAID' ? 'success' : 'warning' }}">
                                {{ ucfirst($pay['status']) }}
                            </span>
                        </td>
                        <td>₦{{ number_format($pay['amount_paid'], 2) }}</td>
                        <td>₦{{ number_format($pay['balance'], 2) }}</td>
                        <td>
                            @if (isset($pay['percentage_paid']))
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" id="progressBar" role="progressbar"
                                        style="width: {{ $pay['percentage_paid'] }}%;"
                                        aria-valuenow="{{ $pay['percentage_paid'] }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <small>{{ $pay['percentage_paid'] }}%</small>
                            @else
                                @if ($pay['status'] == 'PENDING')
                                    <span class="text-muted">Pending</span>
                                @elseif ($pay['status'] == 'PAID')
                                    <span class="text-success">Cleared</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <p class="fw-bold mb-1">
            Outstanding Balance: 
            <span class="text-danger">₦{{ number_format($payment_status['outstanding'], 2) }}</span>
        </p>
        <p class="text-muted mb-0">Please clear your dues to enable course registration.</p>
    </div>

    <div class="mt-3">
        <a href="{{ route('students.load_payment') }}" class="btn btn-primary">
            <i class="ti ti-credit-card me-2"></i>
            Proceed to Payment
        </a>
    </div>
</div>

@extends('layouts.app')

@section('title', 'Add Manual Transaction')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <h3 class="page-title">Add Manual Transaction</h3>
                    <p class="text-muted">Record a payment made outside the portal (e.g., direct bank transfer).</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('bursary.transactions.store') }}" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Student (Username / Email / Matric No)</label>
                                <input type="text" name="identifier" value="{{ old('identifier') }}" class="form-control"
                                    placeholder="e.g. johndoe or johndoe@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Type</label>
                                <select name="payment_type" class="form-select" required>
                                    <option value="">-- Select Payment Type --</option>
                                    @foreach ($paymentTypes as $type)
                                        <option value="{{ $type }}"
                                            {{ old('payment_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" step="0.01" class="form-control" required
                                    placeholder="e.g. 15000">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Session</label>
                                <select name="session" class="form-select" required>
                                    <option value="">-- Select Session --</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session }}"
                                            {{ old('session') == $session ? 'selected' : '' }}>
                                            {{ $session }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select" required>
                                    <option value="1">Successful</option>
                                    <option value="0">Pending</option>
                                    <option value="2">Failed</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check"></i> Save Transaction
                                </button>
                                <a href="{{ route('bursary.transactions') }}"
                                    class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

            @if ($manualTransactions->count())
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Manual Transactions</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Reference</th>
                                        <th>Student</th>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Session</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($manualTransactions as $index => $txn)
                                        <tr>
                                            <td>{{ $manualTransactions->firstItem() + $index }}</td>
                                            <td><strong>{{ $txn->refernce_number }}</strong></td>
                                            <td>{{ optional($txn->user)->first_name }}
                                                {{ optional($txn->user)->last_name }}</td>
                                            <td>{{ ucfirst($txn->payment_type) }}</td>
                                            <td>{{ number_format($txn->amount, 2) }}</td>
                                            <td>{{ $txn->session }}</td>
                                            <td>
                                                @if ($txn->payment_status == 1)
                                                    <span class="badge bg-success">Successful</span>
                                                @elseif($txn->payment_status == 2)
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $txn->id }}">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $txn->id }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal{{ $txn->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('bursary.transactions.update', $txn->id) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Manual Transaction</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Payment Type</label>
                                                                <select name="payment_type" class="form-select" required>
                                                                    @foreach ($paymentTypes as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ $txn->payment_type == $type ? 'selected' : '' }}>
                                                                            {{ ucfirst($type) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Amount</label>
                                                                <input type="number" name="amount" class="form-control"
                                                                    step="0.01" value="{{ $txn->amount }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Session</label>
                                                                <select name="session" class="form-select" required>
                                                                    @foreach ($sessions as $session)
                                                                        <option value="{{ $session }}"
                                                                            {{ $txn->session == $session ? 'selected' : '' }}>
                                                                            {{ $session }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Status</label>
                                                                <select name="payment_status" class="form-select"
                                                                    required>
                                                                    <option value="1"
                                                                        {{ $txn->payment_status == 1 ? 'selected' : '' }}>
                                                                        Successful</option>
                                                                    <option value="0"
                                                                        {{ $txn->payment_status == 0 ? 'selected' : '' }}>
                                                                        Pending</option>
                                                                    <option value="2"
                                                                        {{ $txn->payment_status == 2 ? 'selected' : '' }}>
                                                                        Failed</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $txn->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('bursary.transactions.destroy', $txn->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Manual Transaction</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete the transaction
                                                                <strong>{{ $txn->refernce_number }}</strong>?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Yes,
                                                                Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $manualTransactions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <p class="text-muted mt-4">No manual transactions found yet.</p>
            @endif


        </div>
    </div>
@endsection

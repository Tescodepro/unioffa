@extends('layouts.app')
@section('title', 'Student Payment History')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endpush

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="row align-items-center mb-4">
                    <div class="col-sm-6">
                        <h3 class="page-title">{{ $title }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('burser.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Student History</li>
                        </ul>
                    </div>
                </div>

                @include('layouts.flash-message')

                <!-- Search Card -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('bursary.student.history') }}" method="GET"
                            class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <label class="form-label mb-0 fw-bold">Enter Student Matric Number:</label>
                                <input type="text" name="matric_number" class="form-control form-control-lg mt-2"
                                    placeholder="E.g. ACC/24/0001" value="{{ request('matric_number') }}" required>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="ti ti-search me-2"></i> Search
                                </button>
                                @if(request('matric_number'))
                                    <a href="{{ route('bursary.student.history') }}" class="btn btn-light btn-lg px-4 ms-2">
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                @if($student)
                    <!-- Student Profile Card -->
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><i class="ti ti-user-circle text-primary me-2"></i> Student Profile</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Full Name</p>
                                    <h6 class="mb-0 fw-bold">{{ $student->user->first_name }} {{ $student->user->last_name }}
                                    </h6>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Matric Number</p>
                                    <h6 class="mb-0 fw-bold">{{ $student->matric_no ?? $student->matric_number }}</h6>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Faculty / Department</p>
                                    <h6 class="mb-0 fw-bold">{{ $student->department?->faculty?->faculty_name ?? 'N/A' }} <br><span
                                            class="text-muted fw-normal">{{ $student->department?->department_name ?? 'N/A' }}</span>
                                    </h6>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Level / Programme</p>
                                    <h6 class="mb-0 fw-bold">{{ $student->level ?? 'N/A' }}<br><span
                                            class="text-muted fw-normal">{{ $student->programme ?? 'N/A' }}</span>
                                    </h6>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Center</p>
                                    <h6 class="mb-0 fw-bold">{{ $student->user->campus->name ?? 'Main Campus' }}</h6>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <p class="text-muted mb-1 text-uppercase small">Admission Status</p>
                                    <h6 class="mb-0 fw-bold">
                                        @if($student->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    <div class="card p-3 shadow-sm border-0">
                        <div
                            class="card-header bg-white border-0 pb-0 mb-3 px-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="ti ti-receipt text-primary me-2"></i> Payment History</h5>
                            <span class="badge bg-primary">{{ $transactions->count() }} Transactions
                                Found</span>
                        </div>
                        <div class="table-responsive">
                            <table id="history-table" class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Session</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Method</th>
                                        <th>Payment Status</th>
                                        <th class="text-end">Amount (₦)</th>
                                        <th class="text-end">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $txn)
                                        <tr>
                                            <td>
                                                <span class="d-none">{{ $txn->created_at->format('Ymd') }}</span>
                                                {{ $txn->created_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $txn->session ?? 'N/A' }}</span>
                                            </td>
                                            <td class="font-monospace text-primary fw-bold">
                                                {{ $txn->refernce_number ?? $txn->reference }}</td>
                                            <td>{{ Str::title(str_replace('_', ' ', $txn->payment_type)) }}</td>
                                            <td>
                                                @if(strtolower($txn->gateway) == 'monnify')
                                                    <span class="badge bg-info cursor-pointer" data-bs-toggle="tooltip"
                                                        title="Monnify">Online</span>
                                                @elseif(strtolower($txn->gateway) == 'paystack')
                                                    <span class="badge bg-primary cursor-pointer" data-bs-toggle="tooltip"
                                                        title="Paystack">Online</span>
                                                @elseif(strtolower($txn->gateway) == 'manual')
                                                    <span class="badge bg-secondary cursor-pointer" data-bs-toggle="tooltip"
                                                        title="Manual Upload">Manual</span>
                                                @else
                                                    <span class="badge bg-dark">{{ ucfirst($txn->gateway) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($txn->payment_status == 1 || strtolower($txn->status) == 'paid')
                                                    <span class="badge bg-success"><i class="ti ti-check me-1"></i> Paid</span>
                                                @elseif($txn->payment_status == 0)
                                                    <span class="badge bg-warning text-dark"><i class="ti ti-clock me-1"></i>
                                                        Pending</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="ti ti-x me-1"></i> Failed</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">
                                                ₦{{ number_format($txn->amount, 2) }}
                                            </td>
                                            <td class="text-end">
                                                @if($txn->payment_status == 1)
                                                    <a href="{{ route('bursary.student.receipt', $txn->reference ?? $txn->refernce_number) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-printer"></i> Receipt
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="ti ti-file-analytics fs-1 d-block mb-2"></i>
                                                No payment history found for this student.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
        @if($student)
            <!-- DataTables & Buttons -->
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

            <script>
                $(document).ready(function () {
                    $('#history-table').DataTable({
                        dom: 'lBfrtip',
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        order: [[0, 'desc']], // Order by Date descending initially
                        paging: true,
                        searching: true,
                        info: true
                    });
                });
            </script>
        @endif
    @endpush
@endsection
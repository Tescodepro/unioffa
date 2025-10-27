@extends('layouts.app')

@section('title', 'Agent List')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header d-md-flex d-block align-items-center justify-content-between mb-3">
                    <h3 class="page-title mb-2">Agent Applications</h3>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">All Agent Applications</h5>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table id="agentTable" class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Account Details</th>
                                        <th>Status</th>
                                        <th>Unique Code</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($agentApplications as $agent)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $agent->first_name }} {{ $agent->middle_name }} {{ $agent->last_name }}
                                            </td>
                                            <td>{{ $agent->email }}</td>
                                            <td>{{ $agent->phone }}</td>
                                            <td>
                                                <strong>Bank:</strong> {{ $agent->bank_name }}<br>
                                                <strong>Name:</strong> {{ $agent->account_name }}<br>
                                                <strong>No:</strong> {{ $agent->account_number }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($agent->status == 'pending') bg-warning text-dark 
                                                @elseif($agent->status == 'approved') bg-success 
                                                @else bg-danger @endif">
                                                    {{ ucfirst($agent->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $agent->unique_code ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#statusModal{{ $agent->id }}">
                                                    Change Status
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal for this agent -->
                                        <div class="modal fade" id="statusModal{{ $agent->id }}" tabindex="-1"
                                            aria-labelledby="statusModalLabel{{ $agent->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('admin.agent.application.update_status') }}">
                                                        @csrf
                                                        <input type="hidden" name="agent_id" value="{{ $agent->id }}">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="statusModalLabel{{ $agent->id }}">
                                                                Change Status for {{ $agent->first_name }}
                                                                {{ $agent->last_name }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Select New Status</label>
                                                                <select name="status" class="form-select" required>
                                                                    @if ($agent->status == 'approved')
                                                                        <option value="approved"
                                                                            {{ $agent->status == 'approved' ? 'selected' : '' }}>
                                                                            Approved</option>
                                                                    @else
                                                                        <option value="pending"
                                                                            {{ $agent->status == 'pending' ? 'selected' : '' }}>
                                                                            Pending</option>
                                                                        <option value="approved"
                                                                            {{ $agent->status == 'approved' ? 'selected' : '' }}>
                                                                            Approved</option>
                                                                        <option value="rejected"
                                                                            {{ $agent->status == 'rejected' ? 'selected' : '' }}>
                                                                            Declined</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save
                                                                Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted p-4">
                                                No agent applications found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Export Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#agentTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                order: [
                    [0, 'asc']
                ],
                pageLength: 10
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Report by Department')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Report by Department</h4>
                    <div class="d-flex align-items-center gap-3">
                        <form method="GET" action="{{ route('bursary.reports.department') }}"
                            class="d-flex align-items-center m-0">
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
                        <div>
                            <a href="{{ route('bursary.reports.export', ['type' => 'department', 'format' => 'pdf']) }}?session={{ $selectedSession }}"
                                class="btn btn-sm btn-danger">
                                <i class="ti ti-file-type-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('bursary.reports.export', ['type' => 'department', 'format' => 'xlsx']) }}?session={{ $selectedSession }}"
                                class="btn btn-sm btn-success">
                                <i class="ti ti-file-spreadsheet"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card p-3 shadow-sm">
                    <div class="table-responsive">
                        <table id="department-report-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Faculty</th>
                                    <th>Department</th>
                                    <th>Total Transactions</th>
                                    <th>Expected (₦)</th>
                                    <th>Received (₦)</th>
                                    <th>Outstanding (₦)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row['faculty'] }}</td>
                                        <td>{{ $row['department'] }}</td>
                                        <td>{{ $row['total_transactions'] }}</td>
                                        <td>{{ number_format($row['expected'], 2) }}</td>
                                        <td>{{ number_format($row['received'], 2) }}</td>
                                        <td>{{ number_format($row['outstanding'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
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
                $('#department-report-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    paging: true,
                    searching: true,
                    info: true,
                    ordering: true,
                });
            });
        </script>
    @endpush
@endsection
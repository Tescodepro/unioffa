@extends('layouts.app')

@section('title', 'Report by Level')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Report by Level</h4>
                    <div class="d-flex align-items-center gap-3">
                        <form method="GET" action="{{ route('bursary.reports.level') }}"
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
                            <a href="{{ route('bursary.reports.export', ['type' => 'level', 'format' => 'pdf']) }}?session={{ $selectedSession }}"
                                class="btn btn-sm btn-danger">
                                <i class="ti ti-file-type-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('bursary.reports.export', ['type' => 'level', 'format' => 'xlsx']) }}?session={{ $selectedSession }}"
                                class="btn btn-sm btn-success">
                                <i class="ti ti-file-spreadsheet"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                @foreach($data as $campusName => $centers)
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="ti ti-building-community me-2 text-primary"></i>
                                {{ $campusName }} Section
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapseLevel-{{ Str::slug($campusName) }}">
                                <i class="ti ti-arrows-up-down"></i> Toggle Section
                            </button>
                        </div>
                        <div id="collapseLevel-{{ Str::slug($campusName) }}" class="collapse show">
                            <div class="card-body p-4">
                                @foreach($centers as $centerLabel => $levels)
                                    <div class="mb-5 last-child-mb-0">
                                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                            <span class="avatar avatar-xs rounded-circle bg-primary text-white">
                                                <i class="ti ti-map-pin fs-12"></i>
                                            </span>
                                            <h6 class="mb-0 fw-bold text-primary">{{ $centerLabel }}</h6>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped report-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Level</th>
                                                        <th>Total Students</th>
                                                        <th>Expected (₦)</th>
                                                        <th>Received (₦)</th>
                                                        <th>Outstanding (₦)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($levels as $key => $row)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $row['level'] }}</td>
                                                            <td>{{ $row['total_students'] }}</td>
                                                            <td>{{ number_format($row['expected'], 2) }}</td>
                                                            <td>{{ number_format($row['received'], 2) }}</td>
                                                            <td class="{{ $row['outstanding'] > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                                                {{ number_format($row['outstanding'], 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

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
                $('.report-table').DataTable({
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
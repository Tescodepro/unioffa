@extends('layouts.app')
@section('title', 'Incomplete Applications - ICT Management')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                @include('layouts.flash-message')

                <!-- Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">Incomplete Applications</h3>
                        <p class="text-muted mb-0">Manage applications that were submitted but are missing required modules.
                        </p>
                    </div>
                </div>

                <!-- Table -->
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle" id="incomplete-table">
                            <thead>
                                <tr>
                                    <th>Reg. No.</th>
                                    <th>Applicant Name</th>
                                    <th>Session</th>
                                    <th>Application Type</th>
                                    <th>Missing Modules</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incompleteApplications as $app)
                                    <tr>
                                        <td>{{ $app->user->registration_no ?? $app->user->username }}</td>
                                        <td>{{ $app->user->full_name }}</td>
                                        <td>{{ $app->academic_session }}</td>
                                        <td>{{ $app->applicationSetting->name ?? '—' }}</td>
                                        <td class="text-danger fw-semibold">{{ $app->missing_modules }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('ict.applications.unsubmit', $app->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    onclick="return confirm('Are you sure you want to unsubmit this application so the user can complete it?')">
                                                    <i class="ti ti-arrow-back-up"></i> Unsubmit
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No incomplete submitted applications
                                            found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

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
            $('#incomplete-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                searching: true,
                ordering: true,
                info: true,
            });
        });
    </script>
@endpush
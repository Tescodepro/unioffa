@extends('layouts.app')

@section('title', 'Application Settings')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Application Settings</h3>
                        <p class="text-muted mb-0">Manage admission application types and fees.</p>
                    </div>
                    <div>
                        <a href="{{ route('ict.application_settings.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus"></i> Create New
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Application Types List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="application-settings-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Session</th>
                                        <th>App. Fee</th>
                                        <th>Accept. Fee</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                        <tr>
                                            <td>
                                                <strong>{{ $setting->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($setting->description, 30) }}</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark">{{ $setting->application_code }}</span>
                                            </td>
                                            <td>{{ $setting->academic_session }}</td>
                                            <td>₦{{ number_format($setting->application_fee, 2) }}</td>
                                            <td>₦{{ number_format($setting->acceptance_fee, 2) }}</td>
                                            <td>
                                                @if($setting->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif

                                                @if(!$setting->enabled)
                                                    <span class="badge bg-warning text-dark ml-1">Hidden</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('ict.application_settings.edit', $setting->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="ti ti-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                $('#application-settings-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        search: "Search Applications:",
                        lengthMenu: "Show _MENU_ entries",
                    }
                });
            });
        </script>
    @endpush
@endsection
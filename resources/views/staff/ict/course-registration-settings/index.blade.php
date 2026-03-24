@extends('layouts.app')

@section('title', 'Course Registration Settings')

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
                        <h3 class="page-title mb-1">Course Registration Settings</h3>
                        <p class="text-muted mb-0">Manage closing dates and late registration fees.</p>
                    </div>
                    <div>
                        <a href="{{ route('ict.course-registration-settings.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus"></i> Create New
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Settings List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="course-settings-table">
                                <thead>
                                    <tr>
                                        <th>Campus</th>
                                        <th>Entry Modes</th>
                                        <th>Session / Semester</th>
                                        <th>Closing Date</th>
                                        <th>Late Fee</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                        <tr>
                                            <td>{{ $setting->campus->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($setting->entry_mode)
                                                    @foreach($setting->entry_mode as $mode)
                                                        <span class="badge bg-light text-dark">{{ $mode }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-secondary">All</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $setting->session ?? 'All Sessions' }} <br>
                                                <small class="text-muted">{{ $setting->semester ?? 'All Semesters' }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($setting->closing_date)->format('d M, Y h:i A') }}</td>
                                            <td>₦{{ number_format($setting->late_registration_fee, 2) }}</td>
                                            <td>
                                                @if(now()->gt($setting->closing_date))
                                                    <span class="badge bg-danger">Closed</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('ict.course-registration-settings.edit', $setting->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="ti ti-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('ict.course-registration-settings.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="ti ti-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
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
                $('#course-settings-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                    }
                });
            });
        </script>
    @endpush
@endsection

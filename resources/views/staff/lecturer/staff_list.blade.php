@extends('layouts.app')

@section('title', 'Staff List')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header" style="display: inline">
                    <div class="row align-items-center mb-4">
                        <div class="col-sm-6">
                            <h3 class="page-title"><i class="fas fa-users-cog text-primary me-2"></i>Staff Management</h3>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                <i class="fas fa-plus me-1"></i> Add New Staff
                            </button>
                        </div>
                    </div>
                </div>

                @include('layouts.flash-message')

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Staff Directory</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="staffTable" class="table table-hover table-center mb-0 w-100 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Staff Name</th>
                                                <th>Staff No</th>
                                                <th>Faculty</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($staffs as $index => $staff)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="fw-semibold">{{ $staff->user->first_name ?? '' }} {{ $staff->user->last_name ?? '' }}</td>
                                                    <td>{{ $staff->staff_no }}</td>
                                                    <td>{{ $staff->faculty->faculty_name ?? 'N/A' }}</td>
                                                    <td>{{ $staff->department->department_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($staff->status === 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="actions d-flex justify-content-end gap-2">
                                                            <button class="btn btn-sm bg-warning-light text-warning" data-bs-toggle="modal"
                                                                data-bs-target="#editStaffModal{{ $staff->id }}" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm bg-danger-light text-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteStaffModal{{ $staff->id }}" title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Edit Staff Modal -->
                                                @include('staff.lecturer.modals.edit_staff_modal', ['staff' => $staff])

                                                <!-- Delete Staff Modal -->
                                                @include('staff.lecturer.modals.delete_staff_modal', ['staff' => $staff])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Staff Modal -->
                @include('staff.lecturer.modals.add_staff_modal')

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
            $('#staffTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true
            });
        });
    </script>
@endpush
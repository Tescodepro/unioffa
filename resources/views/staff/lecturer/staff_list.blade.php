@extends('layouts.app')

@section('title', 'Staff List')



@section('content')
    <div class="main-wrapper">

        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-5">
                    <div>
                        <h3 class="page-title mb-1">Staff List</h3>
                        <p class="text-muted mb-0">View, filter, and manage all staff accounts.</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add
                            Staff</button>
                    </div>
                </div>

                @include('layouts.flash-message')

                <!-- Staff Table -->
                <table id="staffTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Staff No</th>
                            <th>Faculty</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffs as $index => $staff)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $staff->user->first_name ?? '' }} {{ $staff->user->last_name ?? '' }}</td>
                                <td>{{ $staff->staff_no }}</td>
                                <td>{{ $staff->faculty->faculty_name ?? 'N/A' }}</td>
                                <td>{{ $staff->department->department_name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($staff->status) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editStaffModal{{ $staff->id }}">Edit</button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteStaffModal{{ $staff->id }}">Delete</button>
                                </td>
                            </tr>

                            <!-- Edit Staff Modal -->
                            @include('staff.lecturer.modals.edit_staff_modal', ['staff' => $staff])

                            <!-- Delete Staff Modal -->
                            @include('staff.lecturer.modals.delete_staff_modal', ['staff' => $staff])
                        @endforeach
                    </tbody>
                </table>

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
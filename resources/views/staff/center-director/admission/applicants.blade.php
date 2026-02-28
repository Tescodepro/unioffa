@extends('layouts.app')
@section('title', 'Applicants — {{ $campus?->name ?? "My Campus" }}')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Applicants — {{ $campus?->name ?? 'My Campus' }}</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('center-director.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Applicants</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                {{-- Filters (no campus selector — it is locked to this director's campus) --}}
                <div class="card mb-4">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Filter Applicants</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">Session</label>
                                <select name="academic_session" class="form-select form-select-sm">
                                    @foreach($sessions as $s)
                                        <option value="{{ $s }}" {{ $selectedSession == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Entry Mode</label>
                                <select name="entry_mode_id" class="form-select form-select-sm">
                                    <option value="">All Entry Modes</option>
                                    @foreach($entryModes as $mode)
                                        <option value="{{ $mode->id }}" {{ $selectedEntryModeId == $mode->id ? 'selected' : '' }}>
                                            {{ $mode->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Department</label>
                                <select name="department_id" class="form-select form-select-sm">
                                    <option value="">All Departments</option>
                                    @foreach($faculties as $faculty)
                                        <optgroup label="{{ $faculty->faculty_name }}">
                                            @foreach($faculty->departments as $dept)
                                                <option value="{{ $dept->id }}" {{ $selectedDeptId == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->department_name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="admitted" {{ $selectedStatus == 'admitted' ? 'selected' : '' }}>Admitted</option>
                                    <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Results Table --}}
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">{{ $applicants->count() }} Applicants Found</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="applicantsTable" class="display nowrap w-100">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Reg No</th>
                                        <th>Name</th>
                                        <th>Entry Mode</th>
                                        <th>1st Choice</th>
                                        <th>2nd Choice</th>
                                        <th>Status</th>
                                        <th>Approved Dept</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applicants as $applicant)
                                        @php
                                            $admStatus = optional($applicant->admissionList)->admission_status;
                                        @endphp
                                        <tr>
                                            <td>{{ $applicant->registration_no }}</td>
                                            <td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td>
                                            <td>{{ optional($applicant->applications->first()?->applicationSetting)->name ?? '—' }}</td>
                                            <td>{{ $applicant->courseOfStudy?->firstDepartment?->department_name ?? '—' }}</td>
                                            <td>{{ $applicant->courseOfStudy?->secondDepartment?->department_name ?? '—' }}</td>
                                            <td>
                                                @if($admStatus === 'admitted')
                                                    <span class="badge bg-success">Admitted</span>
                                                @elseif($admStatus === 'recommended')
                                                    <span class="badge bg-info">Recommended</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($applicant->admissionList?->department)->department_name ?? '—' }}</td>
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).ready(function () {
            $('#applicantsTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                responsive: true
            });
        });
    </script>
@endpush

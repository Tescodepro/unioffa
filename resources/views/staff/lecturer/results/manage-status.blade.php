@extends('layouts.app')

@section('title', 'Manage Result Status')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <h3 class="mb-4">Manage Result Status</h3>

            {{-- Filter Form --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-filter me-2"></i> Select Criteria</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('results.manage.status') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->department_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select" required>
                                <option value="">Select Level</option>
                                @foreach($levels as $lvl)
                                    <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Session</label>
                            <select name="session" class="form-select" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $sess)
                                    <option value="{{ $sess }}" {{ request('session') == $sess ? 'selected' : '' }}>{{ $sess }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="">Select Semester</option>
                                <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>First Semester</option>
                                <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>Second Semester</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Load Results</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(request()->filled('department_id') && count($records) > 0)
                {{-- BULK ACTION FORM --}}
                <form action="{{ route('results.bulk.update') }}" method="POST" id="bulkForm">
                    @csrf
                    <input type="hidden" name="session" value="{{ request('session') }}">
                    <input type="hidden" name="semester" value="{{ request('semester') }}">

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0">Result List ({{ count($records) }})</h5>
                            
                            {{-- Bulk Action Controls --}}
                            <div class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm w-auto" required>
                                    <option value="">-- With Selected --</option>
                                    <option value="recommended">Recommend Selected</option>
                                    <option value="approved">Approve Selected</option>
                                    <option value="pending">Mark as Pending</option>
                                </select>
                                <button class="btn btn-primary btn-sm">Apply</button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Name</th>
                                            <th>Matric No</th>
                                            <th>Session/Sem</th>
                                            <th class="text-center">Units<br>(Offer/Pass)</th>
                                            <th class="text-center">Scores<br>(CA/Exam)</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">GPA</th>
                                            <th class="text-center">Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $row)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_students[]" value="{{ $row->matric_no }}" class="form-check-input student-checkbox">
                                                </td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->matric_no }}</td>
                                                <td>{{ $row->session }}<br><small class="text-muted">{{ $row->semester }}</small></td>
                                                
                                                <td class="text-center">
                                                    {{ $row->total_units }} / 
                                                    <span class="text-success">{{ $row->units_passed }}</span>
                                                </td>
                                                
                                                <td class="text-center">
                                                    {{ $row->total_ca }} / {{ $row->total_exam }}
                                                </td>
                                                
                                                <td class="text-center fw-bold">{{ $row->total_score }}</td>
                                                
                                                <td class="text-center fw-bold 
                                                    {{ $row->gpa >= 3.5 ? 'text-success' : ($row->gpa < 1.5 ? 'text-danger' : 'text-dark') }}">
                                                    {{ number_format($row->gpa, 2) }}
                                                </td>
                                                
                                                <td class="text-center">
                                                    @if($row->current_status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($row->current_status == 'recommended')
                                                        <span class="badge bg-info">Recommended</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @endif
                                                </td>
                                                
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#statusModal{{ str_replace('/', '', $row->matric_no) }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    {{-- Link to individual transcript --}}
                                                    <a href="{{ route('transcript.search', ['matric' => $student->username]) }}" 
                                                       class="btn btn-sm btn-info text-white">
                                                       <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Individual Modals (Outside the main form to avoid nesting issues) --}}
                @foreach($records as $row)
                    <div class="modal fade" id="statusModal{{ str_replace('/', '', $row->matric_no) }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Update {{ $row->matric_no }}</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('results.update.status') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="matric_no" value="{{ $row->matric_no }}">
                                        <input type="hidden" name="session" value="{{ $row->session }}">
                                        <input type="hidden" name="semester" value="{{ $row->semester }}">

                                        <p>Update status for all courses in {{ $row->semester }} {{ $row->session }}?</p>

                                        <div class="mb-3">
                                            <label class="form-label">New Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="pending" {{ $row->current_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                {{-- <option value="recommended" {{ $row->current_status == 'recommended' ? 'selected' : '' }}>Recommended</option> --}}
                                                <option value="approved" {{ $row->current_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

            @elseif(request()->filled('department_id'))
                <div class="alert alert-info mt-4">No results found for the selected criteria.</div>
            @endif

        </div>
    </div>
</div>

{{-- Javascript for "Select All" --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });
</script>
@endsection
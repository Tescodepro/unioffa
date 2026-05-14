@extends('layouts.app')
@section('title', 'Change Student Level')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">
                <!-- Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Student Level Management</h3>
                        <p class="text-muted mb-0">Update academic levels for individual students or bulk groups</p>
                    </div>
                    @include('layouts.flash-message')
                </div>

                <div class="row g-4">
                    <!-- Specific Student Update -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 fw-bold">Individual Update</h5>
                            </div>
                            <div class="card-body pt-0">
                                <form action="{{ route('ict.students.change-level') }}" method="GET" class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Find Student by Matric No</label>
                                    <div class="input-group">
                                        <input type="text" name="search_matric" class="form-control border-0 bg-light shadow-none" 
                                            placeholder="e.g. 23/FAS/CSC/001" value="{{ request('search_matric') }}" required>
                                        <button type="submit" class="btn btn-primary px-3">
                                            <i class="ti ti-search"></i>
                                        </button>
                                    </div>
                                </form>

                                @if($specificStudent)
                                    <div class="p-3 bg-light rounded-3 border mb-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                                <i class="ti ti-user fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $specificStudent->user->first_name }} {{ $specificStudent->user->last_name }}</h6>
                                                <p class="text-muted small mb-0">{{ $specificStudent->matric_no }}</p>
                                            </div>
                                        </div>
                                        <div class="row g-2 small mb-3">
                                            <div class="col-6">
                                                <span class="text-muted">Current Level:</span>
                                                <span class="badge bg-soft-info text-info rounded-pill ms-1">{{ $specificStudent->level }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Department:</span>
                                                <span class="d-block fw-semibold text-truncate">{{ $specificStudent->department->department_name ?? 'N/A' }}</span>
                                            </div>
                                        </div>

                                        <form action="{{ route('ict.students.change-level.specific') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $specificStudent->id }}">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Select New Level</label>
                                                <select name="new_level" class="form-select border-0 bg-white shadow-sm" required>
                                                    @foreach([100, 200, 300, 400, 500, 600, 700] as $lvl)
                                                        <option value="{{ $lvl }}" {{ $specificStudent->level == $lvl ? 'selected' : '' }}>
                                                            Level {{ $lvl }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-dark w-100 rounded-3">
                                                <i class="ti ti-device-floppy me-1"></i> Update Level
                                            </button>
                                        </form>
                                    </div>
                                @elseif(request('search_matric'))
                                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10">
                                        <p class="mb-0 small"><i class="ti ti-alert-triangle-filled me-1"></i> No student found with matric number: <strong>{{ request('search_matric') }}</strong></p>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <img src="{{ asset('assets/img/illustrations/search-student.svg') }}" alt="Search" class="img-fluid mb-3" style="max-height: 120px;">
                                        <p class="text-muted small px-4">Enter a matriculation number above to perform an individual level change.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Category Update -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 fw-bold text-primary">Bulk Progress Management</h5>
                                <p class="text-muted small mb-0">Use filters to move a category of students to a new level</p>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('ict.students.change-level.bulk') }}" method="POST" id="bulkLevelForm">
                                    @csrf
                                    <div class="row g-3">
                                        <!-- Source Criteria -->
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Target Department</label>
                                            <select name="department_id" class="form-select select2">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-premium">Center / Campus</label>
                                            <select name="campus_id" class="form-select select2">
                                                <option value="">All Centers</option>
                                                @foreach($campuses as $campus)
                                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Admission Year</label>
                                            <select name="admission_session" class="form-select">
                                                <option value="">All Sessions</option>
                                                @foreach($admissionSessions as $session)
                                                    <option value="{{ $session }}">{{ $session }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Programme</label>
                                            <select name="programme" class="form-select">
                                                <option value="">All Programmes</option>
                                                @foreach($programmes as $prog)
                                                    <option value="{{ $prog }}">{{ $prog }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label-premium">Entry Mode</label>
                                            <select name="entry_mode" class="form-select">
                                                <option value="">All Entry Modes</option>
                                                @foreach($entryModes as $mode)
                                                    <option value="{{ $mode->code }}">{{ $mode->name }} ({{ $mode->code }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12"><hr class="my-3 opacity-25"></div>

                                        <!-- Update Logic -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-soft-warning rounded-3 border border-warning border-opacity-25">
                                                <label class="form-label fw-bold text-dark small mb-1">Current Level</label>
                                                <select name="target_level" class="form-select border-0 shadow-sm" required>
                                                    <option value="" disabled selected>-- Select Source Level --</option>
                                                    @foreach([100, 200, 300, 400, 500, 600, 700] as $lvl)
                                                        <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text x-small text-dark opacity-75 mt-2">Only students currently at this level will be affected.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="p-3 bg-soft-success rounded-3 border border-success border-opacity-25">
                                                <label class="form-label fw-bold text-dark small mb-1">Move to Level</label>
                                                <select name="new_level" class="form-select border-0 shadow-sm" required>
                                                    <option value="" disabled selected>-- Select Destination --</option>
                                                    @foreach([100, 200, 300, 400, 500, 600, 700] as $lvl)
                                                        <option value="{{ $lvl }}">Level {{ $lvl }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text x-small text-dark opacity-75 mt-2">Selected students will be progressed to this level.</div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 d-flex align-items-center rounded-4">
                                                <i class="ti ti-alert-triangle-filled me-3 fs-3"></i>
                                                <div class="small">
                                                    <strong>Caution:</strong> This action will modify multiple student records at once. 
                                                    Please verify your filters carefully before proceeding. This action cannot be easily undone.
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-sm mt-2" 
                                                onclick="return confirm('Are you sure you want to perform this bulk level update?')">
                                                <i class="ti ti-rocket me-1"></i> Apply Bulk Level Change
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .form-label-premium { font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 0.5rem; }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .bg-soft-dark { background-color: rgba(33, 37, 41, 0.1); }
        .rounded-4 { border-radius: 1rem !important; }
        .x-small { font-size: 0.7rem; }
    </style>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option"
            });
        });
    </script>
@endpush

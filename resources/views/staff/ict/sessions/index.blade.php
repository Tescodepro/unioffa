@extends('layouts.app')
@section('title', 'Session Management & Progression')

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
                        <h3 class="page-title mb-1">Academic Session & Progression</h3>
                        <p class="text-muted mb-0">Transition to new sessions and manage student academic progression</p>
                    </div>
                    @include('layouts.flash-message')
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSessionModal">
                            <i class="ti ti-plus me-1"></i> New Session
                        </button>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                                        <i class="ti ti-users fs-2 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted small mb-0 fw-bold text-uppercase ls-1">Active Students</h6>
                                        <h3 class="mb-0 fw-bold">{{ number_format($studentStats['active_students']) }}</h3>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3">
                                        <i class="ti ti-stairs-up fs-2 text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted small mb-0 fw-bold text-uppercase ls-1">Progression Ready</h6>
                                        <h3 class="mb-0 fw-bold text-success">{{ number_format($studentStats['candidates_for_progression']) }}</h3>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    @php $progPerc = $studentStats['active_students'] > 0 ? ($studentStats['candidates_for_progression'] / $studentStats['active_students']) * 100 : 0; @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $progPerc }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-info bg-opacity-10 p-3 rounded-4 me-3">
                                        <i class="ti ti-school fs-2 text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted small mb-0 fw-bold text-uppercase ls-1">Graduation Ready</h6>
                                        <h3 class="mb-0 fw-bold text-info">{{ number_format($studentStats['candidates_for_graduation']) }}</h3>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    @php $gradPerc = $studentStats['active_students'] > 0 ? ($studentStats['candidates_for_graduation'] / $studentStats['active_students']) * 100 : 0; @endphp
                                    <div class="progress-bar bg-info" style="width: {{ $gradPerc }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Session Toggler -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="card-title mb-0 fw-bold">Active Session Control</h5>
                            </div>
                            <div class="card-body">
                                <div class="p-4 bg-light rounded-4 border border-dashed mb-4 text-center">
                                    <span class="text-muted small text-uppercase fw-bold ls-1 d-block mb-1">Current Active Session</span>
                                    <h2 class="fw-bold text-primary mb-0">{{ $activeSession->name ?? 'None' }}</h2>
                                </div>

                                <form action="{{ route('ict.sessions.set-active') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Switch to New Session</label>
                                        <select name="session_id" class="form-select border-0 bg-light shadow-none py-3 px-4 rounded-3">
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ ($activeSession && $activeSession->id == $session->id) ? 'selected' : '' }}>
                                                    {{ $session->name }} {{ ($activeSession && $activeSession->id == $session->id) ? '(Current)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text small mt-2">
                                            <i class="ti ti-info-circle me-1"></i> Changing the active session will update which fees are visible to students.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-sm">
                                        <i class="ti ti-check me-1"></i> Activate Selected Session
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Progression Actions -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold text-success">Academic Progression Tool</h5>
                                <span class="badge bg-soft-success text-success px-3 rounded-pill">Safe Execution Mode</span>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 rounded-4 p-4 mb-4">
                                    <div class="d-flex">
                                        <i class="ti ti-alert-triangle-filled fs-1 me-3 text-warning"></i>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Bulk Progression Warning</h6>
                                            <p class="small text-dark opacity-75 mb-0">
                                                This action will increment the level of all <strong>Active</strong> students by 100. 
                                                Students who have reached their programme's maximum level (e.g. 400L or 500L) will be automatically marked as <strong>Graduated</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light rounded-4 p-4 mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold mb-1">Session-End Processing</h6>
                                            <p class="text-muted small mb-0">Process all students for the move to {{ $sessions->where('name', '>', $activeSession->name ?? '')->first()->name ?? 'Next' }} session.</p>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                            <form action="{{ route('ict.sessions.progress') }}" method="POST" onsubmit="return confirm('CRITICAL ACTION: Are you sure you want to progress all students? This cannot be easily reversed.')">
                                                @csrf
                                                <button type="submit" class="btn btn-success px-4 py-2 rounded-3 shadow-sm">
                                                    <i class="ti ti-rocket me-1"></i> Run Progression
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 bg-soft-info text-info rounded-4 border border-info border-opacity-25 small">
                                    <i class="ti ti-bulb-filled me-2"></i>
                                    <strong>Pro-tip:</strong> Before running progression, ensure all results for the current session have been finalized and uploaded.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Session Modal -->
    <div class="modal fade" id="newSessionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Create New Academic Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ict.sessions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Session Name</label>
                            <input type="text" name="name" class="form-control border-0 bg-light shadow-none py-3 px-4 rounded-3" 
                                placeholder="e.g. 2026/2027" required>
                            <div class="form-text small mt-2">Format must be YYYY/YYYY (e.g. 2026/2027)</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">Create Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .ls-1 { letter-spacing: 1px; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
        .rounded-4 { border-radius: 1rem !important; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    </style>
@endsection

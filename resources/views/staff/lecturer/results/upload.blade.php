@extends('layouts.app')
@section('title', 'Result Management')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Result Management</h3>
                        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('lecturer.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Upload Results</li>
                        </ol></nav>
                    </div>
                </div>

                @include('layouts.flash-message')

                {{-- Upload Report --}}
                @if (session('upload_report'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h6 class="fw-semibold mb-2"><i class="ti ti-clipboard-list me-1"></i> Upload Summary</h6>
                        <ul class="mb-0 ps-3">
                            @foreach (session('upload_report.uploaded', []) as $msg)
                                <li class="text-success">{{ $msg }}</li>
                            @endforeach
                            @foreach (session('upload_report.skipped_not_student', []) as $msg)
                                <li class="text-danger">{{ $msg }}</li>
                            @endforeach
                            @foreach (session('upload_report.skipped_not_registered', []) as $msg)
                                <li class="text-warning">{{ $msg }}</li>
                            @endforeach
                            @foreach (session('upload_report.errors', []) as $msg)
                                <li class="text-danger">{{ $msg }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">

                    {{-- ── DOWNLOAD CARD ──────────────────────────────────────── --}}
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header border-0 bg-light">
                                <h5 class="card-title mb-0"><i class="ti ti-download me-2 text-primary"></i>Download Result Sheet</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Download a blank Excel template pre-filled with registered students for a course.</p>
                                <form action="{{ route('staff.results.download') }}" method="GET">
                                    <div class="mb-3">
                                        <label for="download_course_code" class="form-label">Course</label>
                                        <select id="download_course_code" name="course_code" class="form-select select2" required>
                                            <option value="">Choose Course</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->course_code }}">{{ $course->course_title }} ({{ $course->course_code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-sm-6">
                                            <label for="download_session" class="form-label">Session</label>
                                            <select name="session" id="download_session" class="form-select select2" required>
                                                <option value="">Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->name }}">{{ $session->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="download_semester" class="form-label">Semester</label>
                                            <select name="semester" id="download_semester" class="form-select select2" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->code }}">{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-download me-1"></i> Download Excel Sheet
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ── UPLOAD CARD ────────────────────────────────────────── --}}
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header border-0 bg-light">
                                <h5 class="card-title mb-0"><i class="ti ti-upload me-2 text-success"></i>Upload Result Sheet</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning py-2 small mb-3">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Ensure the uploaded Excel file has columns named <code>Matric No, CA, Examination</code>.
                                    The system will automatically normalize these headers.
                                </div>
                                <form action="{{ route('staff.results.process') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3 mb-3">
                                        <div class="col-12">
                                            <label for="course_code" class="form-label">Course</label>
                                            <select id="course_code" name="course_code" class="form-select select2" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->course_code }}">{{ $course->course_title }} ({{ $course->course_code }})</option>
                                                @endforeach
                                            </select>
                                            @if($courses->isEmpty())
                                                <small class="text-muted">No courses assigned to you yet.</small>
                                            @endif
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="session" class="form-label">Session</label>
                                            <select name="session" id="session" class="form-select select2" required>
                                                <option value="">Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->name }}">{{ $session->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($sessions->isEmpty())
                                                <small class="text-muted">No active sessions available.</small>
                                            @endif
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="semester" class="form-label">Semester</label>
                                            <select name="semester" id="semester" class="form-select select2" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->code }}">{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($semesters->isEmpty())
                                                <small class="text-muted">No active semesters available.</small>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <label for="file" class="form-label">Excel File</label>
                                            <input type="file" name="file" id="file"
                                                class="form-control" accept=".xlsx,.xls" required>
                                            <small class="text-muted">Columns: <strong>Matric No, CA, Examination</strong></small>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100"
                                        @if($courses->isEmpty() || $sessions->isEmpty() || $semesters->isEmpty()) disabled @endif>
                                        <i class="ti ti-upload me-1"></i> Upload Results
                                    </button>

                                    @if($courses->isEmpty() || $sessions->isEmpty() || $semesters->isEmpty())
                                        <small class="text-danger d-block mt-2 text-center">
                                            Cannot upload: missing courses, sessions, or semesters.
                                        </small>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                </div>{{-- end row --}}

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({ placeholder: 'Select an option', allowClear: true });
        });
    </script>
@endpush
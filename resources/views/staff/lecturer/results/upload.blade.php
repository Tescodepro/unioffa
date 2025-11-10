@extends('layouts.app')

@section('title', 'Result Management')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <h3 class="mb-4">Result Management (Download & Upload)</h3>

                <div class="row g-4">
                    <!-- ====================== DOWNLOAD CARD ====================== -->
                    <div class="col-lg-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Download Result Sheet</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('staff.results.download') }}" method="GET">
                                    <div class="mb-3">
                                        <label for="download_course_id" class="form-label">Select Course</label>
                                        <select id="download_course_id" name="course_id"
                                            class="form-select form-select-sm select2" required>
                                            <option value="">Choose Course</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->course_title }}
                                                    ({{ $course->course_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="download_session" class="form-label">Session</label>
                                            <select name="session" id="download_session" 
                                                class="form-select form-select-sm select2" required>
                                                <option value="">Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->name }}">{{ $session->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="download_semester" class="form-label">Semester</label>
                                            <select name="semester" id="download_semester"
                                                class="form-select form-select-sm select2" required>
                                                <option value="">Select Semester</option>
                                                @php
                                                    var_dump($semesters);
                                                @endphp
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->code }}">{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa fa-download me-1"></i> Download Excel Sheet
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ====================== UPLOAD CARD ====================== -->
                    @include('layouts.flash-message')
                    @if (session('upload_report'))
                        <div class="alert alert-info mt-3">
                            <h6>Upload Summary</h6>
                            <ul>
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
                        </div>
                    @endif


                    <div class="col-lg-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Upload Result Sheet</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('staff.results.process') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <p><strong>Note:</strong> Ensure the uploaded excel file has columns named
                                        <code>Matric No, CA, Examination</code>. The system will automatically normalize these headers.
                                    </p>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="course_id" class="form-label">Course</label>
                                            <select id="course_id" name="course_id"
                                                class="form-select form-select-sm select2" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->course_title }}
                                                        ({{ $course->course_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($courses->isEmpty())
                                                <small class="text-muted">No courses assigned to you yet.</small>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="session" class="form-label">Session</label>
                                            <select name="session" id="session" 
                                                class="form-select form-select-sm select2" required>
                                                <option value="">Select Session</option>
                                                @foreach ($sessions as $session)
                                                    <option value="{{ $session->name }}">{{ $session->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($sessions->isEmpty())
                                                <small class="text-muted">No active sessions available for result upload.</small>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="semester" class="form-label">Semester</label>
                                            <select name="semester" id="semester" 
                                                class="form-select form-select-sm select2" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->name }}">{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($semesters->isEmpty())
                                                <small class="text-muted">No active semesters available for result upload.</small>
                                            @endif
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="file" class="form-label">Upload Excel File</label>
                                            <input type="file" name="file" id="file"
                                                class="form-control form-control-sm" accept=".xlsx,.xls" required>
                                            <small class="text-muted">Ensure columns are: <strong>Matric No, CA,
                                                    Examination</strong></small>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100" 
                                        @if($courses->isEmpty() || $sessions->isEmpty() || $semesters->isEmpty()) disabled @endif>
                                        <i class="fa fa-upload me-1"></i> Upload Results
                                    </button>
                                    
                                    @if($courses->isEmpty() || $sessions->isEmpty() || $semesters->isEmpty())
                                        <small class="text-danger d-block mt-2 text-center">
                                            Cannot upload results. Missing required data (courses, sessions, or semesters).
                                        </small>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true
            });

            // Optional: DataTable initialization if you have a results table
            if ($('#resultsTable').length) {
                $('#resultsTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    responsive: true
                });
            }
        });
    </script>
@endpush
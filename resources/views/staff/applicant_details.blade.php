@extends('layouts.app')

@section('title', 'Admin Dashboard')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
<div class="main-wrapper">

    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Applicant Details</h4>
                </div>
                <div class="card-body">

                    <!-- Basic Applicant Info -->
                    <div class="mb-4">
                        <h5 class="fw-bold">{{ $application->user->full_name }}</h5>
                        <p><strong>Email:</strong> {{ $application->user->email }}</p>
                        <p><strong>Application Type:</strong> {{ $application->applicationSetting->name }}</p>
                        <p><strong>Academic Session:</strong> {{ $application->academic_session }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $application->submitted_by ? 'success' : 'secondary' }}">
                                {{ $application->submitted_by ? 'Submitted' : 'Not Submitted' }}
                            </span>
                        </p>
                    </div>

                    <hr>

                    <!-- Profile -->
                    @if(!empty($modules['profile']) && $modules['profile'])
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">Profile</div>
                            <div class="card-body">
                                @if($application->profile)
                                    <p><strong>Date of Birth:</strong> {{ $application->profile->date_of_birth ?? 'N/A' }}</p>
                                    <p><strong>Gender:</strong> {{ $application->profile->gender ?? 'N/A' }}</p>
                                    <p><strong>Address:</strong> {{ $application->profile->address ?? 'N/A' }}</p>
                                    <p><strong>State of Origin:</strong> {{ $application->profile->state_of_origin ?? 'N/A' }}</p>
                                    <p><strong>Nationality:</strong> {{ $application->profile->nationality ?? 'N/A' }}</p>
                                @else
                                    <p class="text-muted">No profile data submitted.</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- O’Level -->
                    @if(!empty($modules['olevel']) && $modules['olevel'])
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">O’Level Results</div>
                            <div class="card-body">
                                @foreach($application->olevels as $olevel)
                                    <h3>O-Level Results ({{ $olevel->exam_type }} - {{ $olevel->exam_year }})</h3>
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(is_array($olevel->subjects) && !empty($olevel->subjects))
                                                @foreach($olevel->subjects as $subject => $grade)
                                                    <tr>
                                                        <td>{{ $subject }}</td>
                                                        <td>{{ $grade }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2">No subjects available</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- JAMB / A-Level -->
                    @if(!empty($modules['jamb_detail']) && $modules['jamb_detail'])
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">JAMB Details</div>
                            <div class="card-body">
                                @if($application->jambDetail)
                                    <p><strong>Reg No:</strong> {{ $application->jambDetail->registration_number }}</p>
                                    <p><strong>Exam Year:</strong> {{ $application->jambDetail->exam_year }}</p>
                                    <p><strong>Type:</strong> {{ $application->jambDetail->jamb_type }}</p>
                                    <p><strong>Total Score:</strong> {{ $application->jambDetail->score ?? 'N/A' }}</p>
                                    @if(is_array($application->jambDetail->subject_scores) && !empty($application->jambDetail->subject_scores))
                                        <h6>Subject Scores:</h6>
                                        <ul>
                                            @foreach($application->jambDetail->subject_scores as $subject => $score)
                                                <li>{{ $subject }} : {{ $score }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No subject scores available.</p>
                                    @endif
                                @else
                                    <p class="text-muted">No JAMB / A-Level details submitted.</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Course of Study -->
                    @if(!empty($modules['course_of_study']) && $modules['course_of_study'])
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">Course of Study</div>
                            <div class="card-body">
                                <p><strong>First Choice:</strong> {{ $application->user->courseOfStudy?->firstDepartment?->department_name ?? 'N/A' }}</p>
                                <p><strong>Second Choice:</strong> {{ $application->user->courseOfStudy?->secondDepartment?->department_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Documents -->
                    @if(!empty($modules['documents']))
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">Documents</div>
                            <div class="card-body">
                                @forelse($application->documents as $doc)
                                    <p>
                                        <strong>{{ ucfirst($doc->type) }}</strong>: 
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">
                                            {{ $doc->original_name }}
                                        </a> ({{ $doc->file_size }})
                                    </p>
                                @empty
                                    <p class="text-muted">No documents uploaded.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Education History -->
                    @if(!empty($modules['education_history']) && $modules['education_history'])
                        <div class="card mb-4">
                            <div class="card-header bg-light fw-bold">Education History</div>
                            <div class="card-body">
                                @forelse($application->educationHistories as $edu)
                                    <p>
                                        <strong>{{ $edu->institution_name }}</strong>  
                                        ({{ $edu->start_date }} - {{ $edu->end_date }})<br>
                                        Qualification: {{ $edu->qualification ?? 'N/A' }}<br>
                                        Grade: {{ $edu->grade ?? 'N/A' }}
                                    </p>
                                @empty
                                    <p class="text-muted">No prior education history submitted.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <!-- Back Button -->
                    <div class="text-end">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- JSZip (for Excel export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- pdfmake (for PDF export) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 
            'csv', 
            'excel', 
            'pdf', 
            'print'
        ],
        responsive: true
    });
});
</script>

@endpush
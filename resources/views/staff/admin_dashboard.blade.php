@extends('layouts.app')

@section('title', 'Admin Dashboard')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="main-wrapper">

        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Administrator Dashboard</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex">
                        <select name="academic_session" class="form-control me-2" onchange="this.form.submit()">
                            @foreach ($sessions as $session)
                                <option value="{{ $session }}" {{ $selectedSession == $session ? 'selected' : '' }}>
                                    {{ $session }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Statistics Row -->
                <div class="row">
                    <!-- Applicants per Campus -->
                    <div class="col-xl-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h6>Total Applicants (by Campus)</h6>
                                <ul class="list-unstyled mt-3">
                                    @foreach ($campusApplicants as $campus)
                                        <li class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ $campus->name }}</span>
                                            <span class="badge bg-primary">{{ $campus->applicant_count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Applicants per Application Type -->
                    <div class="col-xl-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h6>Total Applicants (by Application Type)</h6>
                                <ul class="list-unstyled mt-3">
                                    @foreach ($applicationApplicants as $app)
                                        <li class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ $app->name }}</span>
                                            <span class="badge bg-info">{{ $app->applicant_count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admission Summary -->
                <div class="row my-4">
                    <div class="col-xl-6 col-sm-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Admitted Students ({{ $selectedSession }})</h6>
                                <h3>{{ $admittedCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-sm-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6>Pending / Not Admitted / Recommended</h6>
                                <h3>{{ $notAdmittedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="card-title">Filter Applicants</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3">
                            <div class="col-md-6">
                                <select name="campus_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Choose Campus --</option>
                                    @foreach ($campuses as $campus)
                                        <option value="{{ $campus->id }}"
                                            {{ $selectedCampusId == $campus->id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select name="application_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Choose Application Type --</option>
                                    @foreach ($applicationTypes as $appType)
                                        <option value="{{ $appType->id }}"
                                            {{ $selectedApplicationId == $appType->id ? 'selected' : '' }}>
                                            {{ $appType->name }} ({{ $appType->academic_session }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>


                <!-- Applicants Table -->
                @if ($students->count())
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Applicants in Selected Campus</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="usersTable" class="display nowrap" style="width:100%">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Applicant No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Application Type</th>
                                            <th>Admittion Status</th>
                                            <th>Department Aproved/Recommended</th>
                                            <th>Submittion</th>
                                            <th>Payment</th>
                                            <th>Applicant Details</th>
                                            <th>Admit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            @php
                                                $modules = json_decode(
                                                    $student->application_modules_enable ?? '[]',
                                                    true,
                                                );
                                            @endphp
                                            <tr>
                                                <td>{{ $student->registration_no }}</td>
                                                <td>{{ $student->full_name }}</td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->application_type ?? 'N/A' }}</td>
                                                <td>
                                                    @if (optional($student->admissionList)->admission_status === 'admitted')
                                                        <span class="badge bg-success">Admitted</span>
                                                    @elseif (optional($student->admissionList)->admission_status === 'recommended')
                                                        <span class="badge bg-warning">Recommended</span>
                                                    @elseif(optional($student->admissionList)->admission_status === 'pending' || empty($student->admissionList))
                                                        <span class="badge bg-warning">Pending</span>
                                                    @else
                                                        <button class="btn btn-danger btn-sm">Rejected</button>
                                                    @endif

                                                </td>
                                                <td>
                                                    {{ $student->admissionListDepartmet->department_name ?? 'N/A' }}
                                                </td>

                                                    
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $student->application_status === 'submitted' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($student->application_status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($student->payment_status == 1 && $student->payment_ref)
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif($student->payment_status == 1 && !$student->payment_ref)
                                                        <span class="badge bg-danger">Failed</span>
                                                    @else
                                                        <span class="badge bg-warning">Unpaid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.applicants.details', [$student->id, $student->application_id]) }}"
                                                        class="btn btn-sm btn-success">
                                                        View Details
                                                    </a>
                                                </td>

                                                <td>
                                                    @if (optional($student->admissionList)->admission_status !== 'admitted')
                                                        {{-- If application not submitted --}}
                                                        @if ($student->application_status !== 'submitted')
                                                            <span class="badge bg-secondary">Application Not
                                                                Submitted</span>

                                                            {{-- If Registrar or Vice-Chancellor --}}
                                                        @elseif (auth()->user()->hasUserType(['registrar', 'vice-chancellor']))
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#admitModal{{ $student->id }}">
                                                                Admit
                                                            </button>

                                                            {{-- If Administrator --}}
                                                        @elseif (auth()->user()->hasUserType('administrator'))
                                                            <button type="button" class="btn btn-sm btn-warning"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#recommendModal{{ $student->id }}">
                                                                Recommend
                                                            </button>
                                                        @endif

                                                        {{-- Admission Modal (Registrar / VC) --}}
                                                        <div class="modal fade" id="admitModal{{ $student->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="admitModalLabel{{ $student->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title">Confirm Admission</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>

                                                                    <form method="POST"
                                                                        action="{{ route('admin.admit', $student->id) }}"
                                                                        id="admitForm{{ $student->id }}"> <!-- Body -->
                                                                        <div class="modal-body">
                                                                            <center>
                                                                                <h4
                                                                                    class="text-danger fw-bold align-items-center">
                                                                                    ⚠️ You are about to admit this applicant
                                                                                    into the university. </h4>
                                                                                <hr>
                                                                            </center>
                                                                            <div class="mb-1"> <strong>Name:</strong>
                                                                                {{ $student->full_name }} </div>
                                                                            <div class="mb-1"> <strong>Application
                                                                                    Type:</strong>
                                                                                {{ $student->application_type }} </div>
                                                                            <div class="mb-1"> <strong>First
                                                                                    Choice:</strong>
                                                                                {{ $student->first_choice ?? 'N/A' }}
                                                                            </div>
                                                                            <div class="mb-3"> <strong>Second
                                                                                    Choice:</strong>
                                                                                {{ $student->second_choice ?? 'N/A' }}
                                                                            </div>

                                                                            <div class="mb-3"> <strong>Recommended Department
                                                                                    Choice:</strong>
                                                                                {{ $student->admissionListDepartmet->department_name ?? 'N/A' }}
                                                                            </div>
                                                                            <div class="mb-3"> <label
                                                                                    class="form-label">Final Course of
                                                                                    Study *</label> <select
                                                                                    class="form-select"
                                                                                    name="final_course"
                                                                                    form="admitForm{{ $student->id }}"
                                                                                    required>
                                                                                    <option value="">-- Select Final
                                                                                        Course --</option>
                                                                                    @foreach ($faculties as $faculty)
                                                                                        <optgroup
                                                                                            label="{{ $faculty->faculty_name }}">
                                                                                            @foreach ($departments->where('faculty_id', $faculty->id) as $dept)
                                                                                                <option
                                                                                                    value="{{ $dept->id }}">
                                                                                                    {{ $dept->department_name }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </optgroup>
                                                                                    @endforeach
                                                                                </select> </div>
                                                                            <div class="mb-3"> <label
                                                                                    class="form-label">Admission Status
                                                                                    *</label> <select class="form-select"
                                                                                    name="status" required>
                                                                                    <option value="">-- Select Final
                                                                                        Course --</option>
                                                                                    <option value="pending">pending
                                                                                    </option>
                                                                                    <option value="rejected">rejected
                                                                                    </option>
                                                                                    <option value="admitted" selected>
                                                                                        admitted</option>
                                                                                </select> </div>
                                                                        </div> <!-- Footer with form -->
                                                                        <div class="modal-footer"> @csrf <input
                                                                                type="hidden" name="application_id"
                                                                                value="{{ $student->application_id }}">
                                                                            <button type="submit"
                                                                                class="btn btn-success">Confirm
                                                                                Admit</button> <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Recommendation Modal (Administrator) --}}
                                                        <div class="modal fade" id="recommendModal{{ $student->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="recommendModalLabel{{ $student->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-warning text-white">
                                                                        <h5 class="modal-title">Recommend Admission</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"></button>
                                                                    </div>

                                                                    <form method="POST"
                                                                        action="{{ route('admin.recommend', $student->id) }}"
                                                                        id="admitForm{{ $student->id }}"> <!-- Body -->
                                                                        <div class="modal-body">
                                                                            <center>
                                                                                <h4 class="text-danger fw-bold align-items-center"> ⚠️ You are about to recommend this applicant to the university for admission</h4>
                                                                                <hr>
                                                                            </center>
                                                                            <div class="mb-1"> <strong>Name:</strong>
                                                                                {{ $student->full_name }} </div>
                                                                            <div class="mb-1"> <strong>Application
                                                                                    Type:</strong>
                                                                                {{ $student->application_type }} </div>
                                                                            <div class="mb-1"> <strong>First
                                                                                    Choice:</strong>
                                                                                {{ $student->first_choice ?? 'N/A' }}
                                                                            </div>
                                                                            <div class="mb-3"> <strong>Second
                                                                                    Choice:</strong>
                                                                                {{ $student->second_choice ?? 'N/A' }}
                                                                            </div>
                                                                            
                                                                            <div class="mb-3"> <strong>Recommended Department
                                                                                    Choice:</strong>
                                                                                {{ $student->admissionListDepartmet->department_name ?? 'N/A' }}
                                                                            </div>

                                                                            <div class="mb-3"> <label class="form-label">Final Course of Study *</label> 
                                                                                <select class="form-select"  name="final_course" orm="admitForm{{ $student->id }}" required>
                                                                                    <option value="">-- Select Final Course --</option>
                                                                                    @foreach ($faculties as $faculty)
                                                                                        <optgroup label="{{ $faculty->faculty_name }}">
                                                                                            @foreach ($departments->where('faculty_id', $faculty->id) as $dept)
                                                                                                <option value="{{ $dept->id }}"> {{ $dept->department_name }} </option>
                                                                                            @endforeach
                                                                                        </optgroup>
                                                                                    @endforeach
                                                                                </select> 
                                                                            </div>
                                                                            <div class="mb-3"> <label class="form-label">Admission Status *</label> 
                                                                                <select class="form-select" name="status" required>
                                                                                    <option value="">-- Select Final Course --</option>
                                                                                    <option value="recommended" selected>Recommend </option>
                                                                                </select> 
                                                                            </div>
                                                                        </div> 
                                                                        <div class="modal-footer" style="display: flex; justify-content: space-between;"> 
                                                                            @csrf 
                                                                            <input type="hidden" name="application_id" value="{{ $student->application_id }}">
                                                                            <button type="submit" class="btn btn-success">Recommend</button> 
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-success">Admitted</span>
                                                    @endif
                                                </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

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

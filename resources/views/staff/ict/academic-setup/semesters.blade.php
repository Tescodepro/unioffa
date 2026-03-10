@extends('layouts.app')

@section('title', 'Manage Academic Semesters')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Academic Setup - Semesters</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('ict.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Semesters</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        @if(auth()->user()->hasPermission('manage_semesters'))
                            <div class="mb-2">
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSemesterModal">
                                    <i class="ti ti-plus me-1"></i>Add Semester
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @include('layouts.flash-message')

                {{-- Inline validation errors: re-open add modal if errors exist --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="ti ti-alert-triangle me-1"></i>Please fix the following:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped custom-table datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Semester Name</th>
                                                <th>Code</th>
                                                <th>Overrides</th>
                                                <th>Status</th>
                                                <th>Result Upload</th>
                                                @if(auth()->user()->hasPermission('manage_semesters'))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($semesters as $key => $semester)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td class="fw-bold">{{ $semester->name }}</td>
                                                    <td><span class="badge bg-secondary">{{ $semester->code }}</span></td>
                                                    <td>
                                                        @if(!empty($semester->stream))
                                                            <span class="badge bg-primary-transparent mb-1" title="Streams">
                                                                <i class="ti ti-activity border-end pe-1 me-1"></i>
                                                                Stream {{ implode(', ', $semester->stream) }}
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($semester->campus_id))
                                                            <span class="badge bg-secondary-transparent mb-1" title="Campuses">
                                                                <i class="ti ti-map-pin border-end pe-1 me-1"></i>
                                                                @foreach($semester->campus_id as $cId)
                                                                    {{ $campuses->firstWhere('id', $cId)?->name ?? 'Unknown' }}{{ !$loop->last ? ',' : '' }}
                                                                @endforeach
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($semester->programme))
                                                            <span class="badge bg-success-transparent mb-1" title="Programmes">
                                                                <i class="ti ti-school border-end pe-1 me-1"></i>
                                                                {{ implode(', ', $semester->programme) }}
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($semester->students_ids))
                                                            <span class="badge bg-info-transparent mb-1" title="Specific Students">
                                                                <i class="ti ti-users border-end pe-1 me-1"></i> {{ count($semester->students_ids) }} Student(s)
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($semester->lecturar_ids))
                                                            <span class="badge bg-warning-transparent mb-1" title="Specific Staff">
                                                                <i class="ti ti-users border-end pe-1 me-1"></i> {{ count($semester->lecturar_ids) }} Staff
                                                            </span><br>
                                                        @endif

                                                        @if(empty($semester->stream) && empty($semester->campus_id) && empty($semester->programme) && empty($semester->students_ids) && empty($semester->lecturar_ids))
                                                            <span class="badge bg-light text-dark shadow-sm">Global Default</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($semester->status == '1')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($semester->status_upload_result == '1')
                                                            <span class="badge bg-success">Enabled</span>
                                                        @else
                                                            <span class="badge bg-secondary">Disabled</span>
                                                        @endif
                                                    </td>
                                                    @if(auth()->user()->hasPermission('manage_semesters'))
                                                        <td class="text-end">
                                                            <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                                data-bs-target="#editSemesterModal{{ $semester->id }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteSemesterModal{{ $semester->id }}">
                                                                <i class="ti ti-trash"></i> Delete
                                                            </a>
                                                        </td>
                                                    @endif
                                                </tr>

                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Modal -->
                <div class="modal fade" id="addSemesterModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="{{ route('ict.semesters.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Semester</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label">Semester Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="e.g., First Semester" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="code" placeholder="e.g., 1st" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="1">Active</option>
                                                <option value="0" selected>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Result Upload</label>
                                            <select class="form-select" name="status_upload_result">
                                                <option value="1">Enabled</option>
                                                <option value="0" selected>Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="text-muted small mb-3"><i class="ti ti-info-circle me-1"></i>
                                        Leave overrides blank to apply globally to all students.</p>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Limit to Stream</label>
                                            <select class="form-select select2-stream" name="stream[]" multiple="multiple">
                                                <option value="1">Stream 1</option>
                                                <option value="2">Stream 2</option>
                                                <option value="3">Stream 3</option>
                                                <option value="4">Stream 4</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Limit to Campus</label>
                                            <select class="form-select select2-campus" name="campus_id[]" multiple="multiple">
                                                @foreach($campuses as $campus)
                                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Limit to Programme</label>
                                            <select class="form-select select2-programme" name="programme[]" multiple="multiple">
                                                @foreach($entryModes->pluck('student_type')->unique() as $type)
                                                    <option value="{{ $type }}">{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Specific Students</label>
                                            <select class="form-control select2-students" name="students_ids[]"
                                                multiple="multiple">
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Specific Lecturers</label>
                                            <select class="form-control select2-lecturers" name="lecturar_ids[]"
                                                multiple="multiple">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add Semester</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @foreach ($semesters as $semester)
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSemesterModal{{ $semester->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('ict.semesters.update', $semester->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Semester — {{ $semester->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-5">
                                                <label class="form-label">Semester Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" value="{{ $semester->name }}"
                                                    placeholder="e.g., First Semester" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="code" value="{{ $semester->code }}"
                                                    placeholder="e.g., 1st" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="1" {{ $semester->status == '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $semester->status == '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Result Upload</label>
                                                <select class="form-select" name="status_upload_result">
                                                    <option value="1" {{ $semester->status_upload_result == '1' ? 'selected' : '' }}>Enabled</option>
                                                    <option value="0" {{ $semester->status_upload_result == '0' ? 'selected' : '' }}>Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="text-muted small mb-3"><i class="ti ti-info-circle me-1"></i>
                                            Leave overrides blank to apply globally to all students.</p>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Limit to Stream</label>
                                                <select class="form-select select2-stream" name="stream[]" multiple="multiple">
                                                    @foreach(['1','2','3','4'] as $num)
                                                        <option value="{{ $num }}" {{ is_array($semester->stream) && in_array($num, $semester->stream) ? 'selected' : '' }}>
                                                            Stream {{ $num }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Limit to Campus</label>
                                                <select class="form-select select2-campus" name="campus_id[]" multiple="multiple">
                                                    @foreach($campuses as $campus)
                                                        <option value="{{ $campus->id }}" {{ is_array($semester->campus_id) && in_array($campus->id, $semester->campus_id) ? 'selected' : '' }}>
                                                            {{ $campus->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Limit to Programme</label>
                                                <select class="form-select select2-programme" name="programme[]" multiple="multiple">
                                                    @foreach($entryModes->pluck('student_type')->unique() as $type)
                                                        <option value="{{ $type }}" {{ is_array($semester->programme) && in_array($type, $semester->programme) ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Specific Students</label>
                                                <select class="form-control select2-students" name="students_ids[]"
                                                    multiple="multiple">
                                                    @if(is_array($semester->students_ids))
                                                        @foreach($semester->students_ids as $sid)
                                                            @php $usr = \App\Models\User::find($sid); @endphp
                                                            @if($usr)
                                                                <option value="{{ $usr->id }}" selected>{{ $usr->first_name }}
                                                                    {{ $usr->last_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Specific Lecturers</label>
                                                <select class="form-control select2-lecturers" name="lecturar_ids[]"
                                                    multiple="multiple">
                                                    @if(is_array($semester->lecturar_ids))
                                                        @foreach($semester->lecturar_ids as $lid)
                                                            @php $usr = \App\Models\User::find($lid); @endphp
                                                            @if($usr)
                                                                <option value="{{ $usr->id }}" selected>{{ $usr->first_name }}
                                                                    {{ $usr->last_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteSemesterModal{{ $semester->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('ict.semesters.destroy', $semester->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the academic semester
                                            <strong>{{ $semester->name }}</strong>?
                                        </p>
                                        <p class="text-warning small"><i class="ti ti-alert-triangle"></i> Deleting a
                                            semester may cause systemic issues if students or
                                            payment configurations are still relying on it. Ensure
                                            it's safe to delete.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2-students').select2({
                placeholder: "Search for specific students by matric/name",
                allowClear: true,
                dropdownParent: $('.modal'),
                ajax: {
                    url: "{{ route('ict.search.students') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) { return { results: data.results }; },
                    cache: true
                }
            });

            $('.select2-lecturers').select2({
                placeholder: "Search for specific lecturers by name/staff no",
                allowClear: true,
                dropdownParent: $('.modal'),
                ajax: {
                    url: "{{ route('ict.search.lecturers') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) { return { results: data.results }; },
                    cache: true
                }
            });

            // Re-initialize selects inside each modal when opened
            $('.modal').on('shown.bs.modal', function () {
                var $modal = $(this);

                // Destroy before re-init so pre-selected Blade values are respected
                var selects = ['.select2-stream', '.select2-campus', '.select2-programme', '.select2-students', '.select2-lecturers'];
                selects.forEach(function (cls) {
                    $modal.find(cls).each(function () {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });
                });

                $modal.find('.select2-stream').select2({
                    placeholder: "-- All Streams --",
                    allowClear: true,
                    dropdownParent: $modal
                });

                $modal.find('.select2-campus').select2({
                    placeholder: "-- All Campuses --",
                    allowClear: true,
                    dropdownParent: $modal
                });

                $modal.find('.select2-programme').select2({
                    placeholder: "-- All Programmes --",
                    allowClear: true,
                    dropdownParent: $modal
                });

                $modal.find('.select2-students').select2({
                    placeholder: "Search for specific students by matric/name",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('ict.search.students') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) { return { results: data.results }; }
                    },
                    dropdownParent: $modal
                });

                $modal.find('.select2-lecturers').select2({
                    placeholder: "Search for specific lecturers by name/staff no",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('ict.search.lecturers') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) { return { results: data.results }; }
                    },
                    dropdownParent: $modal
                });
            });
        });
    </script>
@endpush
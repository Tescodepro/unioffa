@extends('layouts.app')

@section('title', 'Manage Academic Sessions')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Academic Setup - Sessions</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('ict.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Sessions</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                        @if(auth()->user()->hasPermission('manage_sessions'))
                            <div class="mb-2">
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                                    <i class="ti ti-plus me-1"></i>Add Session
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped custom-table datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Session Name</th>
                                                <th>Overrides</th>
                                                <th>Session Status</th>
                                                <th>Result Upload Status</th>
                                                @if(auth()->user()->hasPermission('manage_sessions'))
                                                    <th class="text-end">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sessions as $key => $session)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td class="fw-bold">{{ $session->name }}</td>
                                                    <td>
                                                        @if(!empty($session->stream))
                                                            <span class="badge bg-primary-transparent mb-1" title="Streams">
                                                                <i class="ti ti-activity border-end pe-1 me-1"></i>
                                                                Stream {{ implode(', ', $session->stream) }}
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($session->campus_id))
                                                            <span class="badge bg-secondary-transparent mb-1" title="Campuses">
                                                                <i class="ti ti-map-pin border-end pe-1 me-1"></i>
                                                                @foreach($session->campus_id as $cId)
                                                                    {{ $campuses->firstWhere('id', $cId)?->name ?? 'Unknown' }}{{ !$loop->last ? ',' : '' }}
                                                                @endforeach
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($session->programme))
                                                            <span class="badge bg-success-transparent mb-1" title="Programmes">
                                                                <i class="ti ti-school border-end pe-1 me-1"></i>
                                                                {{ implode(', ', $session->programme) }}
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($session->students_ids))
                                                            <span class="badge bg-info-transparent mb-1" title="Specific Students">
                                                                <i class="ti ti-users border-end pe-1 me-1"></i> {{ count($session->students_ids) }} Student(s)
                                                            </span><br>
                                                        @endif

                                                        @if(!empty($session->lecturar_ids))
                                                            <span class="badge bg-warning-transparent mb-1" title="Specific Staff">
                                                                <i class="ti ti-users border-end pe-1 me-1"></i> {{ count($session->lecturar_ids) }} Staff
                                                            </span><br>
                                                        @endif

                                                        @if(empty($session->stream) && empty($session->campus_id) && empty($session->programme) && empty($session->students_ids) && empty($session->lecturar_ids))
                                                            <span class="badge bg-light text-dark shadow-sm">Global Default</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($session->status == '1')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($session->status_upload_result == '1')
                                                            <span class="badge bg-success">Enabled</span>
                                                        @else
                                                            <span class="badge bg-secondary">Disabled</span>
                                                        @endif
                                                    </td>
                                                    @if(auth()->user()->hasPermission('manage_sessions'))
                                                        <td class="text-end">
                                                            <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                                data-bs-target="#editSessionModal{{ $session->id }}">
                                                                <i class="ti ti-edit"></i> Edit
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                                data-bs-target="#deleteSessionModal{{ $session->id }}">
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
                <div class="modal fade" id="addSessionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="{{ route('ict.sessions.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add New Session</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Session Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" placeholder="e.g., 2024/2025"
                                                required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Session Status</label>
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
                                    <button type="submit" class="btn btn-primary">Add Session</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @foreach ($sessions as $session)
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editSessionModal{{ $session->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('ict.sessions.update', $session->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Session — {{ $session->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Session Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" value="{{ $session->name }}"
                                                    placeholder="2023/2024" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Session Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="1" {{ $session->status == '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $session->status == '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Result Upload</label>
                                                <select class="form-select" name="status_upload_result">
                                                    <option value="1" {{ $session->status_upload_result == '1' ? 'selected' : '' }}>Enabled</option>
                                                    <option value="0" {{ $session->status_upload_result == '0' ? 'selected' : '' }}>Disabled</option>
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
                                                        <option value="{{ $num }}" {{ is_array($session->stream) && in_array($num, $session->stream) ? 'selected' : '' }}>
                                                            Stream {{ $num }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Limit to Campus</label>
                                                <select class="form-select select2-campus" name="campus_id[]" multiple="multiple">
                                                    @foreach($campuses as $campus)
                                                        <option value="{{ $campus->id }}" {{ is_array($session->campus_id) && in_array($campus->id, $session->campus_id) ? 'selected' : '' }}>
                                                            {{ $campus->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Limit to Programme</label>
                                                <select class="form-select select2-programme" name="programme[]" multiple="multiple">
                                                    @foreach($entryModes->pluck('student_type')->unique() as $type)
                                                        <option value="{{ $type }}" {{ is_array($session->programme) && in_array($type, $session->programme) ? 'selected' : '' }}>
                                                            {{ $type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Specific Students</label>
                                                <select class="form-control select2-students" name="students_ids[]"
                                                    multiple="multiple">
                                                    @if(is_array($session->students_ids))
                                                        @foreach($session->students_ids as $sid)
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
                                                    @if(is_array($session->lecturar_ids))
                                                        @foreach($session->lecturar_ids as $lid)
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
                    <div class="modal fade" id="deleteSessionModal{{ $session->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('ict.sessions.destroy', $session->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete the academic session
                                            <strong>{{ $session->name }}</strong>?
                                        </p>
                                        <p class="text-warning small"><i class="ti ti-alert-triangle"></i> Deleting a session
                                            may cause systemic issues if students or payment
                                            configurations are still relying on it. Ensure it's safe
                                            to delete.</p>
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
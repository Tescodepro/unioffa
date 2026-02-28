@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Payment Settings</h4>
                    <a href="{{ route('bursary.payment-settings.create') }}" class="btn btn-primary">Add New</a>
                </div>

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- FILTERS --}}
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h6 class="card-title mb-0"><i class="ti ti-filter text-primary me-2"></i>Filter Settings</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('bursary.payment-settings.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Session</label>
                                    <select name="session" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}"
                                                {{ request('session') == $session ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Semester</label>
                                    <select name="semester" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        <option value="3rd" {{ request('semester') == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Payment Type</label>
                                    <select name="payment_type" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        @foreach ($paymentTypes as $type)
                                           @if ($type != 'technical')
                                                <option value="{{ $type }}"
                                                    {{ request('payment_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                           @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Faculty</label>
                                    <select name="faculty_id" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}"
                                                {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->faculty_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Department</label>
                                    <select name="department_id" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->department_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Installment</label>
                                    <select name="installmental_allow_status" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="1"
                                            {{ request('installmental_allow_status') == '1' ? 'selected' : '' }}>Allowed</option>
                                        <option value="0"
                                            {{ request('installmental_allow_status') == '0' ? 'selected' : '' }}>Not Allowed
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label text-muted small fw-semibold">Matric Number</label>
                                    <input type="text" name="matric_number" class="form-control form-control-sm"
                                        value="{{ request('matric_number') }}" placeholder="Enter matric number">
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="ti ti-search me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0" id="payment-settings-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap fw-semibold text-muted">#</th>
                                    <th class="text-nowrap fw-semibold text-muted">Faculty</th>
                                    <th class="text-nowrap fw-semibold text-muted">Department</th>
                                    <th class="text-nowrap fw-semibold text-muted">Level</th>
                                    <th class="text-nowrap fw-semibold text-muted">Matric Number</th>
                                    <th class="text-nowrap fw-semibold text-muted">Payment Type</th>
                                    <th class="text-nowrap fw-semibold text-muted">Amount (₦)</th>
                                    <th class="text-nowrap fw-semibold text-muted">Session</th>
                                    <th class="text-nowrap fw-semibold text-muted">Semester</th>
                                    <th class="text-nowrap fw-semibold text-muted">Student Type</th>
                                    <th class="text-nowrap fw-semibold text-muted">Entry Mode</th>
                                    <th class="text-nowrap fw-semibold text-muted">Installment</th>
                                    <th class="text-nowrap fw-semibold text-muted">Installment Details</th>
                                    <th class="text-nowrap fw-semibold text-muted text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($settings as $key => $setting)
                                    @if ($setting->payment_type != 'technical')
                                        <tr>
                                            <td class="text-muted">{{ $key + 1 }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $setting->faculty->faculty_code ?? 'All' }}</span></td>
                                            <td><span class="badge bg-light text-dark border">{{ $setting->department->department_code ?? 'All' }}</span></td>
                                            <td><span class="badge bg-light text-dark border">{{ $setting->level ? implode(', ', $setting->level) : 'All' }}</span></td>
                                            <td>{{ $setting->matric_number ?? 'All' }}</td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($setting->payment_type) }}</span></td>
                                            <td class="fw-semibold">₦{{ number_format($setting->amount, 2) }}</td>
                                            <td>{{ $setting->session }}</td>
                                            <td>
                                                @if($setting->semester)
                                                    <span class="badge bg-info bg-opacity-10 text-info">{{ $setting->semester }}</span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">All Semesters</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ is_array($setting->student_type) ? (count($setting->student_type) ? implode(', ', $setting->student_type) : 'All') : ($setting->student_type ?? 'All') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ is_array($setting->entry_mode) ? (count($setting->entry_mode) ? implode(', ', $setting->entry_mode) : 'All') : ($setting->entry_mode ?? 'All') }}</span>
                                            </td>
                                            <td>
                                                @if ($setting->installmental_allow_status)
                                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="ti ti-check me-1"></i>Allowed</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="ti ti-x me-1"></i>Not Allowed</span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">
                                                @if ($setting->installmental_allow_status && $setting->list_instalment_percentage)
                                                    {{ implode('%, ', json_decode($setting->list_instalment_percentage)) . '%' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('bursary.payment-settings.edit', $setting->id) }}"
                                                    class="btn btn-icon btn-sm btn-light border" title="Edit">
                                                    <i class="ti ti-edit text-warning"></i>
                                                </a>
                                                <form
                                                    action="{{ route('bursary.payment-settings.destroy', $setting->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Are you sure you want to delete this payment setting?')"
                                                        class="btn btn-icon btn-sm btn-light border" title="Delete">
                                                        <i class="ti ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center">No settings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($settings->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                {{ $settings->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables & Buttons -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#payment-settings-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                paging: false, // Keep false to allow Laravel pagination
                searching: false, // Search is handled by the filter form above
                info: false,
                ordering: true,
            });
        });
    </script>
@endpush

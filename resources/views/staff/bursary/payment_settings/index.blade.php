@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white rounded-4 shadow-sm border-0">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                            <i class="ti ti-settings-automation me-2 fs-3 text-primary"></i>
                            Payment Settings
                        </h4>
                        <p class="text-muted small mb-0">Manage university fee structures and installment policies</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-download me-2"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('bursary.payment-settings.export', request()->all()) }}">
                                        <i class="ti ti-file-spreadsheet me-2 text-success fs-5"></i> Export to Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('bursary.payment-settings.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                        <i class="ti ti-file-text me-2 text-info fs-5"></i> Export to CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('bursary.payment-settings.create') }}" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                            <i class="ti ti-plus me-2"></i> Create New Setting
                        </a>
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-light border shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('bursary.payment-settings.export', request()->query()) }}">
                                        <i class="ti ti-file-spreadsheet me-2 text-success"></i> Export as Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('bursary.payment-settings.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                                        <i class="ti ti-file-text me-2 text-info"></i> Export as CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-circle-check fs-4 me-2"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Enhanced Filters --}}
                <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-light border-0 py-3">
                        <h6 class="mb-0 fw-bold small text-uppercase tracking-wider text-muted">
                            <i class="ti ti-filter-check me-1"></i> Filter Records
                        </h6>
                    </div>
                    <div class="card-body bg-white py-4">
                        <form method="GET" action="{{ route('bursary.payment-settings.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Academic Session</label>
                                    <select name="session" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">All Sessions</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Fee Category</label>
                                    <select name="payment_type" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">All Types</option>
                                        @foreach ($paymentTypes as $type)
                                            @if ($type != 'technical')
                                                <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Faculty Focus</label>
                                    <select name="faculty_id" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">All Faculties</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->faculty_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Programme Focus</label>
                                    <select name="student_type" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">All Programmes</option>
                                        @foreach ($programmes as $programme)
                                            <option value="{{ $programme }}" {{ request('student_type') == $programme ? 'selected' : '' }}>
                                                {{ $programme }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Entry Mode</label>
                                    <select name="entry_mode" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">All Modes</option>
                                        @foreach ($entryModes as $mode)
                                            <option value="{{ $mode->code }}" {{ request('entry_mode') == $mode->code ? 'selected' : '' }}>
                                                {{ $mode->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-dark fw-semibold small mb-2">Installment Policy</label>
                                    <select name="installmental_allow_status" class="form-select border-light-subtle shadow-none rounded-3">
                                        <option value="">Any Policy</option>
                                        <option value="1" {{ request('installmental_allow_status') == '1' ? 'selected' : '' }}>Allowed</option>
                                        <option value="0" {{ request('installmental_allow_status') == '0' ? 'selected' : '' }}>One-off Only</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark w-100 rounded-3 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-adjustments-horizontal me-2"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-light border rounded-3 px-3" title="Reset">
                                        <i class="ti ti-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Premium Table Card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-4 text-muted fw-bold small text-uppercase py-3">Fee Specification</th>
                                    <th class="text-muted fw-bold small text-uppercase py-3">Standard Amount</th>
                                    <th class="text-muted fw-bold small text-uppercase py-3">Academic Period</th>
                                    <th class="text-muted fw-bold small text-uppercase py-3">Target Audience</th>
                                    <th class="text-muted fw-bold small text-uppercase py-3">Billing Rules</th>
                                    <th class="pe-4 text-muted fw-bold small text-uppercase py-3 text-end">Management</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $key => $setting)
                                    @if ($setting->payment_type != 'technical')
                                        <tr class="transition-all hover:bg-slate-50">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                                        <i class="ti ti-cash fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ ucfirst($setting->payment_type) }}</div>
                                                        @if ($setting->description)
                                                            <div class="text-muted x-small opacity-75 mt-0.5">{{ Str::limit($setting->description, 35) }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-3">
                                                <span class="text-muted small fw-normal">₦</span>{{ number_format($setting->amount, 2) }}
                                            </td>

                                            <td class="py-3">
                                                <div class="badge bg-light text-dark border-0 rounded-2 px-2 py-1.5 d-inline-flex align-items-center">
                                                    <i class="ti ti-calendar-event me-1 text-primary"></i>
                                                    {{ $setting->session }}
                                                </div>
                                                @if (is_array($setting->semesters) && count($setting->semesters))
                                                    <div class="mt-1 d-flex flex-wrap gap-1">
                                                        @foreach($setting->semesters as $sem)
                                                            <span class="badge bg-blue-soft text-blue rounded-pill small px-2">{{ $sem }} Semester</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="py-3">
                                                <div class="d-flex flex-wrap gap-1 max-width-250">
                                                    @php $targets = 0; @endphp
                                                    @if (is_array($setting->faculty_ids) && count($setting->faculty_ids))
                                                        @foreach($setting->faculties() as $fac)
                                                            <span class="badge bg-blue-soft text-blue rounded-pill px-2 border-0" title="{{ $fac->faculty_name }}">{{ $fac->faculty_code }}</span>
                                                            @php $targets++; @endphp
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->department_ids) && count($setting->department_ids))
                                                        @foreach($setting->departments() as $dept)
                                                            <span class="badge bg-soft-success rounded-pill px-2 border-0" title="{{ $dept->department_name }}">{{ $dept->department_code }}</span>
                                                            @php $targets++; @endphp
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->level) && count($setting->level))
                                                        @foreach ($setting->level as $lvl)
                                                            <span class="badge bg-soft-warning rounded-pill px-2 border-0">L{{ $lvl }}</span>
                                                            @php $targets++; @endphp
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->student_type) && count($setting->student_type))
                                                        @foreach ($setting->student_type as $prog)
                                                            <span class="badge bg-soft-primary rounded-pill px-2 border-0">{{ $prog }}</span>
                                                            @php $targets++; @endphp
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->entry_mode) && count($setting->entry_mode))
                                                        @foreach ($setting->entry_mode as $mode)
                                                            <span class="badge bg-soft-info rounded-pill px-2 border-0">{{ $mode }}</span>
                                                            @php $targets++; @endphp
                                                        @endforeach
                                                    @endif
                                                    @if ($targets == 0)
                                                        <span class="text-muted small italic">Universal (All Students)</span>
                                                    @elseif($targets > 8)
                                                         <span class="badge bg-light text-muted border-0">+ more filters</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="py-3">
                                                @if ($setting->installmental_allow_status)
                                                    <div class="d-flex flex-column">
                                                        <span class="text-success fw-bold small d-flex align-items-center">
                                                            <i class="ti ti-circle-check-filled me-1"></i> Installments ({{ $setting->number_of_instalment }})
                                                        </span>
                                                        @if ($setting->list_instalment_percentage)
                                                            <div class="progress mt-1.5 h-[4px] w-[80px] rounded-full">
                                                                @foreach((array) $setting->list_instalment_percentage as $p)
                                                                    <div class="progress-bar {{ $loop->first ? 'bg-success' : ($loop->last ? 'bg-info' : 'bg-primary') }}" style="width: {{ $p }}%"></div>
                                                                @endforeach
                                                            </div>
                                                            <span class="text-muted x-small mt-1">{{ implode(' / ', (array) $setting->list_instalment_percentage) }}%</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small d-flex align-items-center">
                                                        <i class="ti ti-lock me-1"></i> Full Payment
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="pe-4 py-3 text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('bursary.payment-settings.edit', $setting->id) }}"
                                                        class="btn btn-icon btn-light border-0 hover:bg-amber-50 rounded-circle me-1" title="Edit Configuration">
                                                        <i class="ti ti-edit-circle text-amber-600 fs-4"></i>
                                                    </a>
                                                    <form action="{{ route('bursary.payment-settings.destroy', $setting->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button onclick="return confirm('Archive this payment setting? This action cannot be undone.')"
                                                            class="btn btn-icon btn-light border-0 hover:bg-rose-50 rounded-circle" title="Delete Setting">
                                                            <i class="ti ti-trash-x text-rose-600 fs-4"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="ti ti-database-off fs-1 text-muted opacity-25"></i>
                                                <h5 class="mt-3 text-dark fw-bold">No Records Found</h5>
                                                <p class="text-muted px-5">Your current filter criteria did not match any payment settings. Try adjusting your filters or creating a new configuration.</p>
                                                <a href="{{ route('bursary.payment-settings.create') }}" class="btn btn-primary px-4 mt-2">
                                                    <i class="ti ti-plus me-1"></i> Add First Setting
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($settings->hasPages())
                        <div class="card-footer bg-white border-top-0 py-3">
                            <div class="d-flex justify-content-between align-items-center px-2">
                                <div class="text-muted small">
                                    Showing {{ $settings->firstItem() }} to {{ $settings->lastItem() }} of {{ $settings->total() }} entries
                                </div>
                                <div>
                                    {{ $settings->withQueryString()->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <style>
        .x-small { font-size: 0.7rem; }
        .bg-blue-soft { background-color: #eef2ff; }
        .text-blue { color: #4338ca; }
        .bg-indigo-soft { background-color: #f5f3ff; }
        .text-indigo { color: #5b21b6; }
        .bg-teal-soft { background-color: #f0fdf4; }
        .text-teal { color: #166534; }
        .bg-amber-soft { background-color: #fffbeb; }
        .text-amber { color: #92400e; }
        .fs-black { font-weight: 900; }
        .hover\:bg-slate-50:hover { background-color: #f8fafc; }
        .hover\:bg-amber-50:hover { background-color: #fffbeb; }
        .hover\:bg-rose-50:hover { background-color: #fff1f2; }
        .transition-all { transition: all 0.2s ease-in-out; }
    </style>
@endsection

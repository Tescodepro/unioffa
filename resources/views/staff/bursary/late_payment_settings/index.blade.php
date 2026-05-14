@extends('layouts.app')

@section('title', 'Late Payment Penalties')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                {{-- Header Section --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Late Payment Penalties</h4>
                        <p class="text-muted small mb-0">Configure deadlines and late fees for specific payment types</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-download me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('bursary.late-payment-settings.export', request()->query()) }}">
                                        <i class="ti ti-file-spreadsheet me-2 text-success"></i> Export as Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('bursary.late-payment-settings.export', array_merge(request()->query(), ['format' => 'csv'])) }}">
                                        <i class="ti ti-file-text me-2 text-info"></i> Export as CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('bursary.late-payment-settings.create') }}" class="btn btn-primary px-3">
                            <i class="ti ti-plus me-1"></i> Add New Penalty
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white p-2 rounded-2 me-3">
                                <i class="ti ti-circle-check fs-5"></i>
                            </div>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Premium Filter Panel --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('bursary.late-payment-settings.index') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Academic Session</label>
                                    <select name="session" class="form-select border-0 bg-light">
                                        <option value="">All Sessions</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Semester</label>
                                    <select name="semester" class="form-select border-0 bg-light">
                                        <option value="">All</option>
                                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Fee Type</label>
                                    <select name="payment_type" class="form-select border-0 bg-light">
                                        <option value="">All Payment Types</option>
                                        @foreach ($paymentTypes as $type)
                                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Campus</label>
                                    <select name="campus_id" class="form-select border-0 bg-light">
                                        <option value="">All Locations</option>
                                        @foreach ($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Programme</label>
                                    <select name="student_type" class="form-select border-0 bg-light">
                                        <option value="">All</option>
                                        @foreach ($programmes as $prog)
                                            <option value="{{ $prog }}" {{ request('student_type') == $prog ? 'selected' : '' }}>
                                                {{ $prog }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Level</label>
                                    <select name="level" class="form-select border-0 bg-light">
                                        <option value="">All</option>
                                        @foreach ($levels as $lvl)
                                            <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>
                                                {{ $lvl }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Entry Mode</label>
                                    <select name="entry_mode" class="form-select border-0 bg-light">
                                        <option value="">All</option>
                                        @foreach ($entryModes as $mode)
                                            <option value="{{ $mode->code }}" {{ request('entry_mode') == $mode->code ? 'selected' : '' }}>
                                                {{ $mode->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-dark w-100 py-2">
                                        <i class="ti ti-adjustments me-1"></i> Apply
                                    </button>
                                    <a href="{{ route('bursary.late-payment-settings.index') }}" class="btn btn-light border py-2 px-3" title="Clear Filters">
                                        <i class="ti ti-refresh"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Data Card Table --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light border-0">
                                <tr>
                                    <th class="py-3 ps-4 text-muted small text-uppercase fw-bold">Penalty Target</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Financial Rule</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Deadline Control</th>
                                    <th class="py-3 text-muted small text-uppercase fw-bold">Scope</th>
                                    <th class="py-3 text-end pe-4 text-muted small text-uppercase fw-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                    <tr>
                                        {{-- Penalty Target --}}
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                                    <i class="ti ti-alert-triangle fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ ucfirst($setting->payment_type) }}</div>
                                                    <div class="text-muted extra-small">{{ $setting->session }} — {{ $setting->semester ?? 'Full Session' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Financial Rule --}}
                                        <td>
                                            <div class="fw-bold text-danger fs-5">₦{{ number_format($setting->late_fee_amount, 2) }}</div>
                                            @if($setting->increment_amount > 0)
                                                <div class="text-muted extra-small">
                                                    <i class="ti ti-arrow-up-right"></i> Increases by ₦{{ number_format($setting->increment_amount) }} 
                                                    on {{ $setting->increment_date ? $setting->increment_date->format('d M') : 'N/A' }}
                                                </div>
                                            @else
                                                <div class="text-muted extra-small">Fixed Penalty</div>
                                            @endif
                                        </td>

                                        {{-- Deadline Control --}}
                                        <td>
                                            @if($setting->closing_date->isPast())
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 border border-danger-subtle rounded-pill">
                                                    <i class="ti ti-clock-x me-1"></i> Expired {{ $setting->closing_date->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success px-2 py-1 border border-success-subtle rounded-pill">
                                                    <i class="ti ti-clock-check me-1"></i> Closes {{ $setting->closing_date->format('d M, Y') }}
                                                </span>
                                            @endif
                                            <div class="text-muted extra-small mt-1 ps-1">{{ $setting->closing_date->format('h:i A') }}</div>
                                        </td>

                                        {{-- Scope --}}
                                        <td>
                                            <div class="fw-semibold text-dark small">{{ $setting->campus->name ?? 'Global' }}</div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                {{-- Entry Modes --}}
                                                @if (is_array($setting->entry_mode) && count($setting->entry_mode))
                                                    @foreach (array_slice($setting->entry_mode, 0, 2) as $em)
                                                        <span class="badge bg-soft-info text-info border-0 rounded-pill fw-normal px-2">{{ $em }}</span>
                                                    @endforeach
                                                    @if(count($setting->entry_mode) > 2)
                                                        <span class="badge bg-light text-muted border-0 rounded-pill fw-normal px-2">+{{ count($setting->entry_mode) - 2 }}</span>
                                                    @endif
                                                @endif

                                                {{-- Programme Types (Student Type) --}}
                                                @if (is_array($setting->student_type) && count($setting->student_type))
                                                    @foreach (array_slice($setting->student_type, 0, 2) as $st)
                                                        <span class="badge bg-soft-primary text-primary border-0 rounded-pill fw-normal px-2">{{ $st }}</span>
                                                    @endforeach
                                                    @if(count($setting->student_type) > 2)
                                                        <span class="badge bg-light text-muted border-0 rounded-pill fw-normal px-2">+{{ count($setting->student_type) - 2 }}</span>
                                                    @endif
                                                @endif

                                                @if (!is_array($setting->entry_mode) && !is_array($setting->student_type))
                                                    <span class="text-muted extra-small italic">Universal Scope</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm rounded-3">
                                                <a href="{{ route('bursary.late-payment-settings.edit', $setting->id) }}"
                                                    class="btn btn-white btn-sm px-3 border-end" title="Edit Configuration">
                                                    <i class="ti ti-edit text-warning"></i>
                                                </a>
                                                <form action="{{ route('bursary.late-payment-settings.destroy', $setting->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Archive this penalty rule?')"
                                                        class="btn btn-white btn-sm px-3" title="Delete">
                                                        <i class="ti ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-4">
                                                <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                                                    <i class="ti ti-inbox fs-1 text-muted"></i>
                                                </div>
                                                <h5 class="fw-bold">No Penalty Rules Found</h5>
                                                <p class="text-muted mb-4">You haven't configured any late payment penalties for the selected filters.</p>
                                                <a href="{{ route('bursary.late-payment-settings.create') }}" class="btn btn-primary px-4">
                                                    <i class="ti ti-plus me-1"></i> Create First Penalty
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($settings->hasPages())
                        <div class="card-footer bg-white py-3 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Showing {{ $settings->firstItem() }} to {{ $settings->lastItem() }} of {{ $settings->total() }} penalty rules</div>
                                {{ $settings->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <style>
        .extra-small { font-size: 0.7rem; }
        .btn-white { background: #fff; color: #475569; }
        .btn-white:hover { background: #f8fafc; }
    </style>
@endsection

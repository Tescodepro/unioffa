@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Payment Settings</h4>
                        <p class="text-muted small mb-0">Configure fees per session, semester, and student group</p>
                    </div>
                    <a href="{{ route('bursary.payment-settings.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Setting
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- FILTERS --}}
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('bursary.payment-settings.index') }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label text-muted small mb-1">Session</label>
                                    <select name="session" class="form-select form-select-sm">
                                        <option value="">All Sessions</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                                {{ $session }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-muted small mb-1">Semester</label>
                                    <select name="semester" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st</option>
                                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd</option>
                                        <option value="3rd" {{ request('semester') == '3rd' ? 'selected' : '' }}>3rd</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-muted small mb-1">Payment Type</label>
                                    <select name="payment_type" class="form-select form-select-sm">
                                        <option value="">All</option>
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
                                    <label class="form-label text-muted small mb-1">Faculty</label>
                                    <select name="faculty_id" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        @foreach ($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->faculty_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label text-muted small mb-1">Installment</label>
                                    <select name="installmental_allow_status" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="1" {{ request('installmental_allow_status') == '1' ? 'selected' : '' }}>Allowed</option>
                                        <option value="0" {{ request('installmental_allow_status') == '0' ? 'selected' : '' }}>Not Allowed</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="ti ti-search me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-light btn-sm border">
                                        <i class="ti ti-x"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-muted fw-semibold text-nowrap">#</th>
                                    <th class="text-muted fw-semibold text-nowrap">Fee</th>
                                    <th class="text-muted fw-semibold text-nowrap">Amount</th>
                                    <th class="text-muted fw-semibold text-nowrap">Period</th>
                                    <th class="text-muted fw-semibold text-nowrap">Applies To</th>
                                    <th class="text-muted fw-semibold text-nowrap">Installment</th>
                                    <th class="text-muted fw-semibold text-nowrap text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $key => $setting)
                                    @if ($setting->payment_type != 'technical')
                                        <tr>
                                            <td class="text-muted small">{{ $settings->firstItem() + $key }}</td>

                                            {{-- Fee --}}
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                                    {{ ucfirst($setting->payment_type) }}
                                                </span>
                                                @if ($setting->description)
                                                    <div class="text-muted small mt-1">{{ Str::limit($setting->description, 40) }}</div>
                                                @endif
                                            </td>

                                            {{-- Amount --}}
                                            <td class="fw-bold text-nowrap">₦{{ number_format($setting->amount, 2) }}</td>

                                            {{-- Period --}}
                                            <td>
                                                <div class="fw-semibold small">{{ $setting->session }}</div>
                                                @if ($setting->semester)
                                                    <span class="badge bg-info bg-opacity-10 text-info small">{{ $setting->semester }} Sem</span>
                                                @else
                                                    <span class="text-muted small">All semesters</span>
                                                @endif
                                            </td>

                                            {{-- Applies To --}}
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @if ($setting->faculty)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $setting->faculty->faculty_code }}</span>
                                                    @endif
                                                    @if ($setting->department)
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $setting->department->department_code }}</span>
                                                    @endif
                                                    @if (is_array($setting->level) && count($setting->level))
                                                        @foreach ($setting->level as $lvl)
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border">L{{ $lvl }}</span>
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->student_type) && count($setting->student_type))
                                                        @foreach ($setting->student_type as $st)
                                                            <span class="badge bg-success bg-opacity-10 text-success border">{{ $st }}</span>
                                                        @endforeach
                                                    @endif
                                                    @if (is_array($setting->entry_mode) && count($setting->entry_mode))
                                                        @foreach ($setting->entry_mode as $em)
                                                            <span class="badge bg-light text-dark border">{{ $em }}</span>
                                                        @endforeach
                                                    @endif
                                                    @if ($setting->matric_number)
                                                        <span class="badge bg-dark bg-opacity-10 text-dark border">{{ $setting->matric_number }}</span>
                                                    @endif
                                                    @if (!$setting->faculty && !$setting->department && empty($setting->level) && empty($setting->student_type) && empty($setting->entry_mode) && !$setting->matric_number)
                                                        <span class="text-muted small fst-italic">All students</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Installment --}}
                                            <td>
                                                @if ($setting->installmental_allow_status)
                                                    <span class="badge bg-success bg-opacity-10 text-success">
                                                        <i class="ti ti-check me-1"></i>{{ $setting->number_of_instalment }} parts
                                                    </span>
                                                    @if ($setting->list_instalment_percentage)
                                                        <div class="text-muted small mt-1">
                                                            {{ implode('% · ', (array) $setting->list_instalment_percentage) }}%
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Full only</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('bursary.payment-settings.edit', $setting->id) }}"
                                                    class="btn btn-sm btn-light border me-1" title="Edit">
                                                    <i class="ti ti-edit text-warning"></i>
                                                </a>
                                                <form action="{{ route('bursary.payment-settings.destroy', $setting->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Delete this payment setting?')"
                                                        class="btn btn-sm btn-light border" title="Delete">
                                                        <i class="ti ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="ti ti-inbox fs-2 d-block mb-2"></i>
                                            No payment settings found.
                                            <a href="{{ route('bursary.payment-settings.create') }}">Add one now</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($settings->hasPages())
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-end">
                                {{ $settings->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection

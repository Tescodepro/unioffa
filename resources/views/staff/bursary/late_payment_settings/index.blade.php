@extends('layouts.app')

@section('title', 'Late Payment Settings')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Late Payment Penalties</h4>
                        <p class="text-muted small mb-0">Configure deadlines and late fees for specific payment types</p>
                    </div>
                    <a href="{{ route('bursary.late-payment-settings.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New Penalty
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
                        <form method="GET" action="{{ route('bursary.late-payment-settings.index') }}">
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    <label class="form-label text-muted small mb-1">Campus</label>
                                    <select name="campus_id" class="form-select form-select-sm">
                                        <option value="">All Campuses</option>
                                        @foreach ($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="ti ti-search me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('bursary.late-payment-settings.index') }}" class="btn btn-light btn-sm border">
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
                                    <th class="text-muted fw-semibold text-nowrap">Target Fee</th>
                                    <th class="text-muted fw-semibold text-nowrap">Penalty Amount</th>
                                    <th class="text-muted fw-semibold text-nowrap">Closing Date</th>
                                    <th class="text-muted fw-semibold text-nowrap">Period</th>
                                    <th class="text-muted fw-semibold text-nowrap">Campus</th>
                                    <th class="text-muted fw-semibold text-nowrap">Entry Mode</th>
                                    <th class="text-muted fw-semibold text-nowrap text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $key => $setting)
                                    <tr>
                                        {{-- Target Fee --}}
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                                {{ ucfirst($setting->payment_type) }}
                                            </span>
                                        </td>

                                        {{-- Penalty Amount --}}
                                        <td class="fw-bold text-nowrap text-danger">₦{{ number_format($setting->late_fee_amount, 2) }}</td>

                                        {{-- Closing Date --}}
                                        <td class="text-nowrap">
                                            @if($setting->closing_date->isPast())
                                                <span class="text-danger"><i class="ti ti-alert-circle"></i> {{ $setting->closing_date->format('d M Y, h:i A') }}</span>
                                            @else
                                                <span class="text-success">{{ $setting->closing_date->format('d M Y, h:i A') }}</span>
                                            @endif
                                        </td>

                                        {{-- Period --}}
                                        <td>
                                            <div class="fw-semibold small">{{ $setting->session ?? 'All Sessions' }}</div>
                                            @if ($setting->semester)
                                                <span class="badge bg-info bg-opacity-10 text-info small">{{ $setting->semester }} Sem</span>
                                            @else
                                                <span class="text-muted small">All Semesters</span>
                                            @endif
                                        </td>

                                        {{-- Campus --}}
                                        <td>
                                            <div class="fw-semibold small">{{ $setting->campus->name ?? 'N/A' }}</div>
                                        </td>

                                        {{-- Entry Mode --}}
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @if (is_array($setting->entry_mode) && count($setting->entry_mode))
                                                    @foreach ($setting->entry_mode as $em)
                                                        <span class="badge bg-light text-dark border">{{ $em }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small fst-italic">All Entry Modes</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('bursary.late-payment-settings.edit', $setting->id) }}"
                                                class="btn btn-sm btn-light border me-1" title="Edit">
                                                <i class="ti ti-edit text-warning"></i>
                                            </a>
                                            <form action="{{ route('bursary.late-payment-settings.destroy', $setting->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Delete this late payment penalty?')"
                                                    class="btn btn-sm btn-light border" title="Delete">
                                                    <i class="ti ti-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="ti ti-inbox fs-2 d-block mb-2"></i>
                                            No late payment settings found.
                                            <a href="{{ route('bursary.late-payment-settings.create') }}">Add a penalty now</a>
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

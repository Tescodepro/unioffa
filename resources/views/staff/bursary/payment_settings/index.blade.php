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
                <form method="GET" action="{{ route('bursary.payment-settings.index') }}" class="card p-3 shadow-sm mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" class="form-select">
                                <option value="">All</option>
                                @foreach ($paymentTypes as $type)
                                   @if ($type != 'technial')
                                        <option value="{{ $type }}"
                                            {{ request('payment_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                   @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Session</label>
                            <select name="session" class="form-select">
                                <option value="">All</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session }}"
                                        {{ request('session') == $session ? 'selected' : '' }}>
                                        {{ $session }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Faculty</label>
                            <select name="faculty_id" class="form-select">
                                <option value="">All</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}"
                                        {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->faculty_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Matric Number</label>
                            <input type="text" name="matric_number" class="form-control"
                                value="{{ request('matric_number') }}" placeholder="Enter matric number">
                        </div>


                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
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
                            <label class="form-label">Installment</label>
                            <select name="installmental_allow_status" class="form-select">
                                <option value="">All</option>
                                <option value="1"
                                    {{ request('installmental_allow_status') == '1' ? 'selected' : '' }}>Allowed</option>
                                <option value="0"
                                    {{ request('installmental_allow_status') == '0' ? 'selected' : '' }}>Not Allowed
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100">Filter</button>
                        </div>
                    </div>
                </form>

                {{-- TABLE --}}
                <div class="card p-3 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Faculty</th>
                                    <th>Department</th>
                                    <th>Level</th>
                                    <th>Matric Number</th>
                                    <th>Payment Type</th>
                                    <th>Amount (₦)</th>
                                    <th>Session</th>
                                    <th>Student Type</th>
                                    <th>Installment</th>
                                    <th>Installment Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $key => $setting)
                                    @if ($setting->payment_type != 'technical')
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $setting->faculty->faculty_code ?? 'All' }}</td>
                                            <td>{{ $setting->department->department_code ?? 'All' }}</td>
                                            <td>{{ implode(', ', $setting->level ?? []) }}</td>
                                            <td>{{ $setting->matric_number ?? 'All' }}</td>
                                            <td>{{ ucfirst($setting->payment_type) }}</td>
                                            <td>{{ number_format($setting->amount, 2) }}</td>
                                            <td>{{ $setting->session }}</td>
                                            <td>{{ $setting->student_type ?? 'All' }}</td>
                                            <td>
                                                @if ($setting->installmental_allow_status)
                                                    <span class="badge bg-success">Allowed</span>
                                                @else
                                                    <span class="badge bg-danger">Not Allowed</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($setting->installmental_allow_status && $setting->list_instalment_percentage)
                                                    {{ implode('%, ', json_decode($setting->list_instalment_percentage)) . '%' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('bursary.payment-settings.edit', $setting->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form
                                                    action="{{ route('bursary.payment-settings.destroy', $setting->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Are you sure?')"
                                                        class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No settings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($settings->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                {{ $settings->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

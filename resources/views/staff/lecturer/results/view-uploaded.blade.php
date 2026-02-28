@extends('layouts.app')
@section('title', 'View Uploaded Results')
@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">View Uploaded Results</h3>
                        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('lecturer.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">View Uploaded Results</li>
                        </ol></nav>
                    </div>
                </div>

                {{-- Filter Card --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-0 bg-light">
                        <h5 class="card-title mb-0"><i class="ti ti-filter me-2 text-primary"></i>Filter Results</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('results.viewUploaded') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Course</label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $c)
                                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->course_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Session</label>
                                    <select name="session" class="form-select" required>
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $s)
                                            <option value="{{ $s->name }}" {{ request('session') == $s->name ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Semester</label>
                                    <select name="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        @foreach ($semesters as $s)
                                            <option value="{{ $s->name }}" {{ request('semester') == $s->name ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100">
                                        <i class="ti ti-search me-1"></i> View
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Results Table --}}
                @if ($results->isNotEmpty())
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                {{ $course->course_code }} &mdash; Uploaded Results
                                <span class="badge bg-primary ms-2">{{ $results->count() }}</span>
                            </h5>
                            <form action="{{ route('results.download') }}" method="GET" class="d-inline">
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="session" value="{{ request('session') }}">
                                <input type="hidden" name="semester" value="{{ request('semester') }}">
                                <button class="btn btn-sm btn-success">
                                    <i class="ti ti-download me-1"></i> Download Excel
                                </button>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Matric No</th>
                                            <th class="text-center">CA</th>
                                            <th class="text-center">Exam</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Grade</th>
                                            <th class="text-center">Remark</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $i => $r)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td><code>{{ $r->matric_no }}</code></td>
                                                <td class="text-center">{{ $r->ca }}</td>
                                                <td class="text-center">{{ $r->exam }}</td>
                                                <td class="text-center fw-semibold">{{ $r->total }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ in_array($r->grade, ['A','B']) ? 'success' : (in_array($r->grade, ['C','D']) ? 'warning' : 'danger') }}">
                                                        {{ $r->grade }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $r->remark }}</td>
                                                <td class="text-center">
                                                    @php $s = strtolower($r->status); @endphp
                                                    <span class="badge bg-{{ $s === 'approved' ? 'success' : ($s === 'published' ? 'secondary' : ($s === 'recommended' ? 'info' : 'warning text-dark')) }}">
                                                        {{ ucfirst($r->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                @elseif(request()->has('course_id'))
                    <div class="alert alert-warning"><i class="ti ti-alert-triangle me-1"></i> No results found for your selection.</div>
                @endif

            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'View Uploaded Results')
@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">
                <h4>View Uploaded Results</h4>
                <p class="text-muted">Select course, session and semester</p>

                <form action="{{ route('results.viewUploaded') }}" method="GET">

                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $c)
                                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->course_title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Session</label>
                        <select name="session" class="form-control" required>
                            <option value="">Select Session</option>
                            @foreach ($sessions as $s)
                                <option value="{{ $s->name }}" {{ request('session') == $s->name ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-control" required>
                            <option value="">Select Semester</option>
                            @foreach ($semesters as $s)
                                <option value="{{ $s->name }}" {{ request('semester') == $s->name ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-primary">View Result</button>

                </form>

                {{-- ✅ ONLY SHOW TABLE AFTER SEARCH --}}
                @if ($results->isNotEmpty())

                    <hr class="mt-4">

                    <h4>{{ $course->course_code }} - Uploaded Results</h4>
                    <p class="text-muted">Showing only results you uploaded</p>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Matric No</th>
                                <th>CA</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $r)
                                <tr>
                                    <td>{{ $r->matric_no }}</td>
                                    <td>{{ $r->ca }}</td>
                                    <td>{{ $r->exam }}</td>
                                    <td>{{ $r->total }}</td>
                                    <td>{{ $r->grade }}</td>
                                    <td>{{ $r->remark }}</td>
                                    <td>{{ ucfirst($r->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <form action="{{ route('results.download') }}" method="GET">
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <input type="hidden" name="session" value="{{ request('session') }}">
                        <input type="hidden" name="semester" value="{{ request('semester') }}">
                        <button class="btn btn-success">Download Excel</button>
                    </form>
                @elseif(request()->has('course_id'))
                    <div class="alert alert-warning mt-4">No results found for your selection.</div>
                @endif
            </div>
        </div>
    </div>
@endsection

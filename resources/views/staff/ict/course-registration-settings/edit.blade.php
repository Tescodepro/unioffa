@extends('layouts.app')

@section('title', 'Edit Course Registration Setting')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Edit Course Registration Setting</h3>
                        <p class="text-muted mb-0">Modify the deadline and late fee.</p>
                    </div>
                    <div>
                        <a href="{{ route('ict.course-registration-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('ict.course-registration-settings.update', $setting->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label">Campus <span class="text-danger">*</span></label>
                                        <select name="campus_id" class="form-select" required>
                                            <option value="">Select Campus</option>
                                            @foreach($campuses as $campus)
                                                <option value="{{ $campus->id }}" {{ $setting->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Entry Modes <small>(Leave empty to apply to all)</small></label>
                                        <select name="entry_mode[]" class="form-select select2" multiple>
                                            @foreach($entryModes as $mode)
                                                <option value="{{ $mode->code }}" {{ is_array($setting->entry_mode) && in_array($mode->code, $setting->entry_mode) ? 'selected' : '' }}>{{ $mode->name }} ({{ $mode->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Session <small>(Leave empty to apply to all)</small></label>
                                            <select name="session" class="form-select">
                                                <option value="">All Sessions</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->name }}" {{ $setting->session == $session->name ? 'selected' : '' }}>{{ $session->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Semester <small>(Leave empty to apply to all)</small></label>
                                            <select name="semester" class="form-select">
                                                <option value="">All Semesters</option>
                                                @foreach($semesters as $semester)
                                                    <option value="{{ $semester->code }}" {{ $setting->semester == $semester->code ? 'selected' : '' }}>{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Closing Date <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="closing_date" class="form-control" value="{{ \Carbon\Carbon::parse($setting->closing_date)->format('Y-m-d\TH:i') }}" required>
                                    </div>

                                     <div class="mb-3">
                                        <label class="form-label">Late Registration Fee (₦) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="late_registration_fee" class="form-control" value="{{ $setting->late_registration_fee }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Excluded Matric Numbers <small>(Optional, comma-separated)</small></label>
                                        <textarea name="excluded_matric_numbers" class="form-control" rows="3" placeholder="MAT/001, MAT/002">{{ old('excluded_matric_numbers', is_array($setting->excluded_matric_numbers) ? implode(', ', $setting->excluded_matric_numbers) : '') }}</textarea>
                                        <small class="text-muted"><i class="ti ti-info-circle text-primary"></i> Students listed here will not be charged late registration fees.</small>
                                    </div>

                                    <div class="mt-4 text-end">
                                        <button type="submit" class="btn btn-primary">Update Setting</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

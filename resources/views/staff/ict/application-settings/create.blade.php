@extends('layouts.app')

@section('title', 'Create Application Setting')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="page-title mb-1">Create Application Setting</h3>
                        <p class="text-muted mb-0">Add a new admission type configuration</p>
                    </div>
                    <a href="{{ route('ict.application_settings.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('ict.application_settings.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                {{-- Basic Details --}}
                                <div class="col-12">
                                    <h5 class="fw-bold text-primary mb-1">Basic Details</h5>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Application Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        placeholder="e.g. UTME Admission" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Application Code <span class="text-danger">*</span></label>
                                    <input type="text" name="application_code" class="form-control"
                                        value="{{ old('application_code') }}" placeholder="e.g. UTME" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_session" class="form-control"
                                        value="{{ old('academic_session', '2024/2025') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Admission Duration (Years)</label>
                                    <input type="text" name="admission_duration" class="form-control"
                                        value="{{ old('admission_duration', '4') }}">
                                </div>

                                {{-- Fees --}}
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-primary mb-1">Financial Information</h5>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Application Fee (₦) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₦</span>
                                        <input type="number" step="0.01" name="application_fee" class="form-control"
                                            value="{{ old('application_fee', '0') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Acceptance Fee (₦) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">₦</span>
                                        <input type="number" step="0.01" name="acceptance_fee" class="form-control"
                                            value="{{ old('acceptance_fee', '0') }}" required>
                                    </div>
                                </div>

                                {{-- Configuration --}}
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-primary mb-1">System Configuration</h5>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2"
                                        placeholder="Brief description...">{{ old('description') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Visibility</label>
                                    <select name="enabled" class="form-select">
                                        <option value="1" selected>Visible</option>
                                        <option value="0">Hidden</option>
                                    </select>
                                </div>

                                {{-- Modules --}}
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-primary mb-1">Application Modules</h5>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="modules_enable[profile]"
                                            value="1" id="mod_profile" checked>
                                        <label class="form-check-label" for="mod_profile">Profile Information</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="modules_enable[olevel]"
                                            value="1" id="mod_olevel" checked>
                                        <label class="form-check-label" for="mod_olevel">O-Level Results</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="modules_enable[alevel]"
                                            value="1" id="mod_alevel">
                                        <label class="form-check-label" for="mod_alevel">A-Level Results</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="modules_enable[jamb_detail]"
                                            value="1" id="mod_jamb" checked>
                                        <label class="form-check-label" for="mod_jamb">JAMB Details</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            name="modules_enable[course_of_study]" value="1" id="mod_course" checked>
                                        <label class="form-check-label" for="mod_course">Course Selection</label>
                                    </div>
                                </div>

                                {{-- Required Documents --}}
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-primary mb-1">Required Documents</h5>
                                </div>
                                @foreach(['olevel' => 'O-Level Certificate', 'alevel' => 'A-Level Certificate', 'birth_certificate' => 'Birth Certificate', 'state_of_origin' => 'State of Origin', 'primary_school_testimonial' => 'Primary School Testimonial', 'reference_letter' => 'Reference Letter'] as $key => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="modules_enable[documents][]"
                                                value="{{ $key }}" id="doc_{{ $key }}">
                                            <label class="form-check-label" for="doc_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-12 mt-3">
                                    <label class="form-label">Add Custom Document</label>
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="text" class="form-control" id="custom_doc_input"
                                            placeholder="e.g. Health Report">
                                        <button class="btn btn-primary" type="button" id="add_custom_doc_btn"><i
                                                class="ti ti-plus"></i></button>
                                    </div>
                                    <div class="row mt-2" id="custom_docs_container">
                                        {{-- Custom documents appended here --}}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-plus"></i> Create Setting
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.getElementById('add_custom_doc_btn').addEventListener('click', function () {
                const input = document.getElementById('custom_doc_input');
                const value = input.value.trim();

                if (!value) {
                    alert('Please enter a document name');
                    return;
                }

                const container = document.getElementById('custom_docs_container');
                const id = 'custom_doc_' + Date.now();

                const col = document.createElement('div');
                col.className = 'col-md-4 mb-2';
                col.innerHTML = `
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" name="modules_enable[documents][]" value="${value}" id="${id}" checked>
                                <label class="form-check-label me-2" for="${id}">
                                    ${value} <span class="badge bg-info-transparent text-info ms-1">Custom</span>
                                </label>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-auto" onclick="this.closest('.col-md-4').remove()">
                                    <i class="ti ti-x"></i>
                                </button>
                            </div>
                        `;

                container.appendChild(col);
                input.value = '';
            });
        </script>
    @endpush
@endsection
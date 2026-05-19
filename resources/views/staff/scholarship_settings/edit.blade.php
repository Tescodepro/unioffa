@extends('layouts.app')

@section('title', 'Edit Scholarship Setting')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Edit Scholarship Setting</h3>
                        <nav>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('scholarship-settings.index') }}">Settings</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('scholarship-settings.update', $scholarshipSetting->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Academic Session</label>
                                    <input type="text" name="academic_session" class="form-control" value="{{ old('academic_session', $scholarshipSetting->academic_session) }}" required>
                                    @error('academic_session') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Application Type</label>
                                    <select name="application_type" class="form-control" required>
                                        <option value="all" {{ old('application_type', $scholarshipSetting->application_type) == 'all' ? 'selected' : '' }}>All (UTME & DE)</option>
                                        <option value="utme" {{ old('application_type', $scholarshipSetting->application_type) == 'utme' ? 'selected' : '' }}>UTME</option>
                                        <option value="direct_entry" {{ old('application_type', $scholarshipSetting->application_type) == 'direct_entry' ? 'selected' : '' }}>Direct Entry</option>
                                    </select>
                                    @error('application_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Minimum JAMB Score</label>
                                    <input type="number" name="min_jamb_score" class="form-control" value="{{ old('min_jamb_score', $scholarshipSetting->min_jamb_score) }}" required>
                                    @error('min_jamb_score') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $scholarshipSetting->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Setting is Active</label>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Custom Form Fields (What the student should fill)</h5>
                            
                            <div id="form-fields-container">
                                @if($scholarshipSetting->form_fields && is_array($scholarshipSetting->form_fields))
                                    @foreach($scholarshipSetting->form_fields as $index => $field)
                                        <div class="row mb-2 field-row">
                                            <div class="col-md-3">
                                                <input type="text" name="form_fields[{{ $index }}][name]" class="form-control" value="{{ $field['name'] }}" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="form_fields[{{ $index }}][label]" class="form-control" value="{{ $field['label'] }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="form_fields[{{ $index }}][type]" class="form-control" required>
                                                    <option value="text" {{ $field['type'] == 'text' ? 'selected' : '' }}>Short Text</option>
                                                    <option value="textarea" {{ $field['type'] == 'textarea' ? 'selected' : '' }}>Long Text (Textarea)</option>
                                                    <option value="number" {{ $field['type'] == 'number' ? 'selected' : '' }}>Number</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.field-row').remove()"><i class="ti ti-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 mb-4" onclick="addFormField()">
                                <i class="ti ti-plus"></i> Add Field
                            </button>

                            <div class="text-end">
                                <a href="{{ route('scholarship-settings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Setting</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let fieldIndex = {{ is_array($scholarshipSetting->form_fields) ? count($scholarshipSetting->form_fields) : 0 }};

        function addFormField() {
            const container = document.getElementById('form-fields-container');
            const row = document.createElement('div');
            row.className = 'row mb-2 field-row';
            row.innerHTML = `
                <div class="col-md-3">
                    <input type="text" name="form_fields[${fieldIndex}][name]" class="form-control" placeholder="Field ID (e.g. why_scholarship)" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="form_fields[${fieldIndex}][label]" class="form-control" placeholder="Field Label (e.g. Why do you need this?)" required>
                </div>
                <div class="col-md-3">
                    <select name="form_fields[${fieldIndex}][type]" class="form-control" required>
                        <option value="text">Short Text</option>
                        <option value="textarea">Long Text (Textarea)</option>
                        <option value="number">Number</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger w-100" onclick="this.closest('.field-row').remove()"><i class="ti ti-trash"></i></button>
                </div>
            `;
            container.appendChild(row);
            fieldIndex++;
        }
    </script>
@endsection

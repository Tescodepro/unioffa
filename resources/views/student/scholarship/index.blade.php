@extends('layouts.app')

@section('title', 'Scholarship Application')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Header -->
        @include('student.partials.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('student.partials.sidebar')
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">Scholarship Application</h3>
                        <p>Your JAMB Score: <strong>{{ $jambScore }}</strong></p>
                    </div>
                </div>
                <!-- /Page Header -->
                
                @include('layouts.flash-message')

                <div class="row">
                    <div class="col-xl-12 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Apply for Scholarship</h4>
                            </div>
                            <div class="card-body">
                                @if($existingApplication)
                                    <div class="alert alert-info">
                                        You have already submitted a scholarship application. 
                                        Status: <strong>{{ ucfirst($existingApplication->status) }}</strong>
                                        @if($existingApplication->status == 'approved')
                                            (Granted: {{ $existingApplication->granted_percentage }}%)
                                        @endif
                                    </div>
                                @elseif($settings->isEmpty())
                                    <div class="alert alert-warning">
                                        No active scholarship applications are currently available for your entry mode, academic session, or JAMB score.
                                    </div>
                                @else
                                    <form action="{{ route('student.scholarship.store') }}" method="POST">
                                        @csrf
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label">Select Scholarship Scheme <span class="text-danger">*</span></label>
                                                <select name="scholarship_setting_id" class="form-select" required id="scholarshipScheme">
                                                    <option value="">-- Select --</option>
                                                    @foreach($settings as $setting)
                                                        <option value="{{ $setting->id }}" data-fields="{{ json_encode($setting->form_fields) }}">
                                                            {{ $setting->academic_session }} - Min JAMB: {{ $setting->min_jamb_score }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Requested Scholarship Percentage (%) <span class="text-danger">*</span></label>
                                                <input type="number" name="requested_percentage" class="form-control" min="0" max="100" required>
                                            </div>
                                        </div>

                                        <!-- Dynamic Form Fields Container -->
                                        <div id="dynamicFieldsContainer" class="row">
                                            <!-- Fields will be injected here by JS -->
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-send me-1"></i> Submit Application
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const schemeSelect = document.getElementById('scholarshipScheme');
        const container = document.getElementById('dynamicFieldsContainer');

        if (schemeSelect) {
            schemeSelect.addEventListener('change', function() {
                container.innerHTML = ''; // clear previous
                
                const selectedOption = this.options[this.selectedIndex];
                if (!selectedOption.value) return;

                const fieldsDataStr = selectedOption.getAttribute('data-fields');
                if (!fieldsDataStr) return;

                try {
                    const fields = JSON.parse(fieldsDataStr);
                    if (Array.isArray(fields)) {
                        fields.forEach((field, index) => {
                            const colDiv = document.createElement('div');
                            colDiv.className = 'col-md-6 mb-3';
                            
                            const label = document.createElement('label');
                            label.className = 'form-label';
                            label.innerText = field.label + (field.required ? ' *' : '');
                            if (field.required) {
                                label.innerHTML = field.label + ' <span class="text-danger">*</span>';
                            }

                            let input;
                            if (field.type === 'textarea') {
                                input = document.createElement('textarea');
                                input.className = 'form-control';
                                input.name = `form_data[${field.name}]`;
                                if (field.required) input.required = true;
                            } else {
                                input = document.createElement('input');
                                input.type = field.type;
                                input.className = 'form-control';
                                input.name = `form_data[${field.name}]`;
                                if (field.required) input.required = true;
                            }

                            colDiv.appendChild(label);
                            colDiv.appendChild(input);
                            container.appendChild(colDiv);
                        });
                    }
                } catch (e) {
                    console.error('Error parsing dynamic fields', e);
                }
            });
        }
    });
</script>
@endpush

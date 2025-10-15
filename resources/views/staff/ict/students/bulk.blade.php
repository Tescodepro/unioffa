@extends('layouts.app')
@section('title', 'Bulk Upload Students')

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
                    <h3 class="page-title mb-1">Bulk Upload Students</h3>
                    <p class="text-muted mb-0">Upload multiple student records at once using Excel or CSV</p>
                </div>
                <div>
                    <a href="{{ route('ict.students.bulk.template') }}" class="btn btn-success me-2">
                        <i class="ti ti-download"></i> Download Template
                    </a>
                    <a href="{{ route('ict.students.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check-circle me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session()->has('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>Warning!</strong> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Upload Errors --}}
            @if(session()->has('upload_errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-triangle me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <strong>The following errors occurred during upload:</strong>
                            <div class="mt-2" style="max-height: 300px; overflow-y: auto;">
                                <ul class="mb-0 ps-3">
                                    @foreach(session('upload_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-triangle me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                {{-- Upload Form --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Upload File</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('ict.students.bulk.upload') }}" 
                                  enctype="multipart/form-data" id="bulkUploadForm">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="form-label">Select Excel or CSV File <span class="text-danger">*</span></label>
                                    <input type="file" 
                                           name="file" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           accept=".xlsx,.xls,.csv"
                                           required
                                           id="fileInput">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Accepted formats: .xlsx, .xls, .csv (Max size: 10MB)
                                    </small>
                                </div>

                                <div class="mb-3" id="filePreview" style="display: none;">
                                    <div class="alert alert-info">
                                        <strong>Selected file:</strong> <span id="fileName"></span><br>
                                        <strong>Size:</strong> <span id="fileSize"></span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                                        <i class="ti ti-upload"></i> Upload Students
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('fileInput').value = ''; document.getElementById('filePreview').style.display = 'none';">
                                        <i class="ti ti-x"></i> Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="ti ti-info-circle"></i> Instructions
                            </h5>
                        </div>
                        <div class="card-body">
                            <ol class="ps-3 mb-0">
                                <li class="mb-2">Download the template file</li>
                                <li class="mb-2">Fill in student data following the column headers</li>
                                <li class="mb-2">Save the file in Excel (.xlsx) or CSV format</li>
                                <li class="mb-2">Upload the completed file</li>
                                <li class="mb-0">Review the results after upload</li>
                            </ol>

                            <hr>

                            <h6 class="mb-2"><strong>Required Fields:</strong></h6>
                            <ul class="small ps-3 mb-3">
                                <li>First Name</li>
                                <li>Last Name</li>
                                <li>Email</li>
                                <li>Department Code</li>
                                <li>Entry Mode</li>
                                <li>Admission Year</li>
                            </ul>

                            <h6 class="mb-2"><strong>Valid Entry Modes:</strong></h6>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-info">UTME</span>
                                <span class="badge bg-info">DE</span>
                                <span class="badge bg-info">TRANSFER</span>
                                <span class="badge bg-info">DIPLOMA</span>
                                <span class="badge bg-info">TOPUP</span>
                                <span class="badge bg-info">IDELUTME</span>
                                <span class="badge bg-info">IDELDE</span>
                            </div>

                            <hr>

                            <div class="alert alert-warning small mb-0">
                                <i class="ti ti-alert-triangle"></i>
                                <strong>Note:</strong> Duplicate emails or phone numbers will be skipped
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sample Data Format --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sample Data Format</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>first_name</th>
                                    <th>last_name</th>
                                    <th>email</th>
                                    <th>phone</th>
                                    <th>department_code</th>
                                    <th>level</th>
                                    <th>gender</th>
                                    <th>admission_year</th>
                                    <th>entry_mode</th>
                                    <th>dob</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John</td>
                                    <td>Doe</td>
                                    <td>john.doe@example.com</td>
                                    <td>08012345678</td>
                                    <td>CSC</td>
                                    <td>100</td>
                                    <td>male</td>
                                    <td>2024/2025</td>
                                    <td>UTME</td>
                                    <td>2005-05-15</td>
                                </tr>
                                <tr>
                                    <td>Jane</td>
                                    <td>Smith</td>
                                    <td>jane.smith@example.com</td>
                                    <td>08087654321</td>
                                    <td>ENG</td>
                                    <td>200</td>
                                    <td>female</td>
                                    <td>2023/2024</td>
                                    <td>DE</td>
                                    <td>2004-08-20</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File preview
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            document.getElementById('filePreview').style.display = 'block';
        }
    });

    // Form submission
    document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('uploadBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    });
</script>
@endpush
@endsection
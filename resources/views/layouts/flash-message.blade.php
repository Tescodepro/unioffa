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
        @if(session()->has('upload_success_count'))
            <br><small>Successfully uploaded: {{ session('upload_success_count') }} students</small>
        @endif
        @if(session()->has('upload_skip_count'))
            <br><small>Skipped: {{ session('upload_skip_count') }} rows</small>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Upload Errors with Better Formatting --}}
@if(session()->has('upload_errors'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="ti ti-alert-triangle me-2 mt-1"></i>
            <div class="flex-grow-1">
                <strong>The following errors occurred during upload:</strong>
                <div class="mt-2" style="max-height: 400px; overflow-y: auto;">
                    <ul class="mb-0 ps-3 small">
                        @foreach(session('upload_errors') as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @if(session()->has('upload_success_count') && session('upload_success_count') > 0)
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-success">
                            <i class="ti ti-check"></i> 
                            {{ session('upload_success_count') }} student(s) were successfully uploaded
                        </small>
                    </div>
                @endif
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
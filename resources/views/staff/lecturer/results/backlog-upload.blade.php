@extends('layouts.app')

@section('title', 'Upload Results')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <h3 class="mb-4">Upload Student Results</h3>

            <div class="row g-4">
                <div class="col-lg-12">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">
                                <i class="fa fa-upload me-2"></i> Result Upload
                            </h4>
                        </div>

                        <div class="card-body">
                            <p class="text-muted mb-3">
                                Upload an Excel file containing student results. Make sure it follows the required template.
                            </p>

                            <a href="{{ route('backlog.upload.template') }}" class="btn btn-outline-secondary w-100 mb-3">
                                <i class="fa fa-file-excel me-1"></i> Download Excel Template
                            </a>

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form action="{{ route('backlog.upload.process') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Excel File</label>
                                    <input type="file" name="file" class="form-control form-control-lg" required>
                                    <small class="text-muted">Allowed formats: XLS, XLSX.</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fa fa-check me-1"></i> Upload Results
                                </button>
                            </form>

                        </div>
                    </div>

                    @if(session('upload_logs'))
                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-list me-2"></i> Upload Logs
                                </h5>
                            </div>

                            <div class="card-body">
                                @php $log = session('upload_logs'); @endphp

                                @foreach($log as $label => $items)
                                    @if(count($items))
                                        <h6 class="text-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $label)) }}
                                        </h6>
                                        <ul class="mb-3">
                                            @foreach($items as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

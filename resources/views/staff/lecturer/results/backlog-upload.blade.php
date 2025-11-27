@extends('layouts.app')

@section('title', 'Upload Backlog Result')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container py-4">

            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fa fa-upload me-2"></i>
                                Backlog Result Upload
                            </h4>
                        </div>
                        <div class="card-body">

                            <p class="text-muted mb-3">
                                Upload an Excel sheet containing backlog results. 
                                Ensure the sheet follows the template format. You can download the template below.
                            </p>

                            <a href="{{ route('backlog.upload.template') }}" class="btn btn-outline-secondary mb-3 w-100">
                                <i class="fa fa-file-excel me-1"></i> Download Excel Template
                            </a>

                            @if (session('error'))
                                <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('backlog.upload.preview') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Select Excel File</label>
                                    <input type="file" name="file" class="form-control form-control-lg" required>
                                    <small class="text-muted">Only XLS or XLSX files are allowed.</small>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    <i class="fa fa-eye me-1"></i> Preview Upload
                                </button>
                            </form>

                        </div>
                    </div>

                    @if(session('upload_logs'))
                        <div class="card shadow-sm border-0 mt-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fa fa-list me-2"></i>Upload Logs</h5>
                            </div>
                            <div class="card-body">
                                @php $log = session('upload_logs'); @endphp

                                @foreach($log as $label => $items)
                                    @if(count($items))
                                        <h6 class="text-secondary">{{ ucfirst(str_replace('_', ' ', $label)) }}</h6>
                                        <ul>
                                            @foreach($items as $i)
                                                <li>{{ $i }}</li>
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

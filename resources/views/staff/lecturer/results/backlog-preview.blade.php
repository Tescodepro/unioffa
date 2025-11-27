@extends('layouts.app')

@section('title', 'Preview Backlog Upload')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container py-4">

            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">
                                <i class="fa fa-eye me-2"></i>
                                Preview Backlog Upload
                            </h4>
                        </div>
                        <div class="card-body">

                            <p class="text-muted mb-3">
                                Review the uploaded Excel data below before confirming the upload. 
                                Make sure all information is correct.
                            </p>

                            <form action="{{ route('backlog.upload.process') }}" method="get">
                                @csrf
                                <input type="hidden" name="file_data" value="{{ $file_data }}">

                                <div class="table-responsive mb-3">
                                    <table class="table table-striped table-bordered table-hover align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                @foreach(array_keys($rows[0]) as $h)
                                                    <th>{{ ucfirst(str_replace('_', ' ', $h)) }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rows as $r)
                                            <tr>
                                                @foreach($r as $v)
                                                    <td>{{ $v }}</td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('backlog.upload.page') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left me-1"></i> Go Back
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-check me-1"></i> Confirm Upload
                                    </button>
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

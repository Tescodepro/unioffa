@extends('layouts.app')

@section('title', 'Report by Level')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Report by Level</h4>
                <div>
                    <a href="{{ route('bursary.reports.export', ['type' => 'level', 'format' => 'pdf']) }}" class="btn btn-sm btn-danger">
                        <i class="ti ti-file-type-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('bursary.reports.export', ['type' => 'level', 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">
                        <i class="ti ti-file-spreadsheet"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card p-3 shadow-sm">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Level</th>
                            <th>Expected (₦)</th>
                            <th>Received (₦)</th>
                            <th>Outstanding (₦)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $row)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $row['level'] }}</td>
                                <td>{{ number_format($row['expected'], 2) }}</td>
                                <td>{{ number_format($row['received'], 2) }}</td>
                                <td>{{ number_format($row['outstanding'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

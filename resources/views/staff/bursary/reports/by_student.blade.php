@extends('layouts.app')

@section('title', 'Report by Student')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Report by Student</h4>
                <div>
                    <a href="{{ route('bursary.reports.export', ['type' => 'student', 'format' => 'pdf']) }}" class="btn btn-sm btn-danger">
                        <i class="ti ti-file-type-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('bursary.reports.export', ['type' => 'student', 'format' => 'xlsx']) }}" class="btn btn-sm btn-success">
                        <i class="ti ti-file-spreadsheet"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card p-3 shadow-sm">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Matric Number</th>
                            <th>Faculty</th>
                            <th>Department</th>
                            <th>Amount (₦)</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $txn)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $txn['student_name'] }}</td>
                                <td>{{ $txn['matric_number'] }}</td>
                                <td>{{ $txn['faculty'] }}</td>
                                <td>{{ $txn['department'] }}</td>
                                <td>{{ number_format($txn['amount'], 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $txn['status'] == 'Success' ? 'success' : 'danger' }}">
                                        {{ $txn['status'] }}
                                    </span>
                                </td>
                                <td>{{ $txn['reference'] }}</td>
                                <td>{{ $txn['date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

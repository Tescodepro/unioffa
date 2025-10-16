@extends('layouts.app')

@section('title', 'Verify Payment')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <h4 class="mb-3">Verify Payment by Reference</h4>

            {{-- Alerts --}}
            @include('layouts.flash-message')

            {{-- Verification Form --}}
            <form action="{{ route('bursary.verify.action') }}" method="POST" class="card p-3 shadow-sm mb-4">
                @csrf
                <div class="form-group mb-3">
                    <label for="reference" class="form-label">Enter Reference Number</label>
                    <input type="text" id="reference" name="reference" class="form-control" placeholder="e.g. TXN-12345ABC" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-refresh"></i> Verify Payment
                </button>
            </form>

            {{-- Verification Result --}}
            @if(session('verifyData'))
                @php $data = session('verifyData'); @endphp
                <div class="card p-3 shadow-sm">
                    <h5 class="mb-3 text-primary">Verification Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Payer Name</th>
                            <td>{{ $data['payer_name'] }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $data['payer_email'] }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>₦{{ number_format($data['amount'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Reference</th>
                            <td>{{ $data['reference'] }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($data['status'] === 'success')
                                    <span class="badge bg-success">Successful</span>
                                @else
                                    <span class="badge bg-danger text-white">Failed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Gateway Response</th>
                            <td>{{ $data['gateway_response'] }}</td>
                        </tr>
                        <tr>
                            <th>Payment Channel</th>
                            <td>{{ $data['channel'] }}</td>
                        </tr>
                        <tr>
                            <th>Paid At</th>
                            <td>{{ $data['paid_at'] }}</td>
                        </tr>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

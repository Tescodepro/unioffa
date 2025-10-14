@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
<div class="main-wrapper">

    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <h4 class="mb-3">Payment Settings</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('bursary.settings.store') }}" method="POST" class="card p-4 shadow-sm mb-4">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label>Payment Type</label>
                        <input type="text" name="payment_type" class="form-control" required placeholder="e.g. tuition">
                    </div>
                    <div class="col-md-2">
                        <label>Amount (₦)</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Session</label>
                        <input type="text" name="session" class="form-control" placeholder="e.g. 2024/2025" required>
                    </div>
                    <div class="col-md-2">
                        <label>Student Type</label>
                        <input type="text" name="student_type" class="form-control" placeholder="REGULAR / TOPUP">
                    </div>
                    <div class="col-md-12 mt-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-save"></i> Save Setting
                        </button>
                    </div>
                </div>
            </form>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Existing Payment Settings</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment Type</th>
                                    <th>Amount (₦)</th>
                                    <th>Session</th>
                                    <th>Student Type</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                    <tr>
                                        <td>{{ ucfirst($setting->payment_type) }}</td>
                                        <td>{{ number_format($setting->amount, 2) }}</td>
                                        <td>{{ $setting->session }}</td>
                                        <td>{{ $setting->student_type ?? '—' }}</td>
                                        <td>{{ $setting->description ?? '—' }}</td>
                                        <td>{{ $setting->created_at->format('d M, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No payment settings available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $settings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

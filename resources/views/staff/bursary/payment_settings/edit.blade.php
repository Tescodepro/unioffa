@extends('layouts.app')

@section('title', 'Edit Payment Setting')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">Edit Payment Setting</h4>
                        <p class="text-muted small mb-0">
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($paymentSetting->payment_type) }}</span>
                            · {{ $paymentSetting->session }}
                            @if ($paymentSetting->semester) · {{ $paymentSetting->semester }} Semester @endif
                        </p>
                    </div>
                    <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-light border shadow-sm btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Back to Settings
                    </a>
                </div>

                @include('layouts.flash-message')

                <form action="{{ route('bursary.payment-settings.update', $paymentSetting->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('staff.bursary.payment_settings._form')

                    <div class="d-flex justify-content-end gap-2 mb-5">
                        <a href="{{ route('bursary.payment-settings.index') }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-2"></i> Update Payment Setting
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('staff.bursary.payment_settings._form_scripts')
@endpush
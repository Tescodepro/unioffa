@extends('layouts.app')

@section('title', 'Hostel Accommodation')

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .badge-male {
        background-color: #007bff;
    }
    .badge-female {
        background-color: #e83e8c;
    }
</style>
@endpush

@section('content')
<div class="main-wrapper">
    @include('student.partials.header')
    @include('student.partials.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div class="my-auto mb-4">
                    <h3 class="page-title mb-1">Hostel Accommodation</h3>
                    <p class="mb-1 text-muted">
                        {{ activeSession()->name ?? 'N/A' }} session
                    </p>
                </div>
                <div class="my-auto mt-3 mt-lg-0">
                    <a href="{{ route('students.dashboard') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            <!-- /Page Header -->

            @include('layouts.flash-message')

            <!-- Hostel Assignment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Hostel Assignment</h4>
                </div>
                <div class="card-body p-3">
                    @if ($assignment)
                        <div class="alert alert-success">
                            <h5 class="mb-2">🎉 You have been assigned a hostel</h5>
                            <p class="mb-1"><strong>Hostel:</strong> {{ $assignment->room->hostel->name }}</p>
                            <p class="mb-1"><strong>Category:</strong> 
                                <span class="badge {{ $assignment->room->hostel->category == 'male' ? 'badge-male' : 'badge-female' }}">
                                    {{ ucfirst($assignment->room->hostel->category) }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Room Number:</strong> {{ $assignment->room->room_number }}</p>
                            <p class="mb-0"><strong>Price:</strong> ₦{{ number_format($assignment->room->hostel->price, 2) }}</p>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-3">You have not been assigned to a hostel yet.</p>
                            <form action="{{ route('students.hostel.index') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Assign Me to a Room
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Hostel Assignment -->

        </div>
    </div>
</div>
@endsection

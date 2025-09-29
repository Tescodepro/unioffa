@extends('layouts.app')

@section('title', 'Course Registration')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('student.partials.header')


        @include('student.partials.sidebar')

        <div class="page-wrapper">
            <div class="content">

                <!-- Page Header -->
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-4">
                        <h3 class="page-title mb-1">Course Registration</h3>
                        <p class="mb-1 text-muted">Register your courses for the current academic session.</p>
                    </div>
                    <div class="my-auto mt-3 mt-lg-0">
                        <div class="row g-2">
                            <div class="col-12">
                                <a href="{{ route('students.dashboard') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-home"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="bg-light p-3 rounded shadow-sm">
                            <p class="mb-1"><strong>Current Session:</strong> {{ activeSession()->name ?? 'No active session' }}
                            </p>
                            <p class="mb-0"><strong>Current Semester:</strong>
                                {{ activeSemester()->name ?? 'No active semester' }}</p>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                @include('layouts.flash-message')

                @if ( !$payment_status['allCleared'] && (isset($payment_status['tuition']) && $payment_status['tuition']['percentage_paid'] >= 60 && strtolower($current_semester) === '1st'))
                    {{-- Tuition ≥ 60% and it’s first semester --}}
                    @include('student.partials.available-courses')

                @elseif (!$payment_status['allCleared'])

                    @include('student.partials.payment-warning')

                @else
                    {{-- All cleared --}}
                    @include('student.partials.available-courses')
                @endif


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();

            $('#select-all').on('click', function() {
                $('input[name="courses[]"]').prop('checked', this.checked);
            });
        });
    </script>
@endpush

@extends('layouts.app')
@section('title', 'Summer Registration Requests')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Summer Registration Requests</h3>
                        <p class="text-muted mb-0">Manage student requests for exceeding the maximum 6 courses.</p>
                    </div>
                </div>

                @include('layouts.flash-message')

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Pending Requests</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Matric Number</th>
                                        <th>Courses Requested</th>
                                        <th>Reason for Increase</th>
                                        <th>Date Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pendingRequests as $request)
                                        <tr>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="#">{{ $request->student->first_name }} {{ $request->student->last_name }}</a>
                                                </h2>
                                            </td>
                                            <td>{{ $request->student->matric_number ?? 'N/A' }}</td>
                                            <td>{{ is_array($request->courses) ? count($request->courses) : 0 }} courses</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#reasonModal{{ $request->id }}">
                                                    View Reason
                                                </button>
                                                
                                                <!-- Reason Modal -->
                                                <div class="modal fade" id="reasonModal{{ $request->id }}" tabindex="-1" aria-labelledby="reasonModalLabel{{ $request->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="reasonModalLabel{{ $request->id }}">Reason for Request</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ $request->reason_for_increase }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <form action="{{ route('vc.summer.approve', $request->id) }}" method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this request?');">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('vc.summer.reject', $request->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this request?');">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No pending summer registration requests found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $pendingRequests->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

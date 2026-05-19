@extends('layouts.app')
@section('title', 'Public Relations Officer Dashboard')

@section('content')
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">

                {{-- Page Header --}}
                <div class="d-md-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="page-title mb-1">Public Relations Officer Dashboard</h3>
                        <p class="text-muted mb-0">Manage university announcements, publications, and news articles.</p>
                    </div>
                </div>

                {{-- KPI Stats Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card border-0 border-bottom border-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-primary me-3">
                                        <i class="ti ti-news fs-3 text-white"></i>
                                    </span>
                                    <div>
                                        <p class="text-muted small mb-0">Total News Articles</p>
                                        <h3 class="mb-0">{{ number_format($totalNews) }}</h3>
                                        <small class="text-muted">On record</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card border-0 border-bottom border-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-success me-3">
                                        <i class="ti ti-circle-check fs-3 text-white"></i>
                                    </span>
                                    <div>
                                        <p class="text-muted small mb-0">Active / Published</p>
                                        <h3 class="mb-0">{{ number_format($activeNews) }}</h3>
                                        <small class="text-muted">Visible on website</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card border-0 border-bottom border-warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg bg-warning me-3">
                                        <i class="ti ti-edit fs-3 text-white"></i>
                                    </span>
                                    <div>
                                        <p class="text-muted small mb-0">Drafts / Inactive</p>
                                        <h3 class="mb-0">{{ number_format($inactiveNews) }}</h3>
                                        <small class="text-muted">Requires publishing</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent News Table & Quick Links --}}
                <div class="row g-4">
                    {{-- News Table --}}
                    <div class="col-xl-8 col-lg-7">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Recent News Articles</h5>
                                <a href="{{ route('ict.news.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All
                                </a>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Short Title</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentNews as $item)
                                            <tr>
                                                <td>
                                                    @if($item->image)
                                                        <img src="{{ asset('storage/' . $item->image) }}" width="45" class="rounded"
                                                            alt="News Image">
                                                    @else
                                                        <span class="text-muted small">No Image</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($item->title, 40) }}</td>
                                                <td>{{ $item->short_title }}</td>
                                                <td>
                                                    @if($item->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="ti ti-news fs-3 d-block mb-2"></i>
                                                    No news articles found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="col-xl-4 col-lg-5">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-3">
                                    <a href="{{ route('ict.news.index') }}" class="btn btn-outline-primary py-3 text-start">
                                        <i class="ti ti-news fs-4 me-2"></i>
                                        <strong>Manage News System</strong>
                                        <span class="d-block small text-muted mt-1">Publish, edit, or remove news items.</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Menu Management')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>
    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')
        <div class="page-wrapper">
            <div class="content">

                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div>
                        <h3 class="page-title mb-1">Menu Management</h3>
                        <p class="text-muted mb-0">Control which sidebar links exist and who can see them</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('ict.user-types.index') }}">System</a></li>
                                <li class="breadcrumb-item active">Menus</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                        <a href="{{ route('ict.menu-items.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Add Menu Item
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @php $grouped = $menuItems->groupBy('section'); @endphp

                @foreach($grouped as $section => $items)
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                            <span class="fw-semibold text-dark">
                                <i class="ti ti-folder me-2"></i>{{ $section }}
                            </span>
                            <span class="badge bg-secondary">{{ $items->count() }} items</span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width:40px">#</th>
                                        <th>Label</th>
                                        <th>Icon</th>
                                        <th>Route</th>
                                        <th>Permission Required</th>
                                        <th>Scope</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items->sortBy('sort_order') as $item)
                                        <tr class="{{ $item->is_active ? '' : 'table-secondary text-muted' }}">
                                            <td class="text-muted small">{{ $item->sort_order }}</td>
                                            <td class="fw-medium">
                                                <i class="{{ $item->icon }} me-1"></i>
                                                {{ $item->label }}
                                            </td>
                                            <td><code class="small">{{ $item->icon }}</code></td>
                                            <td><code class="small text-primary">{{ $item->route_name }}</code></td>
                                            <td>
                                                @if($item->permission_identifier)
                                                    <code class="small text-success">{{ $item->permission_identifier }}</code>
                                                @else
                                                    <span class="text-muted small">— any staff</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->user_type_scope)
                                                    <span class="badge bg-info text-dark">{{ $item->user_type_scope }}</span>
                                                @else
                                                    <span class="text-muted small">all</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Hidden</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('ict.menu-items.toggle', $item) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button
                                                        class="btn btn-sm {{ $item->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        title="{{ $item->is_active ? 'Hide' : 'Show' }}">
                                                        <i class="ti {{ $item->is_active ? 'ti-eye-off' : 'ti-eye' }}"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('ict.menu-items.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-primary mx-1">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                                <form action="{{ route('ict.menu-items.destroy', $item) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Delete this menu item?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                @if($menuItems->isEmpty())
                    <div class="text-center text-muted py-5">No menu items found.</div>
                @endif

            </div>
        </div>
    </div>
@endsection
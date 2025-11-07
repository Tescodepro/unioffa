@extends('layouts.app')

@section('title', 'Manage News')

@section('content')
<div id="global-loader"><div class="page-loader"></div></div>

<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">
            @include('layouts.flash-message')

            <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="page-title mb-1">Latest News</h3>
                    <p class="text-muted mb-0">Create, edit, and manage news posts.</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="ti ti-plus"></i> Add News
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Short Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                            <tr>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/'.$item->image) }}" width="55" class="rounded">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->short_title }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary editNewsBtn"
                                        data-id="{{ $item->id }}"
                                        data-title="{{ $item->title }}"
                                        data-short_title="{{ $item->short_title }}"
                                        data-slug="{{ $item->slug }}"
                                        data-content="{{ htmlspecialchars($item->content) }}"
                                        data-image="{{ $item->image }}"
                                        data-status="{{ $item->is_active }}">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No news yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ADD NEWS MODAL --}}
<div class="modal fade" id="addNewsModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" onsubmit="syncContent('add')">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Title</label>
                        <input type="text" name="short_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>

                        <div class="border p-2 mb-2 rounded bg-light">
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('bold')"><b>B</b></button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('italic')"><i>I</i></button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('insertOrderedList')">OL</button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('insertUnorderedList')">UL</button>
                        </div>

                        <div id="addEditor" contenteditable="true" class="form-control" style="min-height:150px;"></div>
                        <input type="hidden" name="content" id="addContentInput">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" checked>
                        <label class="form-check-label">Active</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save News</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- EDIT NEWS MODAL --}}
<div class="modal fade" id="editNewsModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editNewsForm" enctype="multipart/form-data" onsubmit="syncContent('edit')">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit News</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Title</label>
                        <input type="text" name="short_title" id="edit_short_title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="edit_slug" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Image</label><br>
                        <img id="editImagePreview" src="" width="80" class="rounded mb-2 d-none">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>

                        <div class="border p-2 mb-2 rounded bg-light">
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('bold')"><b>B</b></button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('italic')"><i>I</i></button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('insertOrderedList')">OL</button>
                            <button type="button" class="btn btn-sm btn-light" onclick="formatDoc('insertUnorderedList')">UL</button>
                        </div>

                        <div id="editEditor" contenteditable="true" class="form-control" style="min-height:150px;"></div>
                        <input type="hidden" name="content" id="editContentInput">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="edit_status" class="form-check-input">
                        <label class="form-check-label">Active</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Update News</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection



@push('scripts')
<script>
    function formatDoc(cmd) {
        document.execCommand(cmd, false, null);
    }

    function syncContent(type) {
        document.getElementById(type + 'ContentInput').value =
            document.getElementById(type + 'Editor').innerHTML;
    }

    document.querySelectorAll('.editNewsBtn').forEach(btn => {
        btn.onclick = () => {
            const m = new bootstrap.Modal(editNewsModal);

            edit_title.value = btn.dataset.title;
            edit_short_title.value = btn.dataset.short_title;
            edit_slug.value = btn.dataset.slug;
            editEditor.innerHTML = btn.dataset.content;
            edit_status.checked = btn.dataset.status == 1;

            if (btn.dataset.image) {
                editImagePreview.src = `/storage/${btn.dataset.image}`;
                editImagePreview.classList.remove('d-none');
            } else {
                editImagePreview.classList.add('d-none');
            }

            // editNewsForm.action = `news/${btn.dataset.id}`;
            editNewsForm.action = `{{ route('news.update', ':id') }}`.replace(':id', btn.dataset.id);

            m.show();
        }
    });
</script>
@endpush
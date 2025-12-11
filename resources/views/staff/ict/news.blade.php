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

            {{-- Page Header --}}
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

            {{-- News Table --}}
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
                                        <img src="{{ asset('storage/'.$item->image) }}" width="55" class="rounded" alt="News Image">
                                    @else
                                        <span class="text-muted small">No Image</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->title, 30) }}</td>
                                <td>{{ $item->short_title }}</td>
                                <td><small class="text-muted">{{ $item->slug }}</small></td>
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
                                        data-image="{{ $item->image }}"
                                        data-status="{{ $item->is_active }}">
                                        <i class="ti ti-edit"></i> Edit
                                    </button>
                                    {{-- Store content in a hidden div instead of data attribute --}}
                                    <div class="d-none news-content-{{ $item->id }}">{!! $item->content !!}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
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
    </div>
</div>

{{-- ==============================
     ADD NEWS MODAL
     ============================== --}}
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" onsubmit="return syncContent('add')">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="add_title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter full headline" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Short Title --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Short Title <span class="text-danger">*</span></label>
                            <input type="text" name="short_title" class="form-control @error('short_title') is-invalid @enderror" placeholder="For menus/breadcrumbs" required>
                            @error('short_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Slug (Auto-generated) --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Slug <small class="text-muted">(Auto-generated)</small></label>
                            <input type="text" name="slug" id="add_slug" class="form-control bg-light @error('slug') is-invalid @enderror" readonly required>
                        </div>

                        {{-- Image Upload with Preview --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Upload Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'addImagePreview')" required>
                            <div class="mt-2">
                                <img id="addImagePreview" src="" class="img-thumbnail d-none" style="max-height: 150px;">
                            </div>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Custom Editor --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>

                            <div class="border p-2 mb-2 rounded bg-light d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="formatDoc('add', 'bold')" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary fst-italic" onclick="formatDoc('add', 'italic')" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('add', 'insertOrderedList')" title="Ordered List"><i class="ti ti-list-numbers"></i> OL</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('add', 'insertUnorderedList')" title="Bullet List"><i class="ti ti-list"></i> UL</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addLink('add')" title="Add Link"><i class="ti ti-link"></i> Link</button>
                            </div>

                            <div id="addEditor" contenteditable="true" class="form-control" 
                                 style="min-height: 200px; height: auto; max-height: 500px; overflow-y: auto; border-color: #dee2e6;">
                            </div>
                            <input type="hidden" name="content" id="addContentInput">
                        </div>

                        {{-- Status --}}
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="activeCheck" checked>
                                <label class="form-check-label" for="activeCheck">Active / Published</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Save News</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ==============================
     EDIT NEWS MODAL
     ============================== --}}
<div class="modal fade" id="editNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editNewsForm" enctype="multipart/form-data" onsubmit="return syncContent('edit')">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>

                        {{-- Short Title --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Short Title</label>
                            <input type="text" name="short_title" id="edit_short_title" class="form-control" required>
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="edit_slug" class="form-control bg-light" required>
                        </div>

                        {{-- Image Update --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">News Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'editImagePreview')">
                            <div class="mt-2">
                                <img id="editImagePreview" src="" class="img-thumbnail d-none" style="max-height: 150px;">
                                <small class="text-muted d-block mt-1">Leave empty to keep current image</small>
                            </div>
                        </div>

                        {{-- Custom Editor --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Content</label>
                            <div class="border p-2 mb-2 rounded bg-light d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="formatDoc('edit', 'bold')" title="Bold">B</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary fst-italic" onclick="formatDoc('edit', 'italic')" title="Italic">I</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('edit', 'insertOrderedList')" title="Ordered List"><i class="ti ti-list-numbers"></i> OL</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatDoc('edit', 'insertUnorderedList')" title="Bullet List"><i class="ti ti-list"></i> UL</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addLink('edit')" title="Add Link"><i class="ti ti-link"></i> Link</button>
                            </div>

                            <div id="editEditor" contenteditable="true" class="form-control" 
                                 style="min-height: 200px; height: auto; max-height: 500px; overflow-y: auto; border-color: #dee2e6;">
                            </div>
                            <input type="hidden" name="content" id="editContentInput">
                        </div>

                        {{-- Status --}}
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_status">
                                <label class="form-check-label" for="edit_status">Active / Published </label> 
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update News</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // -----------------------------------------
    // 1. Text Editor Functions
    // -----------------------------------------
    function formatDoc(type, cmd, value = null) {
        const editor = document.getElementById(type + 'Editor');
        editor.focus();
        document.execCommand(cmd, false, value);
    }

    function addLink(type) {
        const url = prompt("Enter the URL");
        if (url) {
            formatDoc(type, "createLink", url);
        }
    }

    function syncContent(type) {
        // Copy content from Div to Hidden Input before form submission
        const content = document.getElementById(type + 'Editor').innerHTML;
        document.getElementById(type + 'ContentInput').value = content;
        return true; // Allow form submission
    }

    // -----------------------------------------
    // 2. Auto-Slug Logic (For Add Modal)
    // -----------------------------------------
    const titleInput = document.getElementById('add_title');
    if(titleInput) {
        titleInput.addEventListener('keyup', function() {
            let title = this.value;
            let slug = title.toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
            document.getElementById('add_slug').value = slug;
        });
    }

    // -----------------------------------------
    // 3. Image Preview Logic
    // -----------------------------------------
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // -----------------------------------------
    // 4. Edit Modal Logic
    // -----------------------------------------
    document.querySelectorAll('.editNewsBtn').forEach(btn => {
        btn.onclick = () => {
            const modalElement = document.getElementById('editNewsModal');
            const m = new bootstrap.Modal(modalElement);

            // Populate Text Fields
            document.getElementById('edit_title').value = btn.dataset.title;
            document.getElementById('edit_short_title').value = btn.dataset.short_title;
            document.getElementById('edit_slug').value = btn.dataset.slug;

            // Get content from hidden div (NOT from data attribute)
            const contentDiv = document.querySelector('.news-content-' + btn.dataset.id);
            const content = contentDiv ? contentDiv.innerHTML : '';
            document.getElementById('editEditor').innerHTML = content;

            // Populate Checkbox
            document.getElementById('edit_status').checked = btn.dataset.status == 1;

            // Handle Image Preview
            const preview = document.getElementById('editImagePreview');
            if (btn.dataset.image) {
                preview.src = `/storage/${btn.dataset.image}`;
                preview.classList.remove('d-none');
            } else {
                preview.src = "";
                preview.classList.add('d-none');
            }

            // Update Form Action URL
            const form = document.getElementById('editNewsForm');
            let updateUrl = "{{ route('news.update', ':id') }}";
            updateUrl = updateUrl.replace(':id', btn.dataset.id);
            form.action = updateUrl;

            m.show();
        }
    });
</script>
@endpush
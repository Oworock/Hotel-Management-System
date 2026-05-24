@extends('layouts.app')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Gallery Management</h1>
            <p class="admin-page-subtitle">Curate property images that make rooms, dining, and facilities feel real to guests.</p>
        </div>
        <button type="button" onclick="openGalleryModal()" class="btn btn-primary">
            <i class="fa-solid fa-image"></i> Add Photo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="admin-grid">
        @forelse($photos as $photo)
            <article class="admin-card">
                <div class="admin-card-media">
                    <img src="{{ \Illuminate\Support\Str::startsWith($photo->image, ['http://', 'https://']) ? $photo->image : asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}">
                </div>
                <h3 class="admin-card-title">{{ $photo->title }}</h3>
                <p class="admin-card-text">{{ \Illuminate\Support\Str::limit($photo->description ?: 'No description added.', 95) }}</p>
                <div class="admin-card-actions">
                    <span class="status-pill {{ $photo->is_active ? 'active' : '' }}">{{ $photo->is_active ? 'Active' : 'Inactive' }}</span>
                    <span class="status-pill">Order {{ $photo->order }}</span>
                </div>
                <div class="admin-card-actions">
                    <button type="button" class="btn btn-outline" onclick='editPhoto(@json($photo))'><i class="fa-solid fa-pen"></i> Edit</button>
                    <form method="POST" action="{{ route('admin.gallery.delete', $photo) }}" onsubmit="return confirm('Delete this photo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="glass-panel empty-state" style="grid-column:1 / -1;">No photos yet. Upload polished property images to start the gallery.</div>
        @endforelse
    </div>
</div>

<div id="galleryModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="galleryModalTitle" style="margin:0;color:var(--text-primary);">Add Photo</h2>
            <button type="button" class="btn btn-outline" onclick="closeGalleryModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="galleryForm" method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="order">Display Order</label>
                    <input type="number" name="order" id="order" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="image">Photo</label>
                <input type="file" name="image" id="image" accept="image/*" class="form-control" required>
                <p id="imageHint" class="form-hint">Upload a clear image. Recommended minimum width: 1200px.</p>
            </div>
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <span>Active on website</span>
                </label>
            </div>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeGalleryModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Photo</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGalleryModal() {
    const form = document.getElementById('galleryForm');
    form.reset();
    form.action = "{{ route('admin.gallery.store') }}";
    document.getElementById('galleryModalTitle').textContent = 'Add Photo';
    document.getElementById('image').required = true;
    document.getElementById('imageHint').textContent = 'Upload a clear image. Recommended minimum width: 1200px.';
    document.getElementById('is_active').checked = true;
    document.getElementById('galleryModal').classList.add('active');
}

function editPhoto(photo) {
    const form = document.getElementById('galleryForm');
    form.action = `/admin/gallery/${photo.id}/update`;
    document.getElementById('galleryModalTitle').textContent = 'Edit Photo';
    document.getElementById('title').value = photo.title || '';
    document.getElementById('description').value = photo.description || '';
    document.getElementById('order').value = photo.order || 0;
    document.getElementById('image').required = false;
    document.getElementById('imageHint').textContent = 'Leave blank to keep the current photo.';
    document.getElementById('is_active').checked = Boolean(photo.is_active);
    document.getElementById('galleryModal').classList.add('active');
}

function closeGalleryModal() {
    document.getElementById('galleryModal').classList.remove('active');
}
</script>
@endsection

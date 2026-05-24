@extends('layouts.app')

@section('title', 'Amenity Management')

@section('content')
@php
    $categories = [
        'room' => ['label' => 'Room', 'icon' => 'fa-bed'],
        'hotel' => ['label' => 'Hotel', 'icon' => 'fa-hotel'],
        'dining' => ['label' => 'Dining', 'icon' => 'fa-utensils'],
        'activity' => ['label' => 'Activity', 'icon' => 'fa-person-swimming'],
        'service' => ['label' => 'Service', 'icon' => 'fa-bell-concierge'],
    ];
@endphp

<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Amenity Management</h1>
            <p class="admin-page-subtitle">Edit the standard amenities used by room types, hotel pages, and guest-facing content.</p>
        </div>
        <button type="button" onclick="openAmenityModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Amenity
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">{{ session('error') }}</div>
    @endif

    <div class="admin-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
        @foreach($categories as $category => $meta)
            <div class="glass-panel">
                <h2 style="font-size:1.05rem;margin:0 0 1rem;color:var(--text-primary);display:flex;align-items:center;gap:0.5rem;">
                    <i class="fa-solid {{ $meta['icon'] }}" style="color:var(--primary);"></i> {{ $meta['label'] }} Amenities
                </h2>
                <div style="display:grid;gap:0.75rem;">
                    @forelse($amenities->where('category', $category) as $amenity)
                        <div style="border:1px solid var(--border-color);background:var(--surface);border-radius:var(--radius-sm);padding:0.85rem;">
                            <div style="display:flex;justify-content:space-between;gap:0.75rem;align-items:flex-start;">
                                <div>
                                    <strong style="color:var(--text-primary);">{{ $amenity->icon }} {{ $amenity->name }}</strong>
                                    <p style="margin:0.25rem 0 0;color:var(--text-secondary);font-size:0.8rem;line-height:1.5;">{{ \Illuminate\Support\Str::limit($amenity->description ?: 'No description added.', 80) }}</p>
                                </div>
                                <span class="status-pill {{ $amenity->is_active ? 'active' : '' }}">{{ $amenity->is_active ? 'Active' : 'Off' }}</span>
                            </div>
                            <div class="admin-card-actions" style="margin-top:0.85rem;">
                                <button type="button" class="btn btn-outline" onclick='editAmenity(@json($amenity))'><i class="fa-solid fa-pen"></i> Edit</button>
                                <form method="POST" action="{{ route('super_admin.amenities.delete', $amenity) }}" onsubmit="return confirm('Delete this amenity?')" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:1rem;">No amenities yet.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="amenityModal" class="modal">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="amenityModalTitle" style="margin:0;color:var(--text-primary);">Add Amenity</h2>
            <button type="button" class="btn btn-outline" onclick="closeAmenityModal()" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="amenityForm" method="POST" action="{{ route('super_admin.amenities.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="icon">Icon</label>
                    <input type="text" name="icon" id="icon" class="form-control" maxlength="50" placeholder="fa-wifi or WiFi">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="category">Category</label>
                    <select name="category" id="category" class="form-control" required>
                        @foreach($categories as $category => $meta)
                            <option value="{{ $category }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="order">Display Order</label>
                    <input type="number" name="order" id="order" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div>
            <label class="form-checkbox"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span>Active</span></label>
            <div class="admin-card-actions" style="justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeAmenityModal()">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Amenity</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAmenityModal() {
    const form = document.getElementById('amenityForm');
    form.reset();
    form.action = "{{ route('super_admin.amenities.store') }}";
    document.getElementById('amenityModalTitle').textContent = 'Add Amenity';
    document.getElementById('is_active').checked = true;
    document.getElementById('order').value = 0;
    document.getElementById('amenityModal').classList.add('active');
}

function editAmenity(amenity) {
    const form = document.getElementById('amenityForm');
    form.action = `/super-admin/amenities/${amenity.id}/update`;
    document.getElementById('amenityModalTitle').textContent = 'Edit Amenity';
    document.getElementById('name').value = amenity.name || '';
    document.getElementById('icon').value = amenity.icon || '';
    document.getElementById('category').value = amenity.category || 'hotel';
    document.getElementById('description').value = amenity.description || '';
    document.getElementById('order').value = amenity.order || 0;
    document.getElementById('is_active').checked = Boolean(amenity.is_active);
    document.getElementById('amenityModal').classList.add('active');
}

function closeAmenityModal() {
    document.getElementById('amenityModal').classList.remove('active');
}
</script>
@endsection

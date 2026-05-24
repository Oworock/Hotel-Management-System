@extends('layouts.app')

@section('title', 'Inventory & Rooms')

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">Inventory & Rooms</h1>
            <p class="admin-page-subtitle">Manage room categories, editable standard amenities, and room availability from one clean workspace.</p>
        </div>
        <button class="btn btn-primary" onclick="openRoomModal()">
            <i class="fa-solid fa-plus"></i> New Room
        </button>
    </div>

<div style="display: grid; grid-template-columns: minmax(320px, 0.95fr) minmax(0, 1.55fr); gap: 1.5rem;">
    <!-- Left Column: Manage Room Types -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Add Room Type -->
        <div class="glass-panel">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-folder-plus" style="color: var(--primary);"></i> Add Room Type</h3>
            
            <form action="{{ route('admin.room_types.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="rt-name" class="form-label">Type Name</label>
                    <input type="text" name="name" id="rt-name" class="form-control" placeholder="Executive Suite" required>
                </div>
                
                <div class="form-group">
                    <label for="rt-desc" class="form-label">Description</label>
                    <textarea name="description" id="rt-desc" class="form-control" rows="3" placeholder="Description of amenities, space..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="rt-images" class="form-label">Room Gallery Images</label>
                    <input type="file" name="images[]" id="rt-images" class="form-control" multiple accept="image/*">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rt-price" class="form-label">Price per Night ($)</label>
                        <input type="number" step="0.01" name="base_price" id="rt-price" class="form-control" placeholder="250.00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rt-capacity" class="form-label">Max Guests</label>
                        <input type="number" name="capacity" id="rt-capacity" class="form-control" placeholder="2" min="1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Standard Amenities</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; max-height: 160px; overflow-y: auto; padding-right: 0.5rem;">
                        @forelse($standardAmenities as $amenity)
                            <label class="form-checkbox"><input type="checkbox" name="amenities[]" value="{{ $amenity->name }}"> <span>{{ $amenity->icon }} {{ $amenity->name }}</span></label>
                        @empty
                            <p style="color:var(--text-secondary);font-size:0.85rem;grid-column:1 / -1;">No room amenities configured yet.</p>
                        @endforelse
                    </div>
                    @if(Route::has('super_admin.amenities'))
                        <a href="{{ route('super_admin.amenities') }}" style="display:inline-flex;margin-top:0.5rem;color:var(--primary);font-size:0.85rem;text-decoration:none;">Edit standard amenities</a>
                    @endif
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    Create Room Type
                </button>
            </form>
        </div>
        
        <!-- Room Types List -->
        <div class="glass-panel">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.25rem;"><i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Available Categories</h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($roomTypes as $type)
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; background-color: var(--surface);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <h4 style="font-size: 1rem;">{{ $type->name }}</h4>
                            <span style="font-weight: 700; color: var(--primary); font-size: 0.95rem;">${{ number_format($type->base_price, 2) }}<span style="font-size:0.75rem; font-weight:500; color:var(--text-secondary);">/nt</span></span>
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $type->description }}
                        </p>
                        <div style="display: justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-muted); display: flex; margin-top: 0.5rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <span><i class="fa-solid fa-users"></i> {{ $type->capacity }}</span>
                                <span>•</span>
                                <span><i class="fa-solid fa-door-open"></i> Rooms: {{ $type->rooms_count ?? $type->rooms()->count() }}</span>
                            </div>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-outline" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;" 
                                        onclick="openEditRoomTypeModal('{{ $type->id }}', '{{ addslashes($type->name) }}', '{{ addslashes($type->description) }}', '{{ $type->base_price }}', '{{ $type->capacity }}', {{ json_encode($type->amenities) }}, {{ json_encode($type->images) }})">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.room_types.delete', $type->id) }}" method="POST" onsubmit="return confirm('Delete Category {{ addslashes($type->name) }}? This cannot be undone.')" style="margin: 0; display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="font-size: 0.7rem; padding: 0.2rem 0.4rem; border-color: var(--danger); color: var(--danger);">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($roomTypes->hasPages())
                <div style="margin-top:1rem;">
                    {{ $roomTypes->appends(request()->except('types_page'))->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Right Column: Room Inventory -->
    <div class="glass-panel" style="display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-door-open" style="color: var(--primary);"></i> Room Fleet</h3>
            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;" onclick="openRoomModal()">
                <i class="fa-solid fa-plus"></i> New Room
            </button>
        </div>
        
        <div class="table-container" style="border: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Room No.</th>
                        <th>Type Category</th>
                        <th>Status</th>
                        <th>Capacity</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td style="font-weight: 700; font-size: 1.1rem; color: var(--primary);">#{{ $room->room_number }}</td>
                            <td>{{ $room->roomType->name }}</td>
                            <td>
                                @switch($room->status)
                                    @case('available')
                                        <span class="badge badge-success">Available</span>
                                        @break
                                    @case('booked')
                                        <span class="badge badge-info">Booked</span>
                                        @break
                                    @case('dirty')
                                        <span class="badge badge-warning">Dirty</span>
                                        @break
                                    @case('maintenance')
                                        <span class="badge badge-danger">Maintenance</span>
                                        @break
                                @endswitch
                            </td>
                            <td><i class="fa-solid fa-user"></i> {{ $room->roomType->capacity }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;" 
                                            onclick="openEditModal('{{ $room->id }}', '{{ $room->room_number }}', '{{ $room->room_type_id }}', '{{ $room->status }}')">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.rooms.delete', $room->id) }}" method="POST" onsubmit="return confirm('Delete Room #{{ $room->room_number }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; border-color: var(--danger); color: var(--danger);">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">
                                <i class="fa-regular fa-door-closed" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i> No rooms registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rooms->hasPages())
            <div style="padding: 1rem 0 0; display: flex; justify-content: center;">
                {{ $rooms->appends(request()->except('rooms_page'))->links() }}
            </div>
        @endif
    </div>
</div>
</div>

<!-- Add Room Modal -->
<div class="modal" id="addRoomModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h3><i class="fa-solid fa-plus-circle"></i> Register Room</h3>
            <button class="theme-toggle" onclick="closeRoomModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="{{ route('admin.rooms.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="room_number" class="form-label">Room Number</label>
                <input type="text" name="room_number" id="room_number" class="form-control" required placeholder="104">
            </div>
            
            <div class="form-group">
                <label for="room_type_id" class="form-label">Room Type</label>
                <select name="room_type_id" id="room_type_id" class="form-control form-select" required>
                    @foreach($roomTypesList as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (${{ $type->base_price }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="status" class="form-label">Initial Status</label>
                <select name="status" id="status" class="form-control form-select" required>
                    <option value="available">Available</option>
                    <option value="dirty">Dirty</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeRoomModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Room</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal" id="editRoomModal">
    <div class="modal-content glass-panel">
        <div class="modal-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Modify Room</h3>
            <button class="theme-toggle" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="" method="POST" id="editRoomForm">
            @csrf
            
            <div class="form-group">
                <label for="edit_room_number" class="form-label">Room Number</label>
                <input type="text" name="room_number" id="edit_room_number" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="edit_room_type_id" class="form-label">Room Type</label>
                <select name="room_type_id" id="edit_room_type_id" class="form-control form-select" required>
                    @foreach($roomTypesList as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (${{ $type->base_price }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_status" class="form-label">Current Status</label>
                <select name="status" id="edit_status" class="form-control form-select" required>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                    <option value="dirty">Dirty</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Room</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room Type Modal -->
<div class="modal" id="editRoomTypeModal">
    <div class="modal-content glass-panel" style="max-width: 500px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-pen-to-square"></i> Modify Room Type</h3>
            <button class="theme-toggle" onclick="closeEditRoomTypeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="" method="POST" id="editRoomTypeForm" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="edit_rt_name" class="form-label">Type Name</label>
                <input type="text" name="name" id="edit_rt_name" class="form-control" required placeholder="Executive Suite">
            </div>
            
            <div class="form-group">
                <label for="edit_rt_desc" class="form-label">Description</label>
                <textarea name="description" id="edit_rt_desc" class="form-control" rows="3" required placeholder="Description..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_rt_price" class="form-label">Price per Night ($)</label>
                    <input type="number" step="0.01" name="base_price" id="edit_rt_price" class="form-control" required placeholder="250.00">
                </div>
                
                <div class="form-group">
                    <label for="edit_rt_capacity" class="form-label">Max Guests</label>
                    <input type="number" name="capacity" id="edit_rt_capacity" class="form-control" min="1" required placeholder="2">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Standard Amenities</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; max-height: 160px; overflow-y: auto; padding-right: 0.5rem;">
                    @forelse($standardAmenities as $amenity)
                        <label class="form-checkbox"><input type="checkbox" name="amenities[]" value="{{ $amenity->name }}"> <span>{{ $amenity->icon }} {{ $amenity->name }}</span></label>
                    @empty
                        <p style="color:var(--text-secondary);font-size:0.85rem;grid-column:1 / -1;">No room amenities configured yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="form-group">
                <label for="edit_rt_images" class="form-label">Room Gallery Images</label>
                <input type="file" name="images[]" id="edit_rt_images" class="form-control" multiple accept="image/*">
                <div id="edit_rt_images_preview" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;"></div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" onclick="closeEditRoomTypeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Room Type</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const addModal = document.getElementById('addRoomModal');
    const editModal = document.getElementById('editRoomModal');
    const editForm = document.getElementById('editRoomForm');
    
    const editRoomTypeModal = document.getElementById('editRoomTypeModal');
    const editRoomTypeForm = document.getElementById('editRoomTypeForm');
    
    function openRoomModal() {
        addModal.classList.add('active');
    }
    function closeRoomModal() {
        addModal.classList.remove('active');
    }
    
    function openEditModal(id, number, typeId, status) {
        editForm.action = `/admin/rooms/${id}/update`;
        document.getElementById('edit_room_number').value = number;
        document.getElementById('edit_room_type_id').value = typeId;
        document.getElementById('edit_status').value = status;
        editModal.classList.add('active');
    }
    function closeEditModal() {
        editModal.classList.remove('active');
    }

    function openEditRoomTypeModal(id, name, description, price, capacity, amenities, images) {
        editRoomTypeForm.action = `/admin/room-types/${id}/update`;
        document.getElementById('edit_rt_name').value = name;
        document.getElementById('edit_rt_desc').value = description;
        document.getElementById('edit_rt_price').value = price;
        document.getElementById('edit_rt_capacity').value = capacity;
        
        // Reset checkboxes
        const checkboxes = editRoomTypeModal.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
        
        // Match checkboxes to amenities array
        if (Array.isArray(amenities)) {
            checkboxes.forEach(cb => {
                if (amenities.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        }

        // Render images thumbnails
        const previewDiv = document.getElementById('edit_rt_images_preview');
        previewDiv.innerHTML = '';
        if (Array.isArray(images)) {
            images.forEach(img => {
                if (img && img !== 'default.jpg') {
                    const imgEl = document.createElement('img');
                    imgEl.src = img;
                    imgEl.style.width = '60px';
                    imgEl.style.height = '60px';
                    imgEl.style.objectFit = 'cover';
                    imgEl.style.borderRadius = '4px';
                    imgEl.style.border = '1px solid var(--border-color)';
                    previewDiv.appendChild(imgEl);
                }
            });
        }
        
        editRoomTypeModal.classList.add('active');
    }
    
    function closeEditRoomTypeModal() {
        editRoomTypeModal.classList.remove('active');
    }
</script>
@endsection

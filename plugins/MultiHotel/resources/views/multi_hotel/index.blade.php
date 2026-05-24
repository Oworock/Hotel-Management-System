@extends('layouts.app')

@section('title', 'Multi-Hotel Property Manager')

@section('content')
<div class="animate-fade-in" style="padding: 1.5rem;">
    <!-- Header Panel -->
    <div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem; border-left: 5px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; border-radius: 12px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.5rem 0;">
                <i class="fa-solid fa-hotel" style="color: var(--primary); margin-right: 0.5rem;"></i> Multi-Hotel Property Manager
            </h2>
            <p style="color: var(--text-secondary); margin: 0;">Scope hotel rooms, room types, staff, bookings, and payments cleanly across multiple properties.</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="openCreateModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 8px;">
                <i class="fa-solid fa-plus-circle"></i> Create New Hotel
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem; background: rgba(46, 204, 113, 0.15); border: 1px solid rgba(46, 204, 113, 0.3); color: #2ecc71;">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem; background: rgba(231, 76, 60, 0.15); border: 1px solid rgba(231, 76, 60, 0.3); color: #e74c3c;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; background: rgba(231, 76, 60, 0.15); border: 1px solid rgba(231, 76, 60, 0.3); color: #e74c3c;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <strong>Validation Errors:</strong>
            </div>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Hotels Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @forelse($hotels as $hotel)
            <div class="glass-panel" style="padding: 1.5rem; border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between; position: relative; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s, box-shadow 0.2s;">
                <div>
                    <!-- Active Status Badge -->
                    <span style="position: absolute; top: 1rem; right: 1rem; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.5rem; border-radius: 12px; {{ $hotel->is_active ? 'background: rgba(46, 204, 113, 0.2); color: #2ecc71;' : 'background: rgba(149, 165, 166, 0.2); color: #95a5a6;' }}">
                        {{ $hotel->is_active ? 'Active' : 'Inactive' }}
                    </span>

                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0; padding-right: 4rem; color: var(--text-primary);">
                        {{ $hotel->name }}
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-top: 0.2rem; width: 16px;"></i>
                            <span>{{ $hotel->address }}</span>
                        </div>
                        @if($hotel->map_embed_url)
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fa-solid fa-map" style="color: var(--primary); margin-top: 0.2rem; width: 16px;"></i>
                                <span>Custom Google Map configured</span>
                            </div>
                        @endif
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-phone" style="color: var(--primary); width: 16px;"></i>
                            <span>{{ $hotel->phone }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-envelope" style="color: var(--primary); width: 16px;"></i>
                            <span>{{ $hotel->email }}</span>
                        </div>
                        @if($hotel->description)
                            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(255, 255, 255, 0.05); font-style: italic;">
                                {{ \Illuminate\Support\Str::limit($hotel->description, 120) }}
                            </div>
                        @endif
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;margin-top:.75rem;">
                            <div style="border:1px solid var(--border-color);border-radius:8px;padding:.65rem;text-align:center;">
                                <strong style="display:block;color:var(--text-primary);">{{ $hotel->room_types_count ?? 0 }}</strong>
                                <span style="font-size:.75rem;">Types</span>
                            </div>
                            <div style="border:1px solid var(--border-color);border-radius:8px;padding:.65rem;text-align:center;">
                                <strong style="display:block;color:var(--text-primary);">{{ $hotel->rooms_count ?? 0 }}</strong>
                                <span style="font-size:.75rem;">Rooms</span>
                            </div>
                            <div style="border:1px solid var(--border-color);border-radius:8px;padding:.65rem;text-align:center;">
                                <strong style="display:block;color:var(--text-primary);">{{ $hotel->staff_count ?? 0 }}</strong>
                                <span style="font-size:.75rem;">Staff</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 1rem; margin-top: auto; flex-wrap: wrap;">
                    <a class="btn btn-primary" href="{{ route('super_admin.hotels.manage', $hotel) }}" style="flex: 1 1 100%; padding: 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration:none;">
                        <i class="fa-solid fa-door-open"></i> Manage Rooms & Staff
                    </a>
                    <button class="btn btn-secondary" onclick="openEditModal({{ json_encode($hotel) }})" style="flex: 1; padding: 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;">
                        <i class="fa-solid fa-edit"></i> Edit Details
                    </button>
                    
                    <!-- Context select form -->
                    <form action="{{ route('super_admin.hotels.select') }}" method="POST" style="flex: 1; display: flex;">
                        @csrf
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                        <button type="submit" class="btn {{ session('active_hotel_id') == $hotel->id ? 'btn-success' : 'btn-primary' }}" style="width: 100%; padding: 0.5rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;">
                            @if(session('active_hotel_id') == $hotel->id)
                                <i class="fa-solid fa-circle-check"></i> Selected
                            @else
                                <i class="fa-solid fa-right-to-bracket"></i> Select Context
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="glass-panel" style="grid-column: 1 / -1; padding: 3rem; text-align: center; border-radius: 12px; background: rgba(255, 255, 255, 0.02);">
                <i class="fa-solid fa-hotel" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.25rem; color: var(--text-primary); margin-bottom: 0.5rem;">No Hotel Properties Found</h3>
                <p style="color: var(--text-secondary); margin: 0 0 1.5rem 0;">You have not configured any hotel properties yet.</p>
                <button class="btn btn-primary" onclick="openCreateModal()">
                    <i class="fa-solid fa-plus-circle"></i> Create First Hotel
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Modal -->
<div id="createHotelModal" class="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 2rem; border-radius: 12px; background: #1e1e2e; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 15px 30px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-right: 0.5rem;"></i> Create New Hotel Property
            </h3>
            <button onclick="closeCreateModal()" style="background: none; border: none; color: var(--text-secondary); font-size: 1.25rem; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('super_admin.hotels.store') }}" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Hotel Name</label>
                    <input type="text" name="name" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Address</label>
                    <input type="text" name="address" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Google Maps Embed URL / Iframe Code</label>
                    <textarea name="map_embed_url" rows="2" class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff; resize: none;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Phone</label>
                        <input type="text" name="phone" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Email</label>
                        <input type="email" name="email" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Description</label>
                    <textarea name="description" rows="3" class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff; resize: none;"></textarea>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Hotel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editHotelModal" class="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="glass-panel" style="width: 100%; max-width: 500px; padding: 2rem; border-radius: 12px; background: #1e1e2e; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 15px 30px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                <i class="fa-solid fa-edit" style="color: var(--primary); margin-right: 0.5rem;"></i> Edit Hotel Property
            </h3>
            <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-secondary); font-size: 1.25rem; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <form id="editHotelForm" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Hotel Name</label>
                    <input type="text" name="name" id="edit_name" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Address</label>
                    <input type="text" name="address" id="edit_address" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Google Maps Embed URL / Iframe Code</label>
                    <textarea name="map_embed_url" id="edit_map_embed_url" rows="2" class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff; resize: none;"></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Phone</label>
                        <input type="text" name="phone" id="edit_phone" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Email</label>
                        <input type="email" name="email" id="edit_email" required class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="form-control" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff; resize: none;"></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Status</label>
                    <select name="is_active" id="edit_is_active" class="form-control" style="width: 100%; background: #1e1e2e; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; padding: 0.6rem; color: #fff;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Hotel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createHotelModal').style.display = 'flex';
    }
    
    function closeCreateModal() {
        document.getElementById('createHotelModal').style.display = 'none';
    }
    
    function openEditModal(hotel) {
        document.getElementById('edit_name').value = hotel.name;
        document.getElementById('edit_address').value = hotel.address;
        document.getElementById('edit_map_embed_url').value = hotel.map_embed_url || '';
        document.getElementById('edit_phone').value = hotel.phone;
        document.getElementById('edit_email').value = hotel.email;
        document.getElementById('edit_description').value = hotel.description || '';
        document.getElementById('edit_is_active').value = hotel.is_active ? "1" : "0";
        
        // Dynamic route action setting
        document.getElementById('editHotelForm').action = '/super-admin/hotels/' + hotel.id + '/update';
        document.getElementById('editHotelModal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('editHotelModal').style.display = 'none';
    }
    
    // Close modals on clicking backdrop
    window.onclick = function(event) {
        let createModal = document.getElementById('createHotelModal');
        let editModal = document.getElementById('editHotelModal');
        if (event.target == createModal) {
            closeCreateModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
    }
</script>
@endsection

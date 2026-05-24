@extends('layouts.app')

@section('title', 'Manage ' . $hotel->name)

@section('content')
<div class="admin-page-shell animate-fade-in">
    <div class="glass-panel admin-page-header">
        <div>
            <h1 class="admin-page-title">{{ $hotel->name }}</h1>
            <p class="admin-page-subtitle">{{ $hotel->address }} · {{ $hotel->phone }} · {{ $hotel->email }}</p>
        </div>
        <div class="admin-card-actions" style="margin-top:0;">
            <a href="{{ route('super_admin.hotels') }}" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Hotels</a>
            <form action="{{ route('super_admin.hotels.select') }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-location-crosshairs"></i> Use As Context</button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div class="glass-panel" style="padding:1.25rem;"><span class="admin-page-subtitle">Room Types</span><h2 style="margin:.35rem 0 0;color:var(--text-primary);">{{ $roomTypes->count() }}</h2></div>
        <div class="glass-panel" style="padding:1.25rem;"><span class="admin-page-subtitle">Rooms</span><h2 style="margin:.35rem 0 0;color:var(--text-primary);">{{ $rooms->total() }}</h2></div>
        <div class="glass-panel" style="padding:1.25rem;"><span class="admin-page-subtitle">Available</span><h2 style="margin:.35rem 0 0;color:var(--text-primary);">{{ $rooms->getCollection()->where('status', 'available')->count() }}</h2></div>
        <div class="glass-panel" style="padding:1.25rem;"><span class="admin-page-subtitle">Assigned Staff</span><h2 style="margin:.35rem 0 0;color:var(--text-primary);">{{ $staff->count() }}</h2></div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,420px);gap:1.5rem;align-items:start;">
        <div class="glass-panel">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap;margin-bottom:1rem;">
                <div>
                    <h2 style="margin:0;color:var(--text-primary);font-size:1.2rem;">Hotel Rooms</h2>
                    <p class="admin-page-subtitle" style="margin:.25rem 0 0;">Rooms listed here belong only to this hotel property.</p>
                </div>
            </div>

            @if($rooms->count())
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th style="width:230px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td><strong style="color:var(--text-primary);">{{ $room->room_number }}</strong></td>
                                    <td>{{ $room->roomType?->name ?? 'Missing room type' }}</td>
                                    <td><span class="status-pill {{ $room->status === 'available' ? 'active' : '' }}">{{ ucfirst($room->status) }}</span></td>
                                    <td>
                                        <div class="admin-card-actions" style="margin-top:0;">
                                            <form method="POST" action="{{ route('super_admin.hotels.rooms.status', [$hotel, $room->id]) }}" style="margin:0;display:flex;gap:.5rem;">
                                                @csrf
                                                <select name="status" class="form-control" style="min-width:125px;padding:.45rem .6rem;">
                                                    @foreach(['available', 'booked', 'maintenance', 'dirty'] as $status)
                                                        <option value="{{ $status }}" @selected($room->status === $status)>{{ ucfirst($status) }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-outline" type="submit"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <form method="POST" action="{{ route('super_admin.hotels.rooms.delete', [$hotel, $room->id]) }}" onsubmit="return confirm('Delete this room?')" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:1rem;">{{ $rooms->links() }}</div>
            @else
                <div class="empty-state">No rooms have been added to this hotel yet.</div>
            @endif
        </div>

        <div style="display:grid;gap:1.5rem;">
            <div class="glass-panel">
                <h2 style="margin:0 0 1rem;color:var(--text-primary);font-size:1.15rem;">Add Room Type</h2>
                <form method="POST" action="{{ route('super_admin.hotels.room_types.store', $hotel) }}">
                    @csrf
                    <div class="form-group"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                    <div class="form-group"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Base Price</label><input class="form-control" type="number" step="0.01" min="0" name="base_price" required></div>
                        <div class="form-group"><label class="form-label">Capacity</label><input class="form-control" type="number" min="1" name="capacity" required></div>
                    </div>
                    <div class="form-group"><label class="form-label">Amenities</label><input class="form-control" name="amenities" placeholder="WiFi, Breakfast, Balcony"></div>
                    <div class="form-group"><label class="form-label">Image URL</label><input class="form-control" type="url" name="image_url" placeholder="https://..."></div>
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-plus"></i> Add Room Type</button>
                </form>
            </div>

            <div class="glass-panel">
                <h2 style="margin:0 0 1rem;color:var(--text-primary);font-size:1.15rem;">Add Room</h2>
                @if($roomTypes->isEmpty())
                    <div class="empty-state">Create a room type for this hotel before adding rooms.</div>
                @else
                    <form method="POST" action="{{ route('super_admin.hotels.rooms.store', $hotel) }}">
                        @csrf
                        <div class="form-group"><label class="form-label">Room Number</label><input class="form-control" name="room_number" required></div>
                        <div class="form-group">
                            <label class="form-label">Room Type</label>
                            <select class="form-control" name="room_type_id" required>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="available">Available</option>
                                <option value="booked">Booked</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="dirty">Dirty</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-door-open"></i> Add Room</button>
                    </form>
                @endif
            </div>

            <div class="glass-panel">
                <h2 style="margin:0 0 1rem;color:var(--text-primary);font-size:1.15rem;">Assigned Staff</h2>
                @if($staff->count())
                    <div style="display:grid;gap:.75rem;margin-bottom:1rem;">
                        @foreach($staff as $member)
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:.75rem;border:1px solid var(--border-color);border-radius:8px;padding:.75rem;">
                                <div><strong style="color:var(--text-primary);">{{ $member->name }}</strong><br><span class="admin-page-subtitle">{{ str_replace('_', ' ', ucfirst($member->role)) }}</span></div>
                                <form method="POST" action="{{ route('super_admin.hotels.staff.remove', [$hotel, $member->id]) }}" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline" type="submit"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($availableStaff->count())
                    <form method="POST" action="{{ route('super_admin.hotels.staff.assign', $hotel) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Assign Staff</label>
                            <select class="form-control" name="user_id" required>
                                @foreach($availableStaff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} - {{ str_replace('_', ' ', ucfirst($member->role)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-user-plus"></i> Assign</button>
                    </form>
                @else
                    <p class="admin-page-subtitle">No unassigned staff are available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

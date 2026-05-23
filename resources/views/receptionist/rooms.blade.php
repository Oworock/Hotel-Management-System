@extends('layouts.app')

@section('title', 'Room Status & Housekeeping')

@section('content')
<div class="animate-fade-in">
    <!-- Filter Bar -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem;">
        <form action="{{ route('receptionist.rooms') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin: 0;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label for="status" class="form-label" style="font-size: 0.85rem; font-weight: 600;">Filter by Status</label>
                <select name="status" id="status" class="form-control form-select">
                    <option value="">All Rooms</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available (Clean)</option>
                    <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Occupied</option>
                    <option value="dirty" {{ request('status') === 'dirty' ? 'selected' : '' }}>Dirty</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 0.5rem; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->filled('status'))
                    <a href="{{ route('receptionist.rooms') }}" class="btn btn-outline" style="padding: 0.6rem 1.5rem; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Rooms Visual Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        @forelse($rooms as $room)
            <div class="glass-panel" style="padding: 1.5rem; border-top: 5px solid 
                @if($room->status === 'available') var(--success)
                @elseif($room->status === 'booked') var(--primary)
                @elseif($room->status === 'dirty') var(--warning)
                @else var(--danger)
                @endif; display: flex; flex-direction: column; gap: 1rem; height: 100%;">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-primary);">
                            Room #{{ $room->room_number }}
                        </h4>
                        <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">
                            {{ $room->roomType->name }}
                        </span>
                    </div>
                    <div>
                        @switch($room->status)
                            @case('available')
                                <span class="badge badge-success">Available</span>
                                @break
                            @case('booked')
                                <span class="badge badge-primary">Occupied</span>
                                @break
                            @case('dirty')
                                <span class="badge badge-warning">Dirty</span>
                                @break
                            @case('maintenance')
                                <span class="badge badge-danger">Maintenance</span>
                                @break
                        @endswitch
                    </div>
                </div>

                <div style="font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.25rem;">
                    <span>Capacity: {{ $room->roomType->capacity }} Guests</span>
                    <span>Rate: ${{ number_format($room->roomType->base_price, 2) }} / night</span>
                </div>

                <!-- Housekeeping Action Form -->
                <form action="{{ route('receptionist.rooms.status', $room->id) }}" method="POST" style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 0.75rem; display: flex; gap: 0.5rem; align-items: center;">
                    @csrf
                    <select name="status" class="form-control form-select" style="font-size: 0.8rem; padding: 0.35rem 0.5rem; height: auto;" required>
                        <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available (Clean)</option>
                        <option value="booked" {{ $room->status === 'booked' ? 'selected' : '' }}>Occupied</option>
                        <option value="dirty" {{ $room->status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                        <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        Update
                    </button>
                </form>
            </div>
        @empty
            <div class="glass-panel" style="grid-column: 1 / -1; padding: 3rem; text-align: center; color: var(--text-muted);">
                <i class="fa-solid fa-door-closed" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                No rooms found.
            </div>
        @endforelse
    </div>
</div>
@endsection

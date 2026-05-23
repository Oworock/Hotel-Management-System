@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;" class="animate-fade-in">
    <!-- Left Column: Attendance Shift Clocking -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="glass-panel clock-widget">
            <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-business-time" style="color: var(--primary);"></i> Attendance Shift</h3>
            
            <!-- Live Clock -->
            <div class="clock-timer" id="live-clock">00:00:00</div>
            <div id="live-date" style="color: var(--text-secondary); font-size: 0.9rem; margin-top: -0.5rem; margin-bottom: 1.5rem; font-weight: 500;">
                Thursday, May 21
            </div>

            <!-- Clock Status Widget -->
            @if($activeShift)
                <div class="clock-status" style="color: var(--success);">
                    <i class="fa-solid fa-circle-play" style="animation: pulseGlow 1.5s infinite; border-radius: 50%; padding: 2px;"></i> Active Shift: Clocked In Since {{ $activeShift->clock_in_at->format('h:i A') }}
                </div>
                <div class="clock-actions">
                    <form action="{{ route('staff.clock_out') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="background: linear-gradient(135deg, var(--danger), hsl(355, 80%, 65%)); box-shadow: 0 4px 14px var(--danger-glow);">
                            <i class="fa-solid fa-circle-stop"></i> Clock Out Shift
                        </button>
                    </form>
                </div>
            @else
                <div class="clock-status" style="color: var(--text-secondary);">
                    <i class="fa-solid fa-circle-minus"></i> Status: Off-Duty (Clocked Out)
                </div>
                <div class="clock-actions">
                    <form action="{{ route('staff.clock_in') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-circle-play"></i> Clock In Shift
                        </button>
                    </form>
                </div>
            @endif

            <!-- Recent Shifts list -->
            <div class="clock-history">
                <h4 style="font-size: 0.95rem; margin-bottom: 1rem; border-bottom: 1px dashed var(--border-color); padding-bottom: 0.5rem;">Recent Shift Activity</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @forelse($recentShifts as $shift)
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                            <div>
                                <span style="font-weight: 600;">{{ $shift->clock_in_at->format('M d') }}</span>
                                <span style="color: var(--text-secondary);">
                                    ({{ $shift->clock_in_at->format('h:i A') }} - 
                                    {{ $shift->clock_out_at ? $shift->clock_out_at->format('h:i A') : 'Ongoing' }})
                                </span>
                            </div>
                            <span style="font-weight: 700; color: var(--primary);">
                                @if($shift->clock_out_at)
                                    @php
                                        $h = floor($shift->duration_minutes / 60);
                                        $m = $shift->duration_minutes % 60;
                                    @endphp
                                    {{ sprintf('%dh %02dm', $h, $m) }}
                                @else
                                    <span class="badge badge-primary">Active</span>
                                @endif
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">No shifts recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Housekeeping Rooms Status -->
    <div class="glass-panel" style="display: flex; flex-direction: column;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-spray-can-sparkles" style="color: var(--primary);"></i> Housekeeping & Room Status</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem;">
            @foreach($rooms as $room)
                <div class="glass-panel" style="padding: 1rem; text-align: center; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background-color: var(--surface);">
                    <div style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--primary); margin-bottom: 0.25rem;">
                        #{{ $room->room_number }}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem; font-weight: 600;">
                        {{ $room->roomType->name }}
                    </div>
                    
                    <form action="{{ route('staff.rooms.housekeeping', $room->id) }}" method="POST" id="housekeep-form-{{ $room->id }}">
                        @csrf
                        <select name="status" class="form-control" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; text-align: center; border-radius: 4px;" onchange="document.getElementById('housekeep-form-{{ $room->id }}').submit()">
                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="booked" {{ $room->status === 'booked' ? 'selected' : '' }}>Booked</option>
                            <option value="dirty" {{ $room->status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                            <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </form>
                    
                    <div style="margin-top: 0.75rem;">
                        @switch($room->status)
                            @case('available')
                                <span class="badge badge-success" style="font-size:0.65rem; padding: 0.1rem 0.4rem;">Clean</span>
                                @break
                            @case('booked')
                                <span class="badge badge-info" style="font-size:0.65rem; padding: 0.1rem 0.4rem;">Occupied</span>
                                @break
                            @case('dirty')
                                <span class="badge badge-warning" style="font-size:0.65rem; padding: 0.1rem 0.4rem;">Dirty</span>
                                @break
                            @case('maintenance')
                                <span class="badge badge-danger" style="font-size:0.65rem; padding: 0.1rem 0.4rem;">Repair</span>
                                @break
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('live-clock').innerText = `${hrs}:${mins}:${secs}`;
        
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('live-date').innerText = now.toLocaleDateString('en-US', options);
    }
    
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection

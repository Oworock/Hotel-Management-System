@extends('layouts.app')

@section('title', 'Explore Rooms')

@section('content')
<!-- Date Search Bar -->
<div class="glass-panel animate-fade-in" style="margin-bottom: 2rem;">
    <h3 style="font-size: 1.1rem; margin-bottom: 1.25rem;"><i class="fa-solid fa-magnifying-glass" style="color: var(--primary);"></i> Find Available Rooms</h3>
    <form action="{{ route('customer.dashboard') }}" method="GET">
        <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin: 0;">
                <label for="check_in_date" class="form-label">Check-In Date</label>
                <input type="date" name="check_in_date" id="check_in_date" class="form-control" value="{{ $checkIn }}" min="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group" style="margin: 0;">
                <label for="check_out_date" class="form-label">Check-Out Date</label>
                <input type="date" name="check_out_date" id="check_out_date" class="form-control" value="{{ $checkOut }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 2rem;">
                Check Availability
            </button>
        </div>
    </form>
</div>

<!-- Availability Feedback Banner -->
@if($searched)
    <div class="alert alert-info animate-fade-in" style="margin-bottom: 2rem;">
        <i class="fa-solid fa-circle-info"></i>
        <span>Showing room availability for a stay of <strong>{{ $nights }}</strong> {{ Str::plural('night', $nights) }} ({{ date('M d, Y', strtotime($checkIn)) }} to {{ date('M d, Y', strtotime($checkOut)) }}).</span>
    </div>
@endif

<!-- Room Type Catalog -->
<div class="room-grid animate-fade-in">
    @foreach($roomTypes as $type)
        <div class="glass-panel room-card">
            <!-- Simulated image placeholder with high-end matching gradients -->
            <div class="room-card-img" style="background: linear-gradient(135deg, var(--primary-glow) 0%, var(--secondary-glow) 100%); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                @if($type->id === 1)
                    <i class="fa-solid fa-bed" style="font-size: 4rem; opacity: 0.6;"></i>
                @elseif($type->id === 2)
                    <i class="fa-solid fa-tree" style="font-size: 4rem; opacity: 0.6;"></i>
                @else
                    <i class="fa-solid fa-water" style="font-size: 4rem; opacity: 0.6;"></i>
                @endif
                
                <div class="room-card-badge">
                    @if($type->available_count > 0)
                        <span class="badge badge-success">{{ $type->available_count }} Left</span>
                    @else
                        <span class="badge badge-danger">Sold Out</span>
                    @endif
                </div>
            </div>
            
            <div class="room-card-content">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h3 style="font-size: 1.25rem; color: var(--text-primary);">{{ $type->name }}</h3>
                    <div class="room-card-price">
                        ${{ number_format($type->base_price, 0) }}<span>/night</span>
                    </div>
                </div>
                
                <p style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5; flex: 1;">
                    {{ $type->description }}
                </p>
                
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                    <i class="fa-solid fa-users" style="margin-right: 0.25rem;"></i> Up to {{ $type->capacity }} Guest{{ $type->capacity > 1 ? 's' : '' }}
                </div>
                
                <div class="room-amenities">
                    @if($type->amenities)
                        @foreach(array_slice($type->amenities, 0, 4) as $amenity)
                            <span class="room-amenity-tag">{{ $amenity }}</span>
                        @endforeach
                        @if(count($type->amenities) > 4)
                            <span class="room-amenity-tag" style="background-color: var(--primary-glow); color: var(--primary);">+{{ count($type->amenities) - 4 }} More</span>
                        @endif
                    @endif
                </div>
                
                <div style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    @if($type->available_count > 0)
                        <a href="{{ route('customer.book', ['roomType' => $type->id, 'check_in_date' => $checkIn, 'check_out_date' => $checkOut]) }}" class="btn btn-primary btn-block">
                            Book Now <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem;"></i>
                        </a>
                    @else
                        <button class="btn btn-outline btn-block" disabled style="cursor: not-allowed; opacity: 0.5;">
                            Unavailable
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    // Auto sync minimum check out date to check in + 1 day
    const checkInInput = document.getElementById('check_in_date');
    const checkOutInput = document.getElementById('check_out_date');

    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        if (checkInDate) {
            checkInDate.setDate(checkInDate.getDate() + 1);
            const yyyy = checkInDate.getFullYear();
            const mm = String(checkInDate.getMonth() + 1).padStart(2, '0');
            const dd = String(checkInDate.getDate()).padStart(2, '0');
            
            checkOutInput.min = `${yyyy}-${mm}-${dd}`;
            if (checkOutInput.value <= this.value) {
                checkOutInput.value = `${yyyy}-${mm}-${dd}`;
            }
        }
    });
</script>
@endsection

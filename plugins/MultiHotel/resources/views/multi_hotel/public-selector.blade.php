@php
    $hotels = \Plugins\MultiHotel\Models\Hotel::where('is_active', true)->orderBy('name')->get();
    $activeHotelId = session('active_hotel_id');
@endphp

@if($hotels->count() > 1 && Route::has('hotels.select_public'))
    <form action="{{ route('hotels.select_public') }}" method="POST" class="public-hotel-selector" style="display:inline-flex;align-items:center;">
        @csrf
        <label class="sr-only" for="public_hotel_id">Hotel location</label>
        <select name="hotel_id" id="public_hotel_id" onchange="this.form.submit()" style="min-width:150px;border:1px solid var(--border-color);background:var(--surface);color:var(--text-primary);border-radius:999px;padding:0.45rem 0.75rem;font:inherit;font-size:0.85rem;">
            <option value="">All Locations</option>
            @foreach($hotels as $hotel)
                <option value="{{ $hotel->id }}" {{ (string)$activeHotelId === (string)$hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
            @endforeach
        </select>
    </form>
@endif

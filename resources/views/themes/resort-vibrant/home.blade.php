@extends('layouts.frontend')

@section('title', 'Home')

@section('styles')
<style>
    .theme-resort {
        background: #e9fff8;
        color: #053d34;
    }

    .theme-resort .wrap {
        width: min(1200px, 100%);
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .resort-hero {
        min-height: 86vh;
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: #008f9f;
    }

    .resort-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 800ms ease;
        background-size: cover;
        background-position: center;
    }

    .resort-slide.active {
        opacity: 1;
    }

    .resort-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0, 95, 91, 0.86), rgba(14, 165, 233, 0.42));
    }

    .resort-hero-content {
        position: relative;
        z-index: 1;
        color: white;
        padding: 6rem 0 5rem;
    }

    .resort-hero h1 {
        color: white;
        font-size: clamp(3rem, 8vw, 7rem);
        line-height: 0.95;
        max-width: 860px;
        margin: 0 0 1.25rem;
    }

    .resort-hero p {
        max-width: 680px;
        color: rgba(255, 255, 255, 0.86);
        font-size: 1.18rem;
        line-height: 1.75;
    }

    .resort-search-shell {
        margin-top: -4rem;
        position: relative;
        z-index: 2;
    }

    .resort-search {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        box-shadow: 0 22px 60px rgba(5, 61, 52, 0.18);
    }

    .resort-search label {
        display: block;
        color: #067266;
        font-size: 0.8rem;
        font-weight: 900;
        margin-bottom: 0.35rem;
    }

    .resort-search input,
    .resort-search select {
        width: 100%;
        min-height: 44px;
        border-radius: 8px;
        border: 1px solid #b7eadf;
        padding: 0 0.8rem;
        color: #053d34;
    }

    .resort-search button,
    .resort-btn {
        min-height: 44px;
        border: 0;
        border-radius: 8px;
        background: var(--secondary);
        color: white;
        font-weight: 900;
        cursor: pointer;
        padding: 0 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .resort-availability {
        padding: 6rem 0 4rem;
    }

    .resort-topline {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .resort-topline h2 {
        color: #053d34;
        font-size: clamp(2.3rem, 5vw, 4.6rem);
        line-height: 1;
        margin: 0;
    }

    .resort-room-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 1rem;
    }

    .resort-room {
        min-height: 260px;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        display: grid;
        grid-template-columns: 1fr 1fr;
        box-shadow: 0 18px 45px rgba(5, 61, 52, 0.12);
    }

    .resort-room:first-child {
        grid-row: span 2;
        grid-template-columns: 1fr;
    }

    .resort-room-image {
        min-height: 190px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .resort-room-body {
        padding: 1.4rem;
    }

    .resort-room h3 {
        color: #053d34;
        margin: 0 0 0.6rem;
    }

    .resort-room p {
        color: #47746c;
        line-height: 1.6;
    }

    .resort-price {
        color: var(--secondary);
        font-weight: 900;
        font-size: 1.45rem;
    }

    .resort-experience {
        padding: 5rem 0;
        background: #053d34;
        color: white;
    }

    .resort-experience-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }

    .resort-experience-card {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .resort-experience-card h3 {
        color: white;
    }

    @media (max-width: 900px) {
        .resort-search,
        .resort-room-grid,
        .resort-room,
        .resort-experience-grid {
            grid-template-columns: 1fr;
        }

        .resort-topline {
            align-items: start;
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
@php
    $fallbackSlides = [
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1600&q=80',
    ];
@endphp
<div class="theme-resort">
    <section class="resort-hero">
        @forelse($slides as $slide)
            <div class="resort-slide {{ $loop->first ? 'active' : '' }}" style="background-image: url('{{ $slide->image_path }}');"></div>
        @empty
            @foreach($fallbackSlides as $image)
                <div class="resort-slide {{ $loop->first ? 'active' : '' }}" style="background-image: url('{{ $image }}');"></div>
            @endforeach
        @endforelse
        <div class="wrap resort-hero-content">
            <p style="font-weight: 900; text-transform: uppercase;">{{ $hotelName }}</p>
            <h1>{{ $heroTitle }}</h1>
            <p>{{ $heroSubtitle }}</p>
            <a class="resort-btn" href="{{ route('rooms') }}" style="margin-top: 1rem; background: white; color: #053d34;">Start Booking</a>
        </div>
    </section>

    <div class="resort-search-shell">
        <div class="wrap">
            <form class="resort-search" method="GET" action="{{ route('home') }}">
                <div><label>Check-in</label><input type="date" name="check_in_date" value="{{ $checkIn }}"></div>
                <div><label>Check-out</label><input type="date" name="check_out_date" value="{{ $checkOut }}"></div>
                <div>
                    <label>Guests</label>
                    <select name="guests">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ (int) $guestsCount === $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div style="display:flex;align-items:end;"><button type="submit" style="width:100%;">Search</button></div>
            </form>
        </div>
    </div>

    <section class="resort-availability">
        <div class="wrap">
            <div class="resort-topline">
                <div>
                    <p style="font-weight:900;color:var(--secondary);text-transform:uppercase;">Available Now</p>
                    <h2>Rooms close to the water, energy close to everything.</h2>
                </div>
                <p style="max-width: 380px; color: #47746c; line-height: 1.7;">{{ $welcomeDescription }}</p>
            </div>

            <div class="resort-room-grid">
                @forelse($roomTypes->take(5) as $roomType)
                    @php
                        $roomImage = collect($roomType->images ?? [])->first();
                        $roomImageUrl = $roomImage
                            ? (\Illuminate\Support\Str::startsWith($roomImage, ['http://', 'https://', '/']) ? $roomImage : asset('storage/' . $roomImage))
                            : 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="resort-room">
                        <div class="resort-room-image" style="background-image: url('{{ $roomImageUrl }}'); background-size: cover; background-position: center;"></div>
                        <div class="resort-room-body">
                            <h3>{{ $roomType->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($roomType->description, 110) }}</p>
                            <p>{{ $roomType->available_count }} available</p>
                            <div class="resort-price">{{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</div>
                        </div>
                    </article>
                @empty
                    <p>No rooms available at the moment.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="resort-experience">
        <div class="wrap resort-experience-grid">
            <article class="resort-experience-card"><h3>Beach Hours</h3><p>Open-air relaxation and poolside service.</p></article>
            <article class="resort-experience-card"><h3>Family Ready</h3><p>Rooms arranged for groups, couples, and longer stays.</p></article>
            <article class="resort-experience-card"><h3>Local Flavor</h3><p>Bright dining, fast check-in, and easy support.</p></article>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    const resortSlides = document.querySelectorAll('.resort-slide');
    if (resortSlides.length > 1) {
        let currentResortSlide = 0;
        setInterval(() => {
            resortSlides[currentResortSlide].classList.remove('active');
            currentResortSlide = (currentResortSlide + 1) % resortSlides.length;
            resortSlides[currentResortSlide].classList.add('active');
        }, 3800);
    }
</script>
@endsection

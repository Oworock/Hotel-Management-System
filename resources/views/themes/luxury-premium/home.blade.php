@extends('layouts.frontend')

@section('title', 'Home')

@section('styles')
<style>
    .theme-luxury {
        background: #11100d;
        color: #f8f3e7;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .theme-luxury .wrap {
        width: min(1180px, 100%);
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .luxury-hero {
        min-height: 88vh;
        position: relative;
        display: flex;
        align-items: stretch;
        overflow: hidden;
        background: #11100d;
    }

    .luxury-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 900ms ease;
        background-size: cover;
        background-position: center;
    }

    .luxury-slide.active {
        opacity: 1;
    }

    .luxury-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(17, 16, 13, 0.9), rgba(17, 16, 13, 0.55), rgba(17, 16, 13, 0.18));
    }

    .luxury-hero-content {
        position: relative;
        z-index: 1;
        width: min(1180px, 100%);
        margin: 0 auto;
        padding: 7rem 1.5rem 5rem;
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) 360px;
        gap: 3rem;
        align-items: end;
    }

    .luxury-kicker {
        color: #d8b66d;
        text-transform: uppercase;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        margin-bottom: 1rem;
    }

    .theme-luxury h1,
    .theme-luxury h2,
    .theme-luxury h3 {
        font-family: Georgia, 'Times New Roman', serif;
        color: #fff8eb;
        letter-spacing: 0;
    }

    .luxury-hero h1 {
        font-size: clamp(3rem, 7vw, 6.8rem);
        line-height: 0.92;
        max-width: 820px;
        margin: 0 0 1.25rem;
    }

    .luxury-hero p {
        color: rgba(255, 248, 235, 0.78);
        font-size: 1.15rem;
        line-height: 1.8;
        max-width: 680px;
        margin-bottom: 2rem;
    }

    .luxury-booking {
        background: rgba(255, 248, 235, 0.94);
        color: #1f1b14;
        border: 1px solid rgba(216, 182, 109, 0.45);
        padding: 1.2rem;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.38);
    }

    .luxury-booking h3 {
        color: #1f1b14;
        margin: 0 0 1rem;
        font-size: 1.25rem;
    }

    .luxury-booking label {
        display: block;
        font-size: 0.78rem;
        color: #5a4b2e;
        font-weight: 800;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
    }

    .luxury-booking input,
    .luxury-booking select {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbb178;
        background: #fffaf0;
        color: #1f1b14;
        padding: 0 0.75rem;
        margin-bottom: 0.85rem;
    }

    .luxury-booking button,
    .luxury-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 44px;
        border: 1px solid #d8b66d;
        background: #d8b66d;
        color: #17130d;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        text-decoration: none;
    }

    .luxury-intro {
        padding: 6rem 0;
        background: #fff8eb;
        color: #1f1b14;
    }

    .luxury-intro-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .luxury-intro h2 {
        color: #1f1b14;
        font-size: clamp(2.4rem, 5vw, 4.5rem);
        line-height: 1;
        margin: 0;
    }

    .luxury-intro p {
        color: #5d5140;
        line-height: 1.85;
        font-size: 1.05rem;
    }

    .luxury-rooms {
        padding: 6rem 0;
        background: #15120d;
    }

    .luxury-room-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-top: 2rem;
    }

    .luxury-room {
        border: 1px solid rgba(216, 182, 109, 0.35);
        background: rgba(255, 248, 235, 0.05);
        min-height: 460px;
        display: flex;
        flex-direction: column;
    }

    .luxury-room:nth-child(2) {
        margin-top: 2.5rem;
    }

    .luxury-room-image {
        height: 220px;
        background: linear-gradient(135deg, #d8b66d, #342b18);
    }

    .luxury-room-body {
        padding: 1.5rem;
        display: flex;
        flex: 1;
        flex-direction: column;
        gap: 1rem;
    }

    .luxury-room p {
        color: rgba(255, 248, 235, 0.68);
        line-height: 1.65;
    }

    .luxury-price {
        color: #d8b66d;
        font-size: 1.8rem;
        font-weight: 900;
        margin-top: auto;
    }

    @media (max-width: 900px) {
        .luxury-hero-content,
        .luxury-intro-grid,
        .luxury-room-grid {
            grid-template-columns: 1fr;
        }

        .luxury-room:nth-child(2) {
            margin-top: 0;
        }
    }
</style>
@endsection

@section('content')
@php
    $fallbackSlides = [
        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1600&q=80',
    ];
@endphp
<div class="theme-luxury">
    <section class="luxury-hero">
        @forelse($slides as $slide)
            <div class="luxury-slide {{ $loop->first ? 'active' : '' }}" style="background-image: url('{{ $slide->image_path }}');"></div>
        @empty
            @foreach($fallbackSlides as $image)
                <div class="luxury-slide {{ $loop->first ? 'active' : '' }}" style="background-image: url('{{ $image }}');"></div>
            @endforeach
        @endforelse

        <div class="luxury-hero-content">
            <div>
                <div class="luxury-kicker">{{ $hotelName }}</div>
                <h1>{{ $heroTitle }}</h1>
                <p>{{ $heroSubtitle }}</p>
                <a href="{{ route('rooms') }}" class="luxury-btn" style="width: auto; padding: 0 1.4rem;">Reserve Your Suite</a>
            </div>
            <form class="luxury-booking" method="GET" action="{{ route('home') }}">
                <h3>Private Reservation</h3>
                <label>Arrival</label>
                <input type="date" name="check_in_date" value="{{ $checkIn }}">
                <label>Departure</label>
                <input type="date" name="check_out_date" value="{{ $checkOut }}">
                <label>Guests</label>
                <select name="guests">
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ (int) $guestsCount === $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
                <button type="submit">Check Suites</button>
            </form>
        </div>
    </section>

    <section class="luxury-intro">
        <div class="wrap luxury-intro-grid">
            <h2>{{ $welcomeTitle }}</h2>
            <p>{{ $welcomeDescription }}</p>
        </div>
    </section>

    <section class="luxury-rooms">
        <div class="wrap">
            <div class="luxury-kicker">Signature Accommodation</div>
            <h2 style="font-size: clamp(2.5rem, 5vw, 4.8rem); margin: 0;">Rooms With Ceremony</h2>
            <div class="luxury-room-grid">
                @forelse($roomTypes->take(3) as $roomType)
                    @php
                        $roomImage = collect($roomType->images ?? [])->first();
                        $roomImageUrl = $roomImage
                            ? (\Illuminate\Support\Str::startsWith($roomImage, ['http://', 'https://', '/']) ? $roomImage : asset('storage/' . $roomImage))
                            : 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="luxury-room">
                        <div class="luxury-room-image" style="background-image: url('{{ $roomImageUrl }}'); background-size: cover; background-position: center;"></div>
                        <div class="luxury-room-body">
                            <h3>{{ $roomType->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($roomType->description, 140) }}</p>
                            <small>{{ $roomType->available_count }} rooms available</small>
                            <div class="luxury-price">{{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</div>
                        </div>
                    </article>
                @empty
                    <p>No rooms available at the moment.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.luxury-slide').forEach((slide, index, slides) => {
        if (slides.length < 2 || index !== 0) return;
        let current = 0;
        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 4500);
    });
</script>
@endsection

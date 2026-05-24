@extends('layouts.frontend')

@section('title', 'Home')

@section('styles')
<style>
    .theme-boutique {
        background: #fff7f0;
        color: #38251c;
    }

    .theme-boutique .wrap {
        width: min(1160px, 100%);
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .boutique-hero {
        padding: 5rem 0 3rem;
        background:
            linear-gradient(90deg, rgba(255, 247, 240, 0.97), rgba(255, 247, 240, 0.72)),
            url('https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1600&q=80') center/cover;
    }

    .boutique-hero-grid {
        min-height: 72vh;
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        gap: 2rem;
        align-items: center;
    }

    .theme-boutique h1,
    .theme-boutique h2,
    .theme-boutique h3 {
        color: #38251c;
        letter-spacing: 0;
    }

    .boutique-hero h1 {
        font-size: clamp(2.8rem, 7vw, 6.4rem);
        line-height: 0.98;
        margin: 0 0 1rem;
    }

    .boutique-copy {
        color: #6f4e3d;
        font-size: 1.1rem;
        line-height: 1.8;
        max-width: 620px;
    }

    .boutique-panel {
        background: #ffffff;
        border: 1px solid rgba(111, 78, 61, 0.16);
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 22px 60px rgba(111, 78, 61, 0.14);
    }

    .boutique-search {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .boutique-search label {
        display: block;
        margin-bottom: 0.35rem;
        color: #6f4e3d;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .boutique-search input,
    .boutique-search select {
        width: 100%;
        min-height: 44px;
        border: 1px solid rgba(111, 78, 61, 0.22);
        border-radius: 8px;
        padding: 0 0.8rem;
        background: #fffaf6;
        color: #38251c;
    }

    .boutique-search button,
    .boutique-btn {
        border: 0;
        background: var(--primary);
        color: white;
        border-radius: 8px;
        min-height: 44px;
        padding: 0 1rem;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .boutique-story {
        padding: 5rem 0;
    }

    .boutique-story-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        align-items: stretch;
    }

    .boutique-story-card {
        min-height: 260px;
        border-radius: 8px;
        padding: 1.5rem;
        background: #ffffff;
        border: 1px solid rgba(111, 78, 61, 0.14);
    }

    .boutique-story-card.feature {
        grid-column: span 2;
        background:
            linear-gradient(rgba(56, 37, 28, 0.28), rgba(56, 37, 28, 0.38)),
            url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80') center/cover;
        color: white;
        display: flex;
        align-items: end;
    }

    .boutique-story-card.feature h2 {
        color: white;
        font-size: clamp(2rem, 4vw, 4rem);
        max-width: 640px;
    }

    .boutique-rooms {
        padding: 5rem 0;
        background: #f5e6dc;
    }

    .boutique-room-layout {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 2rem;
        align-items: start;
    }

    .boutique-room-stack {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .boutique-room {
        background: #fffaf6;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(111, 78, 61, 0.15);
    }

    .boutique-room:nth-child(even) {
        transform: translateY(2rem);
    }

    .boutique-room-image {
        height: 180px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .boutique-room-body {
        padding: 1.25rem;
    }

    .boutique-price {
        color: var(--primary);
        font-size: 1.5rem;
        font-weight: 900;
    }

    @media (max-width: 900px) {
        .boutique-hero-grid,
        .boutique-story-grid,
        .boutique-room-layout,
        .boutique-room-stack,
        .boutique-search {
            grid-template-columns: 1fr;
        }

        .boutique-story-card.feature {
            grid-column: auto;
        }

        .boutique-room:nth-child(even) {
            transform: none;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-boutique">
    <section class="boutique-hero">
        <div class="wrap boutique-hero-grid">
            <div>
                <p style="font-weight: 900; color: var(--primary); text-transform: uppercase;">{{ $hotelName }}</p>
                <h1>{{ $heroTitle }}</h1>
                <p class="boutique-copy">{{ $heroSubtitle }}</p>
                <a href="{{ route('rooms') }}" class="boutique-btn" style="margin-top: 1rem;">See Rooms</a>
            </div>
            <form class="boutique-panel boutique-search" method="GET" action="{{ route('home') }}">
                <div>
                    <label>Check-in</label>
                    <input type="date" name="check_in_date" value="{{ $checkIn }}">
                </div>
                <div>
                    <label>Check-out</label>
                    <input type="date" name="check_out_date" value="{{ $checkOut }}">
                </div>
                <div>
                    <label>Guests</label>
                    <select name="guests">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ (int) $guestsCount === $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" style="width: 100%;">Find My Stay</button>
                </div>
            </form>
        </div>
    </section>

    <section class="boutique-story">
        <div class="wrap boutique-story-grid">
            <article class="boutique-story-card feature">
                <h2>{{ $welcomeTitle }}</h2>
            </article>
            <article class="boutique-story-card">
                <h3>Made Personal</h3>
                <p class="boutique-copy">{{ $welcomeDescription }}</p>
            </article>
            <article class="boutique-story-card">
                <h3>Slow Mornings</h3>
                <p class="boutique-copy">Breakfast, quiet corners, and service that remembers the details.</p>
            </article>
            <article class="boutique-story-card">
                <h3>Local Texture</h3>
                <p class="boutique-copy">Designed for guests who prefer a stay with personality.</p>
            </article>
            <article class="boutique-story-card">
                <h3>Easy Access</h3>
                <p class="boutique-copy">{{ $contactPhone }}<br>{{ $contactEmail }}</p>
            </article>
        </div>
    </section>

    <section class="boutique-rooms">
        <div class="wrap boutique-room-layout">
            <div>
                <p style="font-weight: 900; color: var(--primary); text-transform: uppercase;">Rooms First</p>
                <h2 style="font-size: clamp(2rem, 4vw, 3.8rem); margin: 0 0 1rem;">Pick a room with a point of view.</h2>
                <p class="boutique-copy">This theme moves rooms into a more editorial, staggered presentation instead of a standard card grid.</p>
            </div>
            <div class="boutique-room-stack">
                @forelse($roomTypes as $roomType)
                    @php
                        $roomImage = collect($roomType->images ?? [])->first();
                        $roomImageUrl = $roomImage
                            ? (\Illuminate\Support\Str::startsWith($roomImage, ['http://', 'https://', '/']) ? $roomImage : asset('storage/' . $roomImage))
                            : 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="boutique-room">
                        <div class="boutique-room-image" style="background-image: url('{{ $roomImageUrl }}'); background-size: cover; background-position: center;"></div>
                        <div class="boutique-room-body">
                            <h3>{{ $roomType->name }}</h3>
                            <p class="boutique-copy">{{ \Illuminate\Support\Str::limit($roomType->description, 95) }}</p>
                            <div class="boutique-price">{{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</div>
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

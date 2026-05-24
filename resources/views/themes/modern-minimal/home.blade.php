@extends('layouts.frontend')

@section('title', 'Home')

@section('styles')
<style>
    .theme-modern {
        --theme-primary: var(--primary);
        --theme-secondary: var(--secondary);
        background: #f7f9fc;
        color: #101828;
    }

    .theme-modern section {
        padding: 5rem 1.5rem;
    }

    .theme-modern .wrap {
        width: min(1180px, 100%);
        margin: 0 auto;
    }

    .modern-hero {
        min-height: 78vh;
        display: grid;
        align-items: center;
        background: linear-gradient(135deg, #ffffff 0%, color-mix(in srgb, var(--theme-primary) 10%, white) 100%);
        border-bottom: 1px solid rgba(16, 24, 40, 0.08);
    }

    .modern-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        gap: 3rem;
        align-items: center;
    }

    .modern-eyebrow {
        color: var(--theme-primary);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0;
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }

    .modern-hero h1 {
        color: #101828;
        font-size: clamp(2.5rem, 6vw, 5.75rem);
        line-height: 0.96;
        margin: 0 0 1.25rem;
    }

    .modern-hero p {
        color: #475467;
        font-size: 1.1rem;
        line-height: 1.8;
        max-width: 620px;
        margin-bottom: 2rem;
    }

    .modern-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0.85rem 1.2rem;
        border-radius: 8px;
        font-weight: 800;
        text-decoration: none;
    }

    .modern-btn.primary {
        color: white;
        background: var(--theme-primary);
    }

    .modern-btn.light {
        color: #101828;
        background: white;
        border: 1px solid #d0d5dd;
    }

    .modern-photo {
        min-height: 500px;
        border-radius: 8px;
        background:
            linear-gradient(180deg, rgba(16, 24, 40, 0.04), rgba(16, 24, 40, 0.24)),
            url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80') center/cover;
        box-shadow: 0 24px 70px rgba(16, 24, 40, 0.18);
        position: relative;
        overflow: hidden;
    }

    .modern-photo-card {
        position: absolute;
        left: 1.25rem;
        right: 1.25rem;
        bottom: 1.25rem;
        background: rgba(255, 255, 255, 0.92);
        border-radius: 8px;
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .modern-photo-card strong {
        display: block;
        color: #101828;
        font-size: 1.2rem;
    }

    .modern-photo-card span {
        color: #667085;
        font-size: 0.8rem;
    }

    .modern-search {
        margin-top: -3rem;
        position: relative;
        z-index: 2;
        padding: 0 1.5rem;
    }

    .modern-search form {
        width: min(1080px, 100%);
        margin: 0 auto;
        background: white;
        border: 1px solid #eaecf0;
        box-shadow: 0 18px 50px rgba(16, 24, 40, 0.12);
        border-radius: 8px;
        padding: 1.25rem;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .modern-search label {
        display: block;
        color: #344054;
        font-size: 0.8rem;
        font-weight: 800;
        margin-bottom: 0.45rem;
    }

    .modern-search input,
    .modern-search select {
        width: 100%;
        min-height: 44px;
        border-radius: 8px;
        border: 1px solid #d0d5dd;
        padding: 0 0.85rem;
        color: #101828;
        background: #fff;
    }

    .modern-search button {
        width: 100%;
        min-height: 44px;
        border: 0;
        border-radius: 8px;
        background: var(--theme-primary);
        color: #fff;
        font-weight: 800;
        cursor: pointer;
    }

    .modern-split {
        display: grid;
        grid-template-columns: 0.75fr 1.25fr;
        gap: 2rem;
        align-items: start;
    }

    .modern-section-title {
        font-size: clamp(2rem, 4vw, 3.4rem);
        color: #101828;
        margin: 0 0 1rem;
    }

    .modern-muted {
        color: #667085;
        line-height: 1.7;
    }

    .modern-room-list {
        display: grid;
        gap: 1rem;
    }

    .modern-room {
        background: #fff;
        border: 1px solid #eaecf0;
        border-radius: 8px;
        padding: 1rem;
        display: grid;
        grid-template-columns: 160px 1fr auto;
        gap: 1rem;
        align-items: center;
    }

    .modern-room-image {
        height: 116px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary));
    }

    .modern-room h3 {
        color: #101828;
        margin: 0 0 0.4rem;
        font-size: 1.25rem;
    }

    .modern-price {
        color: var(--theme-primary);
        font-size: 1.5rem;
        font-weight: 900;
        text-align: right;
        white-space: nowrap;
    }

    .modern-feature-band {
        background: #101828;
        color: #fff;
    }

    .modern-feature-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        overflow: hidden;
    }

    .modern-feature {
        background: #101828;
        padding: 2rem;
    }

    .modern-feature i {
        color: color-mix(in srgb, var(--theme-primary) 50%, white);
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 900px) {
        .modern-hero-grid,
        .modern-split,
        .modern-search form,
        .modern-room {
            grid-template-columns: 1fr;
        }

        .modern-photo {
            min-height: 360px;
        }

        .modern-feature-grid {
            grid-template-columns: 1fr 1fr;
        }

        .modern-price {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-modern">
    <section class="modern-hero">
        <div class="wrap modern-hero-grid">
            <div>
                <div class="modern-eyebrow">{{ $hotelName }}</div>
                <h1>{{ $heroTitle }}</h1>
                <p>{{ $heroSubtitle }}</p>
                <div class="modern-actions">
                    <a class="modern-btn primary" href="{{ route('rooms') }}">Browse Rooms</a>
                    <a class="modern-btn light" href="{{ route('contact') }}">Contact Desk</a>
                </div>
            </div>
            <div class="modern-photo">
                <div class="modern-photo-card">
                    <div><strong>{{ $roomTypes->count() }}</strong><span>Room types</span></div>
                    <div><strong>{{ $roomTypes->sum('available_count') }}</strong><span>Available</span></div>
                    <div><strong>{{ $currency }}</strong><span>Currency</span></div>
                </div>
            </div>
        </div>
    </section>

    <div class="modern-search">
        <form method="GET" action="{{ route('home') }}">
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
                <button type="submit">Check Availability</button>
            </div>
        </form>
    </div>

    <section>
        <div class="wrap modern-split">
            <div>
                <h2 class="modern-section-title">{{ $welcomeTitle }}</h2>
                <p class="modern-muted">{{ $welcomeDescription }}</p>
            </div>
            <div class="modern-room-list">
                @forelse($roomTypes as $roomType)
                    @php
                        $roomImage = collect($roomType->images ?? [])->first();
                        $roomImageUrl = $roomImage
                            ? (\Illuminate\Support\Str::startsWith($roomImage, ['http://', 'https://', '/']) ? $roomImage : asset('storage/' . $roomImage))
                            : 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <article class="modern-room">
                        <div class="modern-room-image" style="background-image: url('{{ $roomImageUrl }}'); background-size: cover; background-position: center;"></div>
                        <div>
                            <h3>{{ $roomType->name }}</h3>
                            <p class="modern-muted">{{ \Illuminate\Support\Str::limit($roomType->description, 120) }}</p>
                            <small>{{ $roomType->available_count }} available</small>
                        </div>
                        <div class="modern-price">{{ $currency }}{{ number_format((float) ($roomType->base_price ?? $roomType->price ?? 0), 0) }}</div>
                    </article>
                @empty
                    <p class="modern-muted">No rooms available at the moment.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="modern-feature-band">
        <div class="wrap modern-feature-grid">
            <div class="modern-feature"><i class="fa-solid fa-water-ladder"></i><h3>Pool Deck</h3><p>Calm spaces for slow afternoons.</p></div>
            <div class="modern-feature"><i class="fa-solid fa-utensils"></i><h3>Dining</h3><p>Seasonal menus and in-room service.</p></div>
            <div class="modern-feature"><i class="fa-solid fa-spa"></i><h3>Wellness</h3><p>Treatment rooms and quiet recovery.</p></div>
            <div class="modern-feature"><i class="fa-solid fa-wifi"></i><h3>Connected</h3><p>Reliable internet across the property.</p></div>
        </div>
    </section>
</div>
@endsection

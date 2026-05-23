@extends('layouts.frontend')

@section('title', 'Luxury Hotel & Resort')

@section('styles')
<style>
    /* Hero Slider Styles */
    .slider-container {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: hidden;
        background: #000;
    }

    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .slide.active {
        opacity: 1;
        z-index: 2;
    }

    /* Dark overlay for contrast */
    .slide::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1;
    }

    .slide-content {
        position: relative;
        z-index: 3;
        max-width: 800px;
        padding: 2rem;
        color: #fff;
        text-align: center;
        transform: translateY(30px);
        transition: transform 0.8s ease-in-out;
    }

    .slide.active .slide-content {
        transform: translateY(0);
    }

    .slide-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        font-family: 'Outfit', sans-serif;
    }

    .slide-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2.5rem;
        text-shadow: 0 1px 5px rgba(0, 0, 0, 0.3);
        line-height: 1.6;
    }

    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        font-size: 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
        z-index: 10;
    }

    .slider-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: var(--primary);
    }

    .slider-btn.prev {
        left: 20px;
    }

    .slider-btn.next {
        right: 20px;
    }

    .slider-dots {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.75rem;
        z-index: 10;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .dot.active {
        background: #fff;
        transform: scale(1.25);
    }

    @media (max-width: 768px) {
        .slider-container {
            height: 480px;
        }
        .slide-title {
            font-size: 2.25rem;
        }
        .slide-subtitle {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .slider-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

    .search-container {
        position: relative;
        z-index: 30;
        max-width: 900px;
        margin: -3rem auto 0 auto;
        padding: 0 1rem;
    }

    .search-card {
        background: var(--surface-glass);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--shadow-lg);
        display: grid;
        grid-template-columns: 1fr 1fr 120px auto;
        gap: 1.5rem;
        align-items: end;
    }

    @media (max-width: 768px) {
        .search-card {
            grid-template-columns: 1fr;
        }
    }

    .section-header {
        text-align: center;
        max-width: 600px;
        margin: 5rem auto 3rem auto;
    }

    .section-tag {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: var(--primary);
        margin-bottom: 0.5rem;
        display: inline-block;
        background: var(--primary-glow);
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .section-desc {
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto 5rem auto;
        padding: 0 1.5rem;
    }

    .room-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform var(--transition-smooth), box-shadow var(--transition-smooth);
        display: flex;
        flex-direction: column;
    }

    .room-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-md);
    }

    .room-image-placeholder {
        height: 220px;
        background: linear-gradient(135deg, var(--primary-glow) 0%, var(--secondary-glow) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 3rem;
        position: relative;
    }

    .room-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--success);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .room-badge.danger {
        background: var(--danger);
    }

    .room-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .room-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .room-description {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 1.25rem;
        flex-grow: 1;
    }

    .room-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .amenity-tag {
        background: var(--background);
        font-size: 0.75rem;
        color: var(--text-secondary);
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
    }

    .room-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
        margin-top: auto;
    }

    .room-price {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .room-price span {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 400;
    }

    .features-section {
        background: var(--background);
        padding: 5rem 1.5rem;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: var(--surface);
        border-radius: var(--radius-md);
        padding: 2rem;
        border: 1px solid var(--border-color);
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-md);
        background: var(--primary-glow);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1.5rem auto;
    }

    .contact-map-section {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        max-width: 1200px;
        margin: 5rem auto;
        gap: 3rem;
        padding: 0 1.5rem;
    }

    @media (max-width: 768px) {
        .contact-map-section {
            grid-template-columns: 1fr;
        }
    }

    .contact-info-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
    }

    .contact-row {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .contact-row-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--secondary-glow);
        color: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .contact-label {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .contact-value {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .map-wrapper {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        height: 400px;
    }

    .map-wrapper iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Promo Pop-up Modal */
    .promo-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    .promo-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .promo-modal-content {
        background: var(--surface-glass);
        backdrop-filter: blur(25px);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        width: 90%;
        max-width: 550px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        transform: scale(0.9) translateY(20px);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
    }

    .promo-modal-overlay.active .promo-modal-content {
        transform: scale(1) translateY(0);
    }

    .promo-modal-close {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast);
        z-index: 10;
    }

    .promo-modal-close:hover {
        background: rgba(0, 0, 0, 0.6);
        transform: rotate(90deg);
    }

    .promo-modal-banner {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .promo-modal-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.5));
    }

    .promo-modal-body {
        padding: 2.25rem;
        text-align: center;
        color: var(--text-primary);
    }

    .promo-badge {
        display: inline-block;
        background: var(--primary-glow);
        color: var(--primary);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 0.35rem 1rem;
        border-radius: var(--radius-full);
        margin-bottom: 1rem;
        letter-spacing: 0.05em;
    }

    .promo-title {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
        font-family: 'Outfit', sans-serif;
    }

    .promo-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }

    .promo-coupon-container {
        background: rgba(255, 255, 255, 0.04);
        border: 2px dashed var(--primary);
        border-radius: var(--radius-md);
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
        gap: 1rem;
    }

    .promo-coupon-code {
        font-family: monospace;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 0.05em;
    }

    .promo-copy-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .promo-copy-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
    <!-- Dynamic Hero Slider -->
    <div class="slider-container">
        @forelse($slides as $index => $slide)
            <div class="slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slide->image_path }}');">
                <div class="slide-content">
                    @if($slide->title)
                        <h1 class="slide-title">{{ $slide->title }}</h1>
                    @endif
                    @if($slide->subtitle)
                        <p class="slide-subtitle">{{ $slide->subtitle }}</p>
                    @endif
                    @if($slide->button_text && $slide->button_link)
                        <a href="{{ $slide->button_link }}" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; font-size: 1rem; text-decoration: none;">
                            {{ $slide->button_text }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <!-- Fallback Static Banner if no slides seeded or configured -->
            <div class="slide active" style="background: linear-gradient(135deg, rgba(110, 68, 255, 0.08) 0%, rgba(244, 68, 150, 0.08) 100%), var(--surface);">
                <div class="slide-content" style="color: var(--text-primary);">
                    <h1 class="slide-title" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        {{ $heroTitle }}
                    </h1>
                    <p class="slide-subtitle" style="color: var(--text-secondary);">{{ $heroSubtitle }}</p>
                </div>
            </div>
        @endforelse

        @if($slides->count() > 1)
            <button class="slider-btn prev" onclick="moveSlide(-1)" aria-label="Previous Slide"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="slider-btn next" onclick="moveSlide(1)" aria-label="Next Slide"><i class="fa-solid fa-chevron-right"></i></button>
            
            <div class="slider-dots">
                @foreach($slides as $index => $slide)
                    <span class="dot {{ $index === 0 ? 'active' : '' }}" onclick="setSlide({{ $index }})"></span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Search / Booking Availability Widget -->
    <div class="search-container">
        <form action="/" method="GET" class="search-card">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="check_in_date" class="form-label" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Check-in Date</label>
                <input type="date" name="check_in_date" id="check_in_date" class="form-control" value="{{ $checkIn }}" required min="{{ date('Y-m-d') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="check_out_date" class="form-label" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Check-out Date</label>
                <input type="date" name="check_out_date" id="check_out_date" class="form-control" value="{{ $checkOut }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="guests" class="form-label" style="font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">Guests</label>
                <select name="guests" id="guests" class="form-control">
                    @for($i=1; $i<=8; $i++)
                        <option value="{{ $i }}" {{ $guestsCount == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary btn-block" style="padding: 0.75rem 1.5rem; height: 46px; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 600;">
                    <i class="fa-solid fa-magnifying-glass"></i> Check
                </button>
            </div>
        </form>
    </div>

    <!-- Main Dynamic Catalog -->
    <section id="rooms" class="explore-section">
        <div class="section-header">
            <span class="section-tag">Luxurious Suites</span>
            <h2 class="section-title">
                @if($searched)
                    Available Rooms for Your Stay
                @else
                    Explore Our Accommodations
                @endif
            </h2>
            <p class="section-desc">
                @if($searched)
                    Showing available options matching {{ $guestsCount }} guests for {{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }} stay.
                @else
                    Find the perfect accommodation choice tailored for your business, comfort, or leisure needs.
                @endif
            </p>
        </div>

        <div class="rooms-grid">
            @forelse($roomTypes as $type)
                @php
                    $roomImage = 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?q=80&w=800';
                    if (is_array($type->images) && count($type->images) > 0 && $type->images[0] !== 'default.jpg') {
                        $roomImage = $type->images[0];
                    } elseif (str_contains(strtolower($type->name), 'deluxe')) {
                        $roomImage = 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=800';
                    } elseif (str_contains(strtolower($type->name), 'suite')) {
                        $roomImage = 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=800';
                    }
                @endphp
                <div class="room-card animate-fade-in">
                    <div class="room-image-placeholder" style="background-image: url('{{ $roomImage }}'); background-size: cover; background-position: center; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                        <i class="fa-solid fa-bed" style="position: relative; z-index: 5; opacity: 0.9;"></i>
                        @if($type->available_count > 0)
                            <span class="room-badge">
                                {{ $type->available_count }} Available
                            </span>
                        @else
                            <span class="room-badge danger">
                                Sold Out
                            </span>
                        @endif
                    </div>
                    <div class="room-body">
                        <h3 class="room-name">{{ $type->name }}</h3>
                        <p class="room-description">{{ $type->description }}</p>
                        
                        <div class="room-amenities">
                            @if(is_array($type->amenities))
                                @foreach($type->amenities as $amenity)
                                    <span class="amenity-tag">{{ $amenity }}</span>
                                @endforeach
                            @elseif(is_string($type->amenities))
                                @foreach(json_decode($type->amenities, true) ?? explode(',', $type->amenities) as $amenity)
                                    <span class="amenity-tag">{{ trim($amenity) }}</span>
                                @endforeach
                            @endif
                            <span class="amenity-tag"><i class="fa-solid fa-users"></i> Max {{ $type->capacity }}</span>
                        </div>

                        <div class="room-footer">
                            <div class="room-price">
                                ${{ number_format($type->base_price, 2) }} <span>/ night</span>
                            </div>
                            
                            @if($type->available_count > 0)
                                @if(Auth::check())
                                    @if(auth()->user()->isCustomer())
                                        <a href="{{ route('customer.book', $type->id) }}?check_in_date={{ $checkIn }}&check_out_date={{ $checkOut }}&guests={{ $guestsCount }}" class="btn btn-primary" style="text-decoration: none;">
                                            Book Now
                                        </a>
                                    @else
                                        <button class="btn btn-outline" disabled>Log in as Customer</button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}?redirect_to={{ urlencode(route('customer.book', $type->id)) }}&check_in_date={{ $checkIn }}&check_out_date={{ $checkOut }}&guests={{ $guestsCount }}" class="btn btn-primary" style="text-decoration: none;">
                                        Book Now
                                    </a>
                                @endif
                            @else
                                <button class="btn btn-outline" style="border-color: var(--border-color); color: var(--text-muted);" disabled>Unavailable</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--text-muted);"></i>
                    <h3>No room types defined yet.</h3>
                    <p>Please log in as Administrator to add room types to the system.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Marketing Features Grid -->
    <section class="features-section">
        <div class="section-header" style="margin-top: 0;">
            <span class="section-tag">Elite Amenities</span>
            <h2 class="section-title">{{ $welcomeTitle }}</h2>
            <p class="section-desc">{{ $welcomeDescription }}</p>
        </div>

        <div class="features-grid">
            @php
                $services = json_decode(\App\Models\Setting::getValue('services_list', '[]'), true) ?: [];
            @endphp
            @forelse($services as $service)
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid {{ $service['icon'] ?? 'fa-star' }}"></i>
                    </div>
                    <h3 style="margin-bottom: 0.75rem;">{{ $service['name'] ?? '' }}</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">{{ $service['description'] ?? '' }}</p>
                </div>
            @empty
                <!-- Fallback defaults -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-bell-concierge"></i>
                    </div>
                    <h3 style="margin-bottom: 0.75rem;">24/7 Concierge</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">Our top-tier professional concierge team is dedicated to supporting your every request, any hour of the day.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-water-ladder"></i>
                    </div>
                    <h3 style="margin-bottom: 0.75rem;">Infinite Pool</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">Bathe in our heated outdoor infinity-edge swimming pool overlooking the stunning golden sea horizons.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 style="margin-bottom: 0.75rem;">Fine Dining</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">Indulge in high-end local culinary delights prepared fresh by award-winning Michelin-star chefs.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Contact & Map Address Section -->
    <section class="contact-map-section">
        <div class="contact-info-card">
            <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; font-weight: 700;">Get in Touch</h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2.5rem;">
                Have questions or special reservation requirements? Reach out directly to our guest experience officers.
            </p>

            <div class="contact-row">
                <div class="contact-row-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <div class="contact-label">Call Support</div>
                    <div class="contact-value">{{ $contactPhone }}</div>
                </div>
            </div>

            <div class="contact-row">
                <div class="contact-row-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <div class="contact-label">Email Address</div>
                    <div class="contact-value">{{ $contactEmail }}</div>
                </div>
            </div>

            <div class="contact-row">
                <div class="contact-row-icon" style="background: var(--primary-glow); color: var(--primary);">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <div class="contact-label">Resort Location</div>
                    <div class="contact-value">Golden Coast Beach Boulevard, Suite A, Victoria</div>
                </div>
            </div>
        </div>

        <div class="map-wrapper">
            @if($mapAddress && (str_contains($mapAddress, 'http') || str_contains($mapAddress, '<iframe')))
                @if(str_contains($mapAddress, '<iframe'))
                    {!! $mapAddress !!}
                @else
                    <iframe src="{{ $mapAddress }}" allowfullscreen="" loading="lazy"></iframe>
                @endif
            @else
                <div style="width:100%; height:100%; background: linear-gradient(135deg, var(--background) 0%, var(--border-color) 100%); display: flex; flex-direction:column; align-items: center; justify-content: center; color: var(--text-secondary); text-align: center; padding: 2rem;">
                    <i class="fa-solid fa-map-location-dot" style="font-size: 3.5rem; margin-bottom: 1rem; color: var(--primary);"></i>
                    <h4 style="margin-bottom: 0.5rem;">Map Address Configured</h4>
                    <p style="font-size: 0.875rem; max-width: 320px;">
                        {{ $mapAddress ?: 'Golden Coast Beach Boulevard, Suite A, Victoria' }}
                    </p>
                </div>
            @endif
        </div>
    </section>

    @if(\App\Models\Setting::getValue('promo_popup_enabled', '0') == '1')
        @php
            $promoTitle = \App\Models\Setting::getValue('promo_popup_title', 'Special Offer!');
            $promoContent = \App\Models\Setting::getValue('promo_popup_content');
            $promoImage = \App\Models\Setting::getValue('promo_popup_image') ?: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=800';
            $promoCoupon = \App\Models\Setting::getValue('promo_popup_coupon');
        @endphp
        <!-- Promo Modal Overlay -->
        <div class="promo-modal-overlay" id="promoModalOverlay">
            <div class="promo-modal-content">
                <button type="button" class="promo-modal-close" onclick="closePromoModal()"><i class="fa-solid fa-xmark"></i></button>
                <div class="promo-modal-banner" style="background-image: url('{{ $promoImage }}');"></div>
                <div class="promo-modal-body">
                    <span class="promo-badge">Limited Time Offer</span>
                    <h2 class="promo-title">{{ $promoTitle }}</h2>
                    <p class="promo-text">{{ $promoContent }}</p>
                    
                    @if($promoCoupon)
                        <div class="promo-coupon-container">
                            <span class="promo-coupon-code" id="promoCouponCode">{{ $promoCoupon }}</span>
                            <button type="button" class="promo-copy-btn" id="promoCopyBtn" onclick="copyPromoCoupon()">
                                <i class="fa-solid fa-copy"></i> Copy Code
                            </button>
                        </div>
                    @endif
                    
                    <div>
                        <button type="button" class="btn btn-outline" onclick="closePromoModal()" style="padding: 0.75rem 2rem;">Dismiss</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Promo Modal script
    function closePromoModal() {
        const overlay = document.getElementById('promoModalOverlay');
        if (overlay) overlay.classList.remove('active');
    }

    function copyPromoCoupon() {
        const couponText = document.getElementById('promoCouponCode').innerText;
        navigator.clipboard.writeText(couponText).then(() => {
            const copyBtn = document.getElementById('promoCopyBtn');
            const originalHtml = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
            copyBtn.style.background = 'var(--success)';
            setTimeout(() => {
                copyBtn.innerHTML = originalHtml;
                copyBtn.style.background = 'var(--primary)';
            }, 2000);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('promoModalOverlay');
        if (overlay && !sessionStorage.getItem('promo_popup_shown')) {
            setTimeout(() => {
                overlay.classList.add('active');
                sessionStorage.setItem('promo_popup_shown', 'true');
            }, 1500);
        }
    });

    // Hero Slider script
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let sliderTimer = null;

    function showSlide(index) {
        if (slides.length === 0) return;
        
        // Reset slide classes
        slides.forEach(slide => slide.classList.remove('active'));
        if (dots.length > 0) {
            dots.forEach(dot => dot.classList.remove('active'));
        }

        currentSlideIndex = (index + slides.length) % slides.length;
        slides[currentSlideIndex].classList.add('active');
        if (dots.length > 0) {
            dots[currentSlideIndex].classList.add('active');
        }
        
        // Reset autoplay timer
        resetTimer();
    }

    function moveSlide(direction) {
        showSlide(currentSlideIndex + direction);
    }

    function setSlide(index) {
        showSlide(index);
    }

    function resetTimer() {
        if (sliderTimer) clearInterval(sliderTimer);
        if (slides.length > 1) {
            sliderTimer = setInterval(() => {
                moveSlide(1);
            }, 6000); // Auto transition every 6 seconds
        }
    }

    // Initialize
    if (slides.length > 0) {
        resetTimer();
    }
</script>
@endsection

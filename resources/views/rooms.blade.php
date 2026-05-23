@extends('layouts.frontend')

@section('title', 'Accommodations Catalog')

@section('styles')
<style>
    .rooms-hero {
        position: relative;
        padding: 5rem 2rem;
        background: linear-gradient(135deg, rgba(110, 68, 255, 0.05) 0%, rgba(244, 68, 150, 0.05) 100%), var(--surface);
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }

    .catalog-container {
        max-width: 1200px;
        margin: 5rem auto;
        padding: 0 1.5rem;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2.5rem;
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

    .room-image {
        height: 240px;
        background: linear-gradient(135deg, var(--primary-glow) 0%, var(--secondary-glow) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 3.5rem;
        position: relative;
        overflow: hidden;
    }

    .room-image::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0.8;
    }

    .room-image.standard::before {
        background-image: url('https://images.unsplash.com/photo-1598928506311-c55ded91a20c?q=80&w=800');
    }

    .room-image.deluxe::before {
        background-image: url('https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=800');
    }

    .room-image.suite::before {
        background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=800');
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
        z-index: 5;
    }

    .room-badge.danger {
        background: var(--danger);
    }

    .room-body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .room-name {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .room-description {
        font-size: 0.95rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .room-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .amenity-tag {
        background: var(--background);
        font-size: 0.75rem;
        color: var(--text-secondary);
        padding: 0.3rem 0.6rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
    }

    .room-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border-color);
        padding-top: 1.25rem;
        margin-top: auto;
    }

    .room-price {
        font-family: 'Outfit', sans-serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .room-price span {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 400;
    }
</style>
@endsection

@section('content')
    <header class="rooms-hero animate-fade-in">
        <div class="container" style="max-width: 800px;">
            <span class="section-tag">Luxurious Stays</span>
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Explore Our Accommodations</h1>
            <p style="font-size: 1.2rem; color: var(--text-secondary); line-height: 1.6;">
                Find the perfect room type matching your desires for absolute comfort and relaxation.
            </p>
        </div>
    </header>

    <div class="catalog-container animate-fade-in">
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
                <div class="room-card">
                    <div class="room-image" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('{{ $roomImage }}'); background-size: cover; background-position: center; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                        <i class="fa-solid fa-bed" style="position: relative; z-index: 5; color: #fff; opacity: 0.9;"></i>
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
                            <span class="amenity-tag"><i class="fa-solid fa-users"></i> Max {{ $type->capacity }} guests</span>
                        </div>

                        <div class="room-footer">
                            <div class="room-price">
                                ${{ number_format($type->base_price, 2) }} <span>/ night</span>
                            </div>
                            
                            @if($type->available_count > 0)
                                @if(Auth::check())
                                    @if(auth()->user()->isCustomer())
                                        <a href="{{ route('customer.book', $type->id) }}" class="btn btn-primary" style="text-decoration: none;">
                                            Book Now
                                        </a>
                                    @else
                                        <button class="btn btn-outline" disabled>Log in as Customer</button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}?redirect_to={{ urlencode(route('customer.book', $type->id)) }}" class="btn btn-primary" style="text-decoration: none;">
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
                    <h3>No accommodations currently listed.</h3>
                    <p>Please check back later or contact customer support.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
@if(isset($roomTypes) && !$roomTypes->isEmpty())
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Hotel",
  "name": "{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel') }}",
  "description": "{{ \App\Models\Setting::getValue('welcome_description', 'A luxury sanctuary where contemporary design meets pristine nature.') }}",
  "telephone": "{{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }}",
  "email": "{{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Golden Coast Beach, Suite A",
    "addressLocality": "Golden Coast",
    "addressCountry": "US"
  },
  "starRating": {
    "@type": "Rating",
    "ratingValue": "5"
  },
  "priceRange": "$$$",
  "containsPlace": [
    @foreach($roomTypes as $index => $type)
    {
      "@type": "HotelRoom",
      "name": "{{ $type->name }}",
      "description": "{{ $type->description }}",
      "occupancy": {
        "@type": "QuantitativeValue",
        "value": {{ $type->capacity }},
        "unitCode": "C62"
      },
      "amenityFeature": [
        @php
          $amenitiesList = [];
          if (is_array($type->amenities)) {
              $amenitiesList = $type->amenities;
          } elseif (is_string($type->amenities)) {
              $amenitiesList = json_decode($type->amenities, true) ?? explode(',', $type->amenities);
          }
        @endphp
        @foreach($amenitiesList as $aIndex => $amenity)
        {
          "@type": "LocationFeatureSpecification",
          "name": "{{ trim($amenity) }}",
          "value": true
        }
        @if(!$loop->last),@endif
        @endforeach
      ],
      "offers": {
        "@type": "Offer",
        "price": "{{ number_format($type->base_price, 2, '.', '') }}",
        "priceCurrency": "{{ \App\Models\Setting::getValue('currency', 'USD') }}"
      }
    }
    @if(!$loop->last),@endif
    @endforeach
  ]
}
</script>
@endif
@endsection

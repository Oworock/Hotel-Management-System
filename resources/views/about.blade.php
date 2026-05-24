@extends('layouts.frontend')

@section('title', 'About Us')

@section('styles')
<style>
    .about-hero {
        position: relative;
        padding: 6rem 2rem;
        background: linear-gradient(135deg, rgba(110, 68, 255, 0.05) 0%, rgba(244, 68, 150, 0.05) 100%), var(--surface);
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }
    
    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        max-width: 1200px;
        margin: 5rem auto;
        padding: 0 1.5rem;
        align-items: center;
    }
    
    @media (max-width: 768px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }
    
    .about-image {
        height: 400px;
        background: linear-gradient(135deg, var(--primary-glow) 0%, var(--secondary-glow) 100%);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: var(--primary);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .about-image::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800');
        background-size: cover;
        background-position: center;
        opacity: 0.85;
    }
    
    .about-content h2 {
        font-size: 2.25rem;
        margin-bottom: 1.5rem;
        font-weight: 700;
    }
    
    .about-content p {
        color: var(--text-secondary);
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }
    
    .testimonials-section {
        background: var(--background);
        padding: 5rem 1.5rem;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2.5rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .testimonial-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .testimonial-card::after {
        content: '"';
        position: absolute;
        top: 15px;
        right: 25px;
        font-size: 6rem;
        color: var(--primary-glow);
        font-family: serif;
        line-height: 1;
    }
    
    .testimonial-quote {
        font-style: italic;
        color: var(--text-primary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 1rem;
        position: relative;
        z-index: 2;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .author-info h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    
    .author-info p {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .section-header {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 3rem auto;
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
</style>
@endsection

@section('content')
    <header class="about-hero animate-fade-in">
        <div class="container" style="max-width: 800px;">
            <span class="section-tag">Our Legacy</span>
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">
                {{ \App\Models\Setting::getValue('about_title', 'A Luxury Oasis of Peace') }}
            </h1>
            <p style="font-size: 1.2rem; color: var(--text-secondary); line-height: 1.6;">
                {{ \App\Models\Setting::getValue('about_description', 'Escape the ordinary at Aetheria resorts, where luxury meets tranquility.') }}
            </p>
        </div>
    </header>

    <section class="about-grid">
        <div class="about-image animate-fade-in">
            <i class="fa-solid fa-hotel" style="position: relative; z-index: 5; color: #fff; text-shadow: 0 4px 15px rgba(0,0,0,0.4);"></i>
        </div>
        <div class="about-content animate-fade-in">
            <h2>Our Story & Hospitality</h2>
            <p>
                {{ \App\Models\Setting::getValue('about_history_text', 'Founded in 2012, Aetheria has grown to represent elite hospitality standards. Over the decade, we have hosted thousands of guests, establishing ourselves as a premium destination for luxury travelers.') }}
            </p>
            <p>
                We believe that true luxury lies in the details. From our curated architecture that blends seamlessly with the coastline, to our highly personalized butler service, every aspect of your stay is carefully crafted to offer ultimate comfort and renewal.
            </p>
            <div style="display: flex; gap: 1.5rem; margin-top: 2rem;">
                <div>
                    <h3 style="font-size: 2rem; color: var(--primary); font-weight: 800;">14+</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">Years of Excellence</p>
                </div>
                <div style="border-left: 1px solid var(--border-color); padding-left: 1.5rem;">
                    <h3 style="font-size: 2rem; color: var(--secondary); font-weight: 800;">150k+</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">Happy Guests</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="section-header" style="margin-top: 0;">
            <span class="section-tag">Reviews</span>
            <h2 class="section-title">What Our Guests Say</h2>
            <p class="section-desc">Read genuine reviews and recommendations from luxury travelers who experienced paradise with us.</p>
        </div>

        <div class="testimonials-grid">
            @php
                $defaultTestimonials = [
                    [
                        'quote' => 'Aetheria is the absolute peak of modern luxury. The beach side views are breathtaking and the butler service makes you feel like royalty.',
                        'name' => 'Charlotte Bennet',
                        'role' => 'Luxury Travel Journalist'
                    ],
                    [
                        'quote' => 'The rooms are state of the art and incredibly clean. Booking check-in and checkout were completely seamless online. We will definitely return next year.',
                        'name' => 'Marcus Aurelius',
                        'role' => 'Frequent Guest'
                    ],
                    [
                        'quote' => 'Unbelievable culinary experience! The fine dining menu designed by the Michelin-star chefs was a highlight of our entire trip.',
                        'name' => 'Sophia Varghese',
                        'role' => 'Food & Wine Critic'
                    ]
                ];
                $managedTestimonials = \App\Models\Testimonial::where('is_active', true)->orderByDesc('is_featured')->take(6)->get();
                $testimonials = $managedTestimonials->isNotEmpty()
                    ? $managedTestimonials->map(fn ($item) => [
                        'quote' => $item->content,
                        'name' => $item->guest_name,
                        'role' => $item->guest_title,
                    ])->all()
                    : $defaultTestimonials;
            @endphp
            @foreach($testimonials as $t)
                <div class="testimonial-card">
                    <p class="testimonial-quote">{{ $t['quote'] ?? '' }}</p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>{{ $t['name'] ?? '' }}</h4>
                            <p>{{ $t['role'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

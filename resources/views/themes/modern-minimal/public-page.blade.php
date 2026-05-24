@extends('layouts.frontend')

@section('title', $title)
@section('meta_title', $page->meta_title ?? $post->meta_title ?? $title)
@section('meta_description', $page->meta_description ?? $post->meta_description ?? $subtitle ?? '')
@section('meta_keywords', $page->meta_keywords ?? $post->meta_keywords ?? '')

@section('styles')
<style>
    .theme-public-modern {
        background:
            linear-gradient(180deg, rgba(255,255,255,.96), rgba(247,249,252,.98)),
            radial-gradient(circle at 14% 8%, color-mix(in srgb, var(--primary) 13%, transparent), transparent 32%);
        color: #101828;
    }
    .theme-public-modern .wrap {
        width: min(1180px, 100%);
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    .theme-public-modern .page-shell {
        min-height: 100vh;
    }
    .theme-public-modern .page-hero {
        padding: 4.5rem 0 2.75rem;
        background:
            linear-gradient(135deg, #fff 0%, color-mix(in srgb, var(--primary) 8%, #fff) 100%);
        border-bottom: 1px solid rgba(16, 24, 40, 0.08);
    }
    .theme-public-modern .hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 2rem;
        align-items: end;
    }
    .theme-public-modern .page-kicker {
        color: var(--primary);
        font-weight: 900;
        text-transform: uppercase;
        font-size: .8rem;
        letter-spacing: .08em;
    }
    .theme-public-modern h1 {
        font-size: clamp(2.5rem, 6vw, 5rem);
        line-height: .98;
        margin: .75rem 0 1rem;
        color: #101828;
        letter-spacing: 0;
    }
    .theme-public-modern .subtitle {
        color: #667085;
        max-width: 720px;
        line-height: 1.75;
        font-size: 1.08rem;
    }
    .theme-public-modern .hero-panel {
        background: #101828;
        color: #fff;
        padding: 1.25rem;
        border-radius: 8px;
        box-shadow: 0 20px 50px rgba(16, 24, 40, .18);
    }
    .theme-public-modern .hero-panel strong {
        display: block;
        font-size: 2.25rem;
        line-height: 1;
        color: #fff;
    }
    .theme-public-modern .hero-panel span {
        color: rgba(255, 255, 255, .72);
        font-size: .9rem;
    }
    .theme-public-modern .content-band {
        padding: 3.25rem 0 4.5rem;
    }
    .theme-public-modern .modern-page-frame {
        display: grid;
        grid-template-columns: 230px minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }
    .theme-public-modern .modern-page-index {
        position: sticky;
        top: 6rem;
        background: #fff;
        border: 1px solid #eaecf0;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 14px 35px rgba(16, 24, 40, .05);
    }
    .theme-public-modern .modern-page-index a,
    .theme-public-modern .modern-page-index span {
        display: block;
        padding: .7rem .8rem;
        color: #667085;
        font-weight: 800;
        border-radius: 8px;
    }
    .theme-public-modern .modern-page-index .active {
        background: color-mix(in srgb, var(--primary) 10%, white);
        color: var(--primary);
    }
    .theme-public-modern .modern-content-panel {
        min-width: 0;
    }
    .theme-public-modern .modern-stat-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1px;
        overflow: hidden;
        border-radius: 8px;
        border: 1px solid #eaecf0;
        margin-bottom: 1.25rem;
        background: #eaecf0;
    }
    .theme-public-modern .modern-stat-strip div {
        background: #fff;
        padding: 1rem;
    }
    .theme-public-modern .modern-stat-strip strong {
        display: block;
        font-size: 1.45rem;
        color: #101828;
    }
    .theme-public-modern .modern-stat-strip span {
        color: #667085;
        font-size: .82rem;
    }
    .theme-public-modern .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.15rem;
    }
    .theme-public-modern .page-card,
    .theme-public-modern details,
    .theme-public-modern article {
        background: #fff;
        border: 1px solid #eaecf0;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 14px 35px rgba(16, 24, 40, .06);
    }
    .theme-public-modern .room-card,
    .theme-public-modern .blog-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .theme-public-modern .room-card:hover,
    .theme-public-modern .blog-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 22px 45px rgba(16, 24, 40, .1);
    }
    .theme-public-modern .media-block {
        height: 205px;
        margin: -1.25rem -1.25rem 1rem;
        border-radius: 8px 8px 0 0;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        overflow: hidden;
    }
    .theme-public-modern .media-block img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .theme-public-modern .price {
        color: var(--primary);
        font-size: 1.4rem;
        font-weight: 900;
    }
    .theme-public-modern .btn-link {
        display: inline-flex;
        margin-top: 1rem;
        border-radius: 8px;
        background: var(--primary);
        color: #fff;
        padding: .75rem 1rem;
        font-weight: 800;
        text-decoration: none;
    }
    .theme-public-modern .faq-item {
        padding: 1.05rem 1.2rem;
    }
    .theme-public-modern .gallery-grid {
        grid-template-columns: repeat(12, 1fr);
    }
    .theme-public-modern .gallery-card {
        grid-column: span 4;
    }
    .theme-public-modern .gallery-card:nth-child(5n + 1) {
        grid-column: span 6;
    }
    .theme-public-modern .blog-show-layout .media-block {
        height: 360px;
    }
    @media (max-width: 900px) {
        .theme-public-modern .hero-grid {
            grid-template-columns: 1fr;
        }
        .theme-public-modern .modern-page-frame,
        .theme-public-modern .modern-stat-strip {
            grid-template-columns: 1fr;
        }
        .theme-public-modern .modern-page-index {
            position: static;
        }
        .theme-public-modern .gallery-grid,
        .theme-public-modern .gallery-card,
        .theme-public-modern .gallery-card:nth-child(5n + 1) {
            display: grid;
            grid-template-columns: 1fr;
            grid-column: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-public-modern page-shell">
    <section class="page-hero">
        <div class="wrap hero-grid">
            <div>
                <div class="page-kicker">{{ $hotelName }}</div>
                <h1>{{ $title }}</h1>
                @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
            </div>
            <aside class="hero-panel">
                <strong>{{ $pageType === 'rooms' ? '09' : '24' }}</strong>
                <span>{{ \App\Helpers\ThemeHelper::getThemeContent('side_text', $pageType === 'rooms' ? 'Curated stays ready for booking' : 'Guest services with direct support') }}</span>
            </aside>
        </div>
    </section>

    <section class="content-band">
        <div class="wrap modern-page-frame">
            <aside class="modern-page-index">
                <span class="active">{{ $title }}</span>
                <a href="{{ route('rooms') }}">Rooms</a>
                <a href="{{ route('gallery') }}">Gallery</a>
                <a href="{{ route('contact') }}">Contact</a>
            </aside>
            <div class="modern-content-panel">
                <div class="modern-stat-strip">
                    <div><strong>{{ $pageType === 'rooms' ? '09' : '01' }}</strong><span>{{ $pageType === 'rooms' ? 'Room categories' : 'Current page' }}</span></div>
                    <div><strong>24/7</strong><span>Guest support</span></div>
                    <div><strong>{{ $currency }}</strong><span>Booking currency</span></div>
                </div>
                @include('themes.partials.public-content')
            </div>
        </div>
    </section>
</div>
@endsection

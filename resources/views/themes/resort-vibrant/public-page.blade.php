@extends('layouts.frontend')

@section('title', $title)
@section('meta_title', $page->meta_title ?? $post->meta_title ?? $title)
@section('meta_description', $page->meta_description ?? $post->meta_description ?? $subtitle ?? '')
@section('meta_keywords', $page->meta_keywords ?? $post->meta_keywords ?? '')

@section('styles')
<style>
    .theme-public-resort { background:#e9fff8; color:#053d34; }
    .theme-public-resort .wrap { width:min(1180px,100%); margin:0 auto; padding:0 1.5rem; }
    .theme-public-resort .page-hero {
        padding:5.5rem 0 4rem;
        background:
            linear-gradient(135deg,rgba(0,95,91,.88),rgba(14,165,233,.56)),
            url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80') center/cover;
        position:relative;
    }
    .theme-public-resort .page-hero:after {
        content:"";
        position:absolute;
        left:0;
        right:0;
        bottom:-1px;
        height:44px;
        background:#e9fff8;
        clip-path:polygon(0 45%, 18% 72%, 38% 42%, 58% 70%, 78% 44%, 100% 68%, 100% 100%, 0 100%);
    }
    .theme-public-resort .hero-grid {
        position:relative;
        z-index:1;
        display:grid;
        grid-template-columns:minmax(0,1fr) 340px;
        gap:2rem;
        align-items:end;
    }
    .theme-public-resort .page-kicker { color:#fff; font-weight:900; text-transform:uppercase; }
    .theme-public-resort h1 { color:#fff; font-size:clamp(2.8rem,7vw,5.8rem); line-height:1; margin:.75rem 0 1rem; }
    .theme-public-resort h3 { color:#053d34; }
    .theme-public-resort .subtitle { color:rgba(255,255,255,.86); max-width:720px; line-height:1.8; }
    .theme-public-resort .quick-card {
        background:#fff;
        border-radius:8px;
        padding:1.15rem;
        box-shadow:0 22px 60px rgba(5,61,52,.22);
    }
    .theme-public-resort .quick-card strong {
        color:#053d34;
        display:block;
        font-size:1.6rem;
    }
    .theme-public-resort .quick-card p {
        color:#45746d;
        margin:.4rem 0 0;
    }
    .theme-public-resort .content-band { padding:4.5rem 0; }
    .theme-public-resort .resort-page-shell {
        display:grid;
        grid-template-columns:minmax(0, 1fr) 260px;
        gap:1.25rem;
        align-items:start;
    }
    .theme-public-resort .resort-route-card {
        background:#f7ff65;
        color:#053d34;
        border-radius:8px;
        padding:1.25rem;
        box-shadow:0 18px 45px rgba(5,61,52,.12);
        position:sticky;
        top:6rem;
    }
    .theme-public-resort .resort-route-card a {
        display:flex;
        justify-content:space-between;
        color:#053d34;
        font-weight:900;
        padding:.75rem 0;
        border-top:1px solid rgba(5,61,52,.16);
    }
    .theme-public-resort .resort-content-water {
        background:rgba(255,255,255,.7);
        border:1px solid #b7eadf;
        border-radius:8px;
        padding:1.2rem;
    }
    .theme-public-resort .card-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.1rem; }
    .theme-public-resort .page-card,
    .theme-public-resort details,
    .theme-public-resort article { background:#fff; border:1px solid #b7eadf; border-radius:8px; padding:1.25rem; box-shadow:0 18px 45px rgba(5,61,52,.1); }
    .theme-public-resort .room-card {
        border-top:6px solid #19b7a5;
    }
    .theme-public-resort .media-block { height:205px; margin:-1.25rem -1.25rem 1rem; border-radius:8px 8px 0 0; background:linear-gradient(135deg,var(--primary),var(--secondary)); overflow:hidden; }
    .theme-public-resort .media-block img { width:100%; height:100%; object-fit:cover; }
    .theme-public-resort .price { color:var(--secondary); font-size:1.45rem; font-weight:900; }
    .theme-public-resort .btn-link { display:inline-flex; margin-top:1rem; background:var(--secondary); color:#fff; border-radius:8px; padding:.75rem 1rem; font-weight:900; text-decoration:none; }
    .theme-public-resort .gallery-grid {
        grid-template-columns:repeat(6,1fr);
    }
    .theme-public-resort .gallery-card {
        grid-column:span 2;
    }
    .theme-public-resort .gallery-card:nth-child(4n + 1) {
        grid-column:span 3;
    }
    .theme-public-resort .gallery-card:nth-child(4n + 2) {
        grid-column:span 3;
    }
    .theme-public-resort .testimonial-card {
        background:#f7ff65;
        border-color:#d6e93f;
    }
    .theme-public-resort .faq-item summary {
        color:#04796c;
    }
    .theme-public-resort .map-card iframe {
        height:500px;
    }
    @media (max-width: 900px) {
        .theme-public-resort .hero-grid {
            grid-template-columns:1fr;
        }
        .theme-public-resort .resort-page-shell {
            grid-template-columns:1fr;
        }
        .theme-public-resort .resort-route-card {
            position:static;
        }
        .theme-public-resort .gallery-grid,
        .theme-public-resort .gallery-card,
        .theme-public-resort .gallery-card:nth-child(4n + 1),
        .theme-public-resort .gallery-card:nth-child(4n + 2) {
            display:grid;
            grid-template-columns:1fr;
            grid-column:auto;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-public-resort">
    <section class="page-hero">
        <div class="wrap hero-grid">
            <div>
                <div class="page-kicker">{{ $hotelName }}</div>
                <h1>{{ $title }}</h1>
                @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
            </div>
            <aside class="quick-card">
                <strong>{{ \App\Helpers\ThemeHelper::getThemeContent('booking_title', 'Plan your stay') }}</strong>
                <p>{{ $contactPhone ?? 'Reservations are ready to help.' }}</p>
                <a class="btn-link" href="{{ route('rooms') }}">View Rooms</a>
            </aside>
        </div>
    </section>
    <section class="content-band">
        <div class="wrap resort-page-shell">
            <div class="resort-content-water">
                @include('themes.partials.public-content')
            </div>
            <aside class="resort-route-card">
                <strong style="font-size:1.35rem;">{{ \App\Helpers\ThemeHelper::getThemeContent('side_title', 'Explore next') }}</strong>
                <a href="{{ route('rooms') }}"><span>Rooms</span><i class="fa-solid fa-arrow-right"></i></a>
                <a href="{{ route('services') }}"><span>Activities</span><i class="fa-solid fa-arrow-right"></i></a>
                <a href="{{ route('contact') }}"><span>Map</span><i class="fa-solid fa-arrow-right"></i></a>
            </aside>
        </div>
    </section>
</div>
@endsection

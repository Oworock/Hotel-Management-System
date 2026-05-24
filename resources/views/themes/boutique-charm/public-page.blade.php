@extends('layouts.frontend')

@section('title', $title)
@section('meta_title', $page->meta_title ?? $post->meta_title ?? $title)
@section('meta_description', $page->meta_description ?? $post->meta_description ?? $subtitle ?? '')
@section('meta_keywords', $page->meta_keywords ?? $post->meta_keywords ?? '')

@section('styles')
<style>
    .theme-public-boutique { background:#fff7f0; color:#38251c; }
    .theme-public-boutique .wrap { width:min(1160px,100%); margin:0 auto; padding:0 1.5rem; }
    .theme-public-boutique .page-hero {
        padding:4.75rem 0 3.75rem;
        background:
            linear-gradient(90deg,#fff7f0 0%, #f3dccd 58%, #e7c7b7 100%);
        overflow:hidden;
    }
    .theme-public-boutique .hero-grid {
        display:grid;
        grid-template-columns:minmax(0, .95fr) minmax(280px, .55fr);
        gap:2rem;
        align-items:center;
    }
    .theme-public-boutique .page-kicker { color:var(--primary); font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .theme-public-boutique h1 { color:#38251c; font-size:clamp(2.6rem,7vw,5.4rem); line-height:1; margin:.75rem 0 1rem; }
    .theme-public-boutique h3 { color:#38251c; }
    .theme-public-boutique .subtitle { color:#6f4e3d; max-width:720px; line-height:1.8; }
    .theme-public-boutique .hero-card {
        background:#fffaf6;
        border:1px solid rgba(111,78,61,.16);
        border-radius:8px;
        padding:1rem;
        transform:rotate(1.5deg);
        box-shadow:0 24px 60px rgba(111,78,61,.16);
    }
    .theme-public-boutique .hero-card img {
        width:100%;
        aspect-ratio:4/5;
        object-fit:cover;
        border-radius:6px;
        display:block;
    }
    .theme-public-boutique .content-band { padding:4rem 0; }
    .theme-public-boutique .boutique-scrapbook {
        display:grid;
        grid-template-columns:minmax(0, 1fr) 280px;
        gap:1.5rem;
        align-items:start;
    }
    .theme-public-boutique .scrapbook-main {
        background:#fffaf6;
        border:1px solid rgba(111,78,61,.16);
        border-radius:8px;
        padding:1.2rem;
        box-shadow:0 24px 60px rgba(111,78,61,.12);
    }
    .theme-public-boutique .scrapbook-tabs {
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        margin-bottom:1rem;
    }
    .theme-public-boutique .scrapbook-tabs span,
    .theme-public-boutique .scrapbook-tabs a {
        background:#ffe9dc;
        color:#6f4e3d;
        border-radius:999px;
        padding:.5rem .8rem;
        font-weight:900;
        font-size:.84rem;
    }
    .theme-public-boutique .scrapbook-note {
        background:#38251c;
        color:#fff7f0;
        border-radius:8px;
        padding:1.25rem;
        transform:rotate(1deg);
        position:sticky;
        top:6rem;
    }
    .theme-public-boutique .scrapbook-note h3 {
        color:#fff7f0;
    }
    .theme-public-boutique .card-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.15rem; }
    .theme-public-boutique .page-card,
    .theme-public-boutique details,
    .theme-public-boutique article { background:#fffaf6; border:1px solid rgba(111,78,61,.16); border-radius:8px; padding:1.25rem; box-shadow:0 18px 45px rgba(111,78,61,.1); }
    .theme-public-boutique .room-card:nth-child(even),
    .theme-public-boutique .gallery-card:nth-child(even),
    .theme-public-boutique .blog-card:nth-child(even) {
        transform:translateY(1.25rem);
    }
    .theme-public-boutique .media-block { height:185px; margin:-1.25rem -1.25rem 1rem; border-radius:8px 8px 0 0; background:linear-gradient(135deg,var(--primary),var(--secondary)); overflow:hidden; }
    .theme-public-boutique .media-block img { width:100%; height:100%; object-fit:cover; }
    .theme-public-boutique .price { color:var(--primary); font-size:1.45rem; font-weight:900; }
    .theme-public-boutique .btn-link { display:inline-flex; margin-top:1rem; background:var(--primary); color:#fff; border-radius:8px; padding:.75rem 1rem; font-weight:900; text-decoration:none; }
    .theme-public-boutique .faq-item {
        border-style:dashed;
    }
    .theme-public-boutique .testimonial-card {
        background:#ffe9dc;
    }
    .theme-public-boutique .gallery-grid {
        align-items:start;
    }
    .theme-public-boutique .gallery-card:nth-child(3n + 1) .media-block {
        height:260px;
    }
    .theme-public-boutique .contact-grid {
        grid-template-columns:1fr 1fr 1fr;
    }
    @media (max-width: 900px) {
        .theme-public-boutique .hero-grid,
        .theme-public-boutique .contact-grid,
        .theme-public-boutique .boutique-scrapbook {
            grid-template-columns:1fr;
        }
        .theme-public-boutique .scrapbook-note {
            position:static;
            transform:none;
        }
        .theme-public-boutique .room-card:nth-child(even),
        .theme-public-boutique .gallery-card:nth-child(even),
        .theme-public-boutique .blog-card:nth-child(even) {
            transform:none;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-public-boutique">
    <section class="page-hero">
        <div class="wrap hero-grid">
            <div>
                <div class="page-kicker">{{ $hotelName }}</div>
                <h1>{{ $title }}</h1>
                @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
            </div>
            <figure class="hero-card">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=900&q=80" alt="{{ $hotelName }}">
            </figure>
        </div>
    </section>
    <section class="content-band">
        <div class="wrap boutique-scrapbook">
            <div class="scrapbook-main">
                <div class="scrapbook-tabs">
                    <span>{{ $title }}</span>
                    <a href="{{ route('testimonials') }}">Stories</a>
                    <a href="{{ route('gallery') }}">Photos</a>
                </div>
                @include('themes.partials.public-content')
            </div>
            <aside class="scrapbook-note">
                <h3>{{ \App\Helpers\ThemeHelper::getThemeContent('side_title', 'Guest note') }}</h3>
                <p style="line-height:1.7;margin-top:.7rem;">{{ \App\Helpers\ThemeHelper::getThemeContent('side_text', 'Warm service, local texture, and a stay that feels personal from arrival.') }}</p>
            </aside>
        </div>
    </section>
</div>
@endsection

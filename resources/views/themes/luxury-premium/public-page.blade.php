@extends('layouts.frontend')

@section('title', $title)
@section('meta_title', $page->meta_title ?? $post->meta_title ?? $title)
@section('meta_description', $page->meta_description ?? $post->meta_description ?? $subtitle ?? '')
@section('meta_keywords', $page->meta_keywords ?? $post->meta_keywords ?? '')

@section('styles')
<style>
    .theme-public-luxury {
        background: #120f0a;
        color: #fff8eb;
        font-family: Georgia, 'Times New Roman', serif;
    }
    .theme-public-luxury .wrap { width:min(1180px,100%); margin:0 auto; padding:0 1.5rem; }
    .theme-public-luxury .page-hero {
        padding: 5.5rem 0 4.5rem;
        background:
            linear-gradient(120deg, rgba(18,15,10,.98), rgba(18,15,10,.66)),
            url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1600&q=80') center/cover;
        border-bottom: 1px solid rgba(216,182,109,.3);
    }
    .theme-public-luxury .hero-grid {
        display:grid;
        grid-template-columns:minmax(0,1fr) 360px;
        gap:3rem;
        align-items:center;
    }
    .theme-public-luxury .page-kicker { color:#d8b66d; text-transform:uppercase; font-size:.78rem; font-weight:900; letter-spacing:.12em; }
    .theme-public-luxury h1 { color:#fff8eb; font-size:clamp(2.8rem,7vw,5.8rem); line-height:.95; margin:.85rem 0 1rem; font-family:Georgia,'Times New Roman',serif; }
    .theme-public-luxury h3 { color:#fff8eb; font-family:Georgia,'Times New Roman',serif; }
    .theme-public-luxury .subtitle { color:rgba(255,248,235,.75); max-width:720px; line-height:1.8; }
    .theme-public-luxury .hero-reservation {
        border:1px solid rgba(216,182,109,.45);
        padding:1.35rem;
        background:rgba(18,15,10,.72);
        box-shadow:0 30px 70px rgba(0,0,0,.28);
    }
    .theme-public-luxury .hero-reservation span { display:block; color:#d8b66d; font-size:.78rem; text-transform:uppercase; letter-spacing:.12em; font-weight:900; }
    .theme-public-luxury .hero-reservation strong { display:block; color:#fff8eb; font-size:2rem; margin:.6rem 0; }
    .theme-public-luxury .content-band {
        padding:4.5rem 0;
        background:linear-gradient(180deg, #120f0a, #1c1710);
    }
    .theme-public-luxury .luxury-folio {
        display:grid;
        grid-template-columns:120px minmax(0, 1fr);
        gap:2rem;
        align-items:start;
    }
    .theme-public-luxury .folio-spine {
        writing-mode:vertical-rl;
        text-orientation:mixed;
        border-right:1px solid rgba(216,182,109,.32);
        min-height:520px;
        padding-right:1.2rem;
        color:#d8b66d;
        text-transform:uppercase;
        letter-spacing:.18em;
        font-size:.78rem;
        font-weight:900;
    }
    .theme-public-luxury .folio-head {
        display:grid;
        grid-template-columns:minmax(0, 1fr) 220px;
        gap:1.5rem;
        border-bottom:1px solid rgba(216,182,109,.32);
        padding-bottom:1.5rem;
        margin-bottom:1.5rem;
    }
    .theme-public-luxury .folio-mark {
        border:1px solid rgba(216,182,109,.32);
        display:grid;
        place-items:center;
        min-height:150px;
        color:#d8b66d;
        font-size:3rem;
    }
    .theme-public-luxury .card-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.35rem; }
    .theme-public-luxury .page-card,
    .theme-public-luxury details,
    .theme-public-luxury article {
        background:rgba(255,248,235,.06);
        border:1px solid rgba(216,182,109,.32);
        padding:1.35rem;
        color:rgba(255,248,235,.82);
    }
    .theme-public-luxury .room-card,
    .theme-public-luxury .blog-card,
    .theme-public-luxury .testimonial-card {
        border-radius:0;
    }
    .theme-public-luxury .media-block { height:250px; margin:-1.35rem -1.35rem 1.1rem; background:linear-gradient(135deg,#d8b66d,#332817); overflow:hidden; }
    .theme-public-luxury .media-block img { width:100%; height:100%; object-fit:cover; }
    .theme-public-luxury .price { color:#d8b66d; font-size:1.6rem; font-weight:900; }
    .theme-public-luxury .btn-link { display:inline-flex; margin-top:1rem; border:1px solid #d8b66d; background:#d8b66d; color:#15120d; padding:.75rem 1rem; font-weight:900; text-decoration:none; text-transform:uppercase; font-size:.78rem; }
    .theme-public-luxury .faq-list {
        max-width:900px;
        margin:0 auto;
    }
    .theme-public-luxury .faq-item {
        border-left:4px solid #d8b66d;
    }
    .theme-public-luxury .testimonial-grid {
        grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    }
    .theme-public-luxury .gallery-card:nth-child(even) .media-block {
        height:310px;
    }
    .theme-public-luxury .blog-show-layout {
        grid-template-columns:minmax(0,1fr) 350px;
    }
    .theme-public-luxury .blog-sidebar {
        background:#d8b66d;
        color:#15120d;
        border-color:#d8b66d;
    }
    .theme-public-luxury .blog-sidebar h3 { color:#15120d; }
    @media (max-width: 900px) {
        .theme-public-luxury .hero-grid { grid-template-columns:1fr; }
        .theme-public-luxury .luxury-folio,
        .theme-public-luxury .folio-head { grid-template-columns:1fr; }
        .theme-public-luxury .folio-spine {
            writing-mode:horizontal-tb;
            min-height:auto;
            border-right:0;
            border-bottom:1px solid rgba(216,182,109,.32);
            padding:0 0 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="theme-public-luxury">
    <section class="page-hero">
        <div class="wrap hero-grid">
            <div>
                <div class="page-kicker">{{ $hotelName }}</div>
                <h1>{{ $title }}</h1>
                @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
            </div>
            <aside class="hero-reservation">
                <span>{{ \App\Helpers\ThemeHelper::getThemeContent('side_title', 'Private desk') }}</span>
                <strong>{{ $contactPhone ?? 'Reservations' }}</strong>
                <p>{{ \App\Helpers\ThemeHelper::getThemeContent('side_text', 'Tailored arrivals, premium rooms, and guest support arranged with care.') }}</p>
            </aside>
        </div>
    </section>
    <section class="content-band">
        <div class="wrap luxury-folio">
            <aside class="folio-spine">{{ $hotelName }} / {{ $title }}</aside>
            <div>
                <div class="folio-head">
                    <div>
                        <div class="page-kicker">Selected dossier</div>
                        <h2 style="font-size:clamp(2rem,4vw,4rem);margin:.4rem 0 0;">{{ $title }}</h2>
                    </div>
                    <div class="folio-mark">§</div>
                </div>
                @include('themes.partials.public-content')
            </div>
        </div>
    </section>
</div>
@endsection

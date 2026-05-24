@extends('layouts.frontend')

@section('title', $title)
@section('meta_title', $page->meta_title ?? $post->meta_title ?? $title)
@section('meta_description', $page->meta_description ?? $post->meta_description ?? $subtitle ?? '')
@section('meta_keywords', $page->meta_keywords ?? $post->meta_keywords ?? '')

@section('styles')
<style>
    .theme-public-modern {
        background: #f7f9fc;
        color: #101828;
    }
    .theme-public-modern .wrap {
        width: min(1120px, 100%);
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    .theme-public-modern .page-hero {
        padding: 5rem 0 3rem;
        background: linear-gradient(135deg, #fff, color-mix(in srgb, var(--primary) 9%, white));
        border-bottom: 1px solid rgba(16, 24, 40, 0.08);
    }
    .theme-public-modern .page-kicker {
        color: var(--primary);
        font-weight: 900;
        text-transform: uppercase;
        font-size: .8rem;
    }
    .theme-public-modern h1 {
        font-size: clamp(2.5rem, 6vw, 5rem);
        line-height: 1;
        margin: .75rem 0 1rem;
        color: #101828;
    }
    .theme-public-modern .subtitle {
        color: #667085;
        max-width: 720px;
        line-height: 1.75;
        font-size: 1.08rem;
    }
    .theme-public-modern .content-band {
        padding: 4rem 0;
    }
    .theme-public-modern .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1rem;
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
    .theme-public-modern .media-block {
        height: 190px;
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
</style>
@endsection

@section('content')
<div class="theme-public-modern">
    <section class="page-hero">
        <div class="wrap">
            <div class="page-kicker">{{ $hotelName }}</div>
            <h1>{{ $title }}</h1>
            @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
        </div>
    </section>

    <section class="content-band">
        <div class="wrap">
            @include('themes.partials.public-content')
        </div>
    </section>
</div>
@endsection

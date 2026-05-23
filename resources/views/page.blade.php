@extends('layouts.frontend')

@section('title', $page->title)

@if($page->meta_title)
    @section('meta_title', $page->meta_title)
@endif
@if($page->meta_description)
    @section('meta_description', $page->meta_description)
@endif
@if($page->meta_keywords)
    @section('meta_keywords', $page->meta_keywords)
@endif

@section('styles')
<style>
    .dynamic-page-hero {
        position: relative;
        padding: 5rem 2rem;
        background: linear-gradient(135deg, rgba(110, 68, 255, 0.05) 0%, rgba(244, 68, 150, 0.05) 100%), var(--surface);
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }
    
    .dynamic-page-content {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 1.5rem;
        line-height: 1.8;
        color: var(--text-primary);
        font-size: 1.1rem;
    }
    
    .dynamic-page-content h2, .dynamic-page-content h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .dynamic-page-content h2 {
        font-size: 1.75rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.5rem;
    }

    .dynamic-page-content h3 {
        font-size: 1.4rem;
    }

    .dynamic-page-content p {
        margin-bottom: 1.5rem;
        color: var(--text-secondary);
    }

    .dynamic-page-content ul, .dynamic-page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
        color: var(--text-secondary);
    }

    .dynamic-page-content li {
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
    <header class="dynamic-page-hero animate-fade-in">
        <div class="container" style="max-width: 800px;">
            <span class="section-tag">Information Page</span>
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem;">
                {{ $page->title }}
            </h1>
        </div>
    </header>

    <main class="dynamic-page-content animate-fade-in">
        {!! nl2br(e($page->content)) !!}
    </main>
@endsection

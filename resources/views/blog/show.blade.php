@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-16 max-w-3xl">
        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-96 object-cover">
            @endif
            
            <div class="p-8 md:p-12">
                <div class="flex items-center justify-between mb-6 text-gray-600 text-sm">
                    <time>{{ $post->published_at->format('F d, Y') }}</time>
                    <span>By {{ $post->author->name }}</span>
                </div>
                
                <h1 class="text-4xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>
                
                <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                    {!! \App\Support\HtmlSanitizer::clean($post->content) !!}
                </div>
                
                <div class="border-t pt-8">
                    <a href="{{ route('blog.index') }}" class="inline-block text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                        ← Back to Blog
                    </a>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection

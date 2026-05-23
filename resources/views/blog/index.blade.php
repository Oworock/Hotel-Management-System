@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Hotel Blog</h1>
        <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto">
            Stay updated with our latest news, travel tips, and hotel updates.
        </p>

        @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                @if($post->featured_image)
                <div class="aspect-w-16 aspect-h-9 w-full overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover hover:scale-105 transition-transform">
                </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <time class="text-sm text-gray-500">{{ $post->published_at->format('M d, Y') }}</time>
                        <span class="text-xs text-gray-600">{{ $post->author->name }}</span>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 hover:text-blue-600">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    
                    <p class="text-gray-600 mb-4">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}</p>
                    
                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-block text-blue-600 font-semibold hover:text-blue-800 transition-colors">
                        Read More →
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg p-12 text-center shadow-md">
            <p class="text-gray-600 text-lg">No blog posts available yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection

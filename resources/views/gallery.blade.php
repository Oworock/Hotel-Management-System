@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Photo Gallery</h1>
        <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto">
            Explore our stunning hotel facilities and amenities.
        </p>

        @if($photos->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($photos as $photo)
            <div class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-shadow bg-white">
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $photo->image) }}" alt="{{ $photo->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $photo->title }}</h3>
                    @if($photo->description)
                    <p class="text-gray-600 text-sm">{{ $photo->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg p-12 text-center shadow-md">
            <p class="text-gray-600 text-lg">No photos available yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection

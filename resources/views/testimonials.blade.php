@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Guest Testimonials</h1>
        <p class="text-xl text-gray-600 text-center mb-12 max-w-3xl mx-auto">
            Hear from our guests about their unforgettable experiences at our hotel.
        </p>

        @if($testimonials->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="flex items-center mb-4">
                    @for($i = 0; $i < $testimonial->rating; $i++)
                    <span class="text-yellow-400 text-xl">★</span>
                    @endfor
                </div>
                
                <p class="text-gray-700 mb-6 italic">{{ $testimonial->content }}</p>
                
                <div class="flex items-center">
                    @if($testimonial->image)
                    <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->guest_name }}" class="w-12 h-12 rounded-full mr-4 object-cover">
                    @else
                    <div class="w-12 h-12 rounded-full bg-blue-200 mr-4 flex items-center justify-center">
                        <span class="text-blue-600 font-bold">{{ substr($testimonial->guest_name, 0, 1) }}</span>
                    </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-800">{{ $testimonial->guest_name }}</p>
                        @if($testimonial->guest_title)
                        <p class="text-sm text-gray-600">{{ $testimonial->guest_title }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-gray-100 rounded-lg p-12 text-center">
            <p class="text-gray-600 text-lg">No testimonials available yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection

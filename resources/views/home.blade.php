@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 via-white to-blue-50">
    <!-- Hero Section -->
    <section class="relative h-screen bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 overflow-hidden flex items-center">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-8 right-1/4 w-96 h-96 bg-cyan-400 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Left Content -->
                <div class="text-white space-y-6">
                    <h1 class="text-5xl md:text-6xl font-bold leading-tight">
                        Your Perfect Stay
                        <span class="block text-cyan-200">Awaits You</span>
                    </h1>
                    <p class="text-xl text-blue-100 leading-relaxed">
                        Experience world-class hospitality with stunning views, exceptional service, and unforgettable memories.
                    </p>
                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('rooms') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 rounded-lg font-bold transition-all transform hover:scale-105 shadow-lg">
                            Browse Rooms
                        </a>
                        <a href="{{ route('contact') }}" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-lg font-bold transition-all">
                            Contact Us
                        </a>
                    </div>
                </div>

                <!-- Right Image/Illustration -->
                <div class="relative h-96 md:h-full">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-cyan-300/20 rounded-3xl backdrop-blur-sm border border-white/20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-9xl">🏨</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Search Bar -->
    <section class="relative -mt-20 z-20 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Find Your Perfect Room</h3>
                <form method="GET" action="{{ route('home') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in</label>
                        <input type="date" name="check_in_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" value="{{ request('check_in_date') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out</label>
                        <input type="date" name="check_out_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" value="{{ request('check_out_date') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Guests</label>
                        <select name="guests" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
                            @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ request('guests', 1) == $i ? 'selected' : '' }}>{{ $i }} Guest{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Welcome Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold text-gray-900">Welcome to stayFlow</h2>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Discover a luxury sanctuary where contemporary design meets pristine nature. Our hotel offers an unparalleled experience with world-class amenities and personalized service.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3">
                            <span class="text-2xl">⭐</span>
                            <span class="text-gray-700">Award-winning hospitality</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-2xl">🏆</span>
                            <span class="text-gray-700">Premium rooms with ocean views</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-2xl">🍽️</span>
                            <span class="text-gray-700">Fine dining restaurants</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-2xl">💆</span>
                            <span class="text-gray-700">World-class spa & wellness</span>
                        </li>
                    </ul>
                    <a href="{{ route('services') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                        Explore Our Services
                    </a>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-100 to-cyan-100 rounded-3xl p-8 h-96 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-8xl mb-4">🌴</div>
                            <p class="text-gray-700 font-semibold">Tropical Paradise</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Rooms -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Featured Rooms</h2>
                <p class="text-xl text-gray-600">Discover our luxurious accommodation options</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($roomTypes ?? [] as $roomType)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-shadow overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-cyan-400 flex items-center justify-center text-6xl">
                        🛏️
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $roomType->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($roomType->description, 100) }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold text-blue-600">${{ $roomType->price }}</span>
                            <span class="text-sm text-gray-500">per night</span>
                        </div>
                        <a href="{{ route('rooms') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Book Now
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-8 text-gray-500">
                    <p>No rooms available at the moment</p>
                </div>
                @endforelse
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('rooms') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    View All Rooms
                </a>
            </div>
        </div>
    </section>

    <!-- Services Highlights -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">World-Class Amenities</h2>
                <p class="text-xl text-gray-600">Everything you need for a perfect stay</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🏊</div>
                    <h3 class="font-bold text-gray-800">Swimming Pool</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">💆</div>
                    <h3 class="font-bold text-gray-800">Spa & Wellness</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🍽️</div>
                    <h3 class="font-bold text-gray-800">Fine Dining</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🏋️</div>
                    <h3 class="font-bold text-gray-800">Fitness Center</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">📶</div>
                    <h3 class="font-bold text-gray-800">High-Speed WiFi</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🛎️</div>
                    <h3 class="font-bold text-gray-800">24/7 Concierge</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🚗</div>
                    <h3 class="font-bold text-gray-800">Valet Parking</h3>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition-colors">
                    <div class="text-5xl mb-3">🎉</div>
                    <h3 class="font-bold text-gray-800">Event Space</h3>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('services') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gradient-to-r from-blue-50 to-cyan-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Guest Reviews</h2>
                <p class="text-xl text-gray-600">What our guests are saying</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $testimonials = [
                        ['name' => 'Sarah Johnson', 'rating' => 5, 'comment' => 'An absolutely wonderful stay! The staff was incredibly attentive and the rooms are beautifully designed.'],
                        ['name' => 'Michael Chen', 'rating' => 5, 'comment' => 'Exceeded all expectations. The spa was amazing and the food at the restaurants was outstanding.'],
                        ['name' => 'Emma Rodriguez', 'rating' => 5, 'comment' => 'Perfect vacation! Beautiful views, comfortable beds, and excellent service throughout our stay.'],
                    ]
                @endphp

                @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < $testimonial['rating']; $i++)
                        <span class="text-yellow-400 text-2xl">★</span>
                        @endfor
                    </div>
                    <p class="text-gray-700 mb-6 leading-relaxed">{{ $testimonial['comment'] }}</p>
                    <p class="font-bold text-gray-800">{{ $testimonial['name'] }}</p>
                    <p class="text-sm text-gray-500">Verified Guest</p>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('testimonials') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    Read More Reviews
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-4">Ready for Your Dream Vacation?</h2>
            <p class="text-xl mb-8 text-blue-100">Book your stay today and experience luxury like never before</p>
            <a href="{{ route('rooms') }}" class="inline-block bg-white text-blue-600 hover:bg-blue-50 font-bold py-4 px-10 rounded-lg transition-colors text-lg">
                Book Now
            </a>
        </div>
    </section>

    <!-- Gallery Preview -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Photo Gallery</h2>
                <p class="text-xl text-gray-600">Explore our beautiful property</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $photos = ['🏨', '🏊', '🍽️', '💆']
                @endphp
                @foreach($photos as $photo)
                <div class="relative group overflow-hidden rounded-xl h-64 bg-gradient-to-br from-blue-300 to-cyan-300 flex items-center justify-center cursor-pointer">
                    <div class="text-7xl group-hover:scale-110 transition-transform duration-300">{{ $photo }}</div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('gallery') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    View Full Gallery
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Preview -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600">Find answers to common questions</p>
            </div>

            <div class="space-y-4">
                @php
                    $faqs = [
                        ['q' => 'What time is check-in?', 'a' => 'Check-in is available from 3:00 PM. Early check-in may be available upon request.'],
                        ['q' => 'Is WiFi free?', 'a' => 'Yes, high-speed WiFi is complimentary for all guests throughout the property.'],
                        ['q' => 'Do you have parking?', 'a' => 'Yes, we offer both self-parking and valet parking services for our guests.'],
                    ]
                @endphp
                @foreach($faqs as $faq)
                <details class="bg-white rounded-lg shadow-md p-6 group cursor-pointer hover:shadow-lg transition-shadow">
                    <summary class="font-bold text-gray-800 flex justify-between items-center">
                        {{ $faq['q'] }}
                        <span class="group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <p class="text-gray-600 mt-4">{{ $faq['a'] }}</p>
                </details>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('faqs') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    See All FAQs
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Preview -->
    <section class="py-16 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="{{ route('rooms') }}" class="hover:text-white transition">Rooms</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition">Services</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white transition">Gallery</a></li>
                        <li><a href="{{ route('faqs') }}" class="hover:text-white transition">FAQs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition">Terms</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <p class="text-gray-300 text-sm mb-2">📞 +1 (800) 555-FLOW</p>
                    <p class="text-gray-300 text-sm">📧 info@stayflow.com</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Follow Us</h4>
                    <div class="flex gap-4 text-xl">
                        <a href="#" class="hover:text-cyan-400 transition">📱</a>
                        <a href="#" class="hover:text-cyan-400 transition">🐦</a>
                        <a href="#" class="hover:text-cyan-400 transition">📷</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; 2026 stayFlow. All rights reserved. | Luxury Hotel Management Platform</p>
            </div>
        </div>
    </section>
</div>
@endsection

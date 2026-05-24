@extends('layouts.app')

@section('theme-content')
<div class="min-h-screen bg-white">
    <!-- Modern Minimal Header -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">stayFlow</h1>
            <ul class="flex gap-8 text-gray-700">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a></li>
                <li><a href="{{ route('rooms') }}" class="hover:text-blue-600 transition">Rooms</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-blue-600 transition">Services</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-blue-600 transition">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-50 to-blue-100 py-20">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-5xl font-bold text-gray-900 mb-4">Your Perfect Stay Awaits</h2>
            <p class="text-xl text-gray-600 mb-8">Modern comfort, exceptional service, unforgettable memories</p>
            <button class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                Book Now
            </button>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-16">
        @yield('main-content')
    </main>

    <!-- Modern Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 grid grid-cols-3 gap-8 mb-8">
            <div>
                <h4 class="font-bold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('faqs') }}" class="hover:text-white transition">FAQs</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Terms</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Contact</h4>
                <p class="text-gray-400 text-sm">info@stayflow.com</p>
                <p class="text-gray-400 text-sm">+1 (800) 555-FLOW</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Follow Us</h4>
                <div class="flex gap-4 text-gray-400">
                    <a href="#" class="hover:text-white transition">Twitter</a>
                    <a href="#" class="hover:text-white transition">Facebook</a>
                    <a href="#" class="hover:text-white transition">Instagram</a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
            <p>&copy; 2026 stayFlow. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection

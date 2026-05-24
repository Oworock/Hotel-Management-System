@extends('layouts.app')

@section('theme-content')
<div class="min-h-screen bg-gradient-to-b from-cyan-50 to-green-50">
    <!-- Resort Header -->
    <header class="bg-gradient-to-r from-cyan-400 via-blue-400 to-pink-400 sticky top-0 z-50 shadow-2xl">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-white drop-shadow-lg">🌴 stayFlow Resort</h1>
            <ul class="flex gap-8 text-white font-bold text-lg">
                <li><a href="{{ route('home') }}" class="hover:text-yellow-200 transition">Home</a></li>
                <li><a href="{{ route('rooms') }}" class="hover:text-yellow-200 transition">Rooms</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-yellow-200 transition">Activities</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-yellow-200 transition">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Resort Hero -->
    <section class="relative h-96 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-400 to-cyan-300 flex items-center justify-center">
            <div class="text-center text-white">
                <h2 class="text-6xl font-bold mb-4 drop-shadow-lg">🌊 Paradise Awaits</h2>
                <p class="text-2xl mb-8 drop-shadow-lg">Your Tropical Getaway</p>
                <button class="bg-pink-500 hover:bg-pink-600 text-white px-10 py-4 rounded-full font-bold transition shadow-lg transform hover:scale-105">
                    🏖️ Book Your Paradise
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-16">
        @yield('main-content')
    </main>

    <!-- Resort Footer -->
    <footer class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-4 gap-8 mb-12">
                <div>
                    <h4 class="font-bold mb-4 text-yellow-200">🏝️ EXPLORE</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('rooms') }}" class="hover:text-yellow-200 transition">Rooms</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-yellow-200 transition">Activities</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-yellow-200 transition">Gallery</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-yellow-200">📚 INFORMATION</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('faqs') }}" class="hover:text-yellow-200 transition">FAQ</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-yellow-200 transition">Privacy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-yellow-200 transition">Terms</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-yellow-200">☎️ CONTACT US</h4>
                    <p class="text-sm mb-2">+1 (800) 555-FLOW</p>
                    <p class="text-sm">hello@stayflow.com</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-yellow-200">🌐 FOLLOW US</h4>
                    <div class="flex gap-4 text-sm">
                        <a href="#" class="hover:text-yellow-200 transition">Instagram 📸</a>
                        <a href="#" class="hover:text-yellow-200 transition">Facebook 👍</a>
                    </div>
                </div>
            </div>
            <div class="border-t-2 border-blue-400 pt-8 text-center text-sm">
                <p>&copy; 2026 stayFlow Resort. Come Experience Paradise! 🌺</p>
            </div>
        </div>
    </footer>
</div>
@endsection

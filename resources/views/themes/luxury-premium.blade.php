@extends('layouts.app')

@section('theme-content')
<div class="min-h-screen bg-amber-50">
    <!-- Luxury Header -->
    <header class="bg-gradient-to-r from-amber-900 to-yellow-900 sticky top-0 z-50 shadow-xl">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex justify-between items-center">
            <h1 class="text-3xl font-serif text-amber-200 tracking-widest">STAYFLOW</h1>
            <ul class="flex gap-10 text-amber-100 font-serif text-lg">
                <li><a href="{{ route('home') }}" class="hover:text-white transition border-b-2 border-transparent hover:border-white">Home</a></li>
                <li><a href="{{ route('rooms') }}" class="hover:text-white transition border-b-2 border-transparent hover:border-white">Rooms</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-white transition border-b-2 border-transparent hover:border-white">Services</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition border-b-2 border-transparent hover:border-white">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Luxury Hero -->
    <section class="relative h-96 bg-gradient-to-b from-gray-800 to-gray-900 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1200 600%22><path fill=%22%23D4AF37%22 d=%22M0,300 Q300,200 600,300 T1200,300 L1200,0 L0,0 Z%22/></svg>')"></div>
        <div class="text-center text-white relative z-10">
            <h2 class="text-6xl font-serif mb-4 tracking-wider">ELEGANCE & COMFORT</h2>
            <p class="text-xl font-light tracking-wide mb-8">Experience Unparalleled Luxury</p>
            <button class="bg-amber-500 hover:bg-amber-600 text-gray-900 px-10 py-4 rounded-none font-serif tracking-widest transition">
                RESERVE NOW
            </button>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-20">
        @yield('main-content')
    </main>

    <!-- Luxury Footer -->
    <footer class="bg-amber-900 text-amber-50 py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-4 gap-8 mb-12 font-serif">
                <div>
                    <h4 class="text-amber-200 mb-4 font-bold tracking-widest">INFORMATION</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('faqs') }}" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Press</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-200 mb-4 font-bold tracking-widest">SERVICES</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('services') }}" class="hover:text-white transition">Amenities</a></li>
                        <li><a href="#" class="hover:text-white transition">Dining</a></li>
                        <li><a href="#" class="hover:text-white transition">Spa</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-amber-200 mb-4 font-bold tracking-widest">CONTACT</h4>
                    <p class="text-sm mb-2">+1 (800) 555-FLOW</p>
                    <p class="text-sm">reservations@stayflow.com</p>
                </div>
                <div>
                    <h4 class="text-amber-200 mb-4 font-bold tracking-widest">FOLLOW US</h4>
                    <div class="flex gap-4 text-sm">
                        <a href="#" class="hover:text-white transition">Instagram</a>
                        <a href="#" class="hover:text-white transition">Facebook</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-amber-800 pt-8 text-center text-sm">
                <p>&copy; 2026 STAYFLOW LUXURY PROPERTIES. ALL RIGHTS RESERVED.</p>
            </div>
        </div>
    </footer>
</div>
@endsection

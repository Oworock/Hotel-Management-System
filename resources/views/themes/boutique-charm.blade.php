@extends('layouts.app')

@section('theme-content')
<div class="min-h-screen bg-orange-50">
    <!-- Boutique Header -->
    <header class="bg-gradient-to-r from-orange-100 to-pink-100 border-b-4 border-orange-400 sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-orange-700 italic">stayFlow Boutique</h1>
            <ul class="flex gap-6 text-orange-900">
                <li><a href="{{ route('home') }}" class="hover:text-orange-600 transition font-medium">Home</a></li>
                <li><a href="{{ route('rooms') }}" class="hover:text-orange-600 transition font-medium">Rooms</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-orange-600 transition font-medium">Services</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-orange-600 transition font-medium">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Boutique Hero -->
    <section class="bg-gradient-to-b from-orange-300 to-pink-200 py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22none%22 stroke=%22%23000%22 stroke-width=%221%22/></svg>')"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-5xl font-bold text-orange-900 mb-4">Charming & Artistic</h2>
            <p class="text-lg text-orange-800 mb-8">Discover the warmth of authentic hospitality</p>
            <button class="bg-orange-500 text-white px-8 py-3 rounded-full hover:bg-orange-600 transition font-bold shadow-lg">
                Plan Your Stay
            </button>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-16">
        @yield('main-content')
    </main>

    <!-- Boutique Footer -->
    <footer class="bg-orange-200 text-orange-900 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-3 gap-8 mb-8">
                <div>
                    <h4 class="font-bold mb-4 text-orange-700">Discover</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('faqs') }}" class="hover:text-orange-700 transition">FAQ</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-orange-700 transition">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-orange-700 transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-orange-700">Connect</h4>
                    <p class="text-sm mb-2">📞 +1 (800) 555-FLOW</p>
                    <p class="text-sm">📧 hello@stayflow.com</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-orange-700">Follow</h4>
                    <div class="flex gap-4 text-sm">
                        <a href="#" class="hover:text-orange-700 transition">🌍 Instagram</a>
                        <a href="#" class="hover:text-orange-700 transition">💬 Facebook</a>
                    </div>
                </div>
            </div>
            <div class="border-t-2 border-orange-300 pt-6 text-center text-sm">
                <p>&copy; 2026 stayFlow Boutique. Crafted with ❤️</p>
            </div>
        </div>
    </footer>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white">
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Our Premium Services</h1>
        <p class="text-xl text-gray-600 text-center mb-16 max-w-3xl mx-auto">
            Experience world-class hospitality with our comprehensive range of amenities and services designed for your comfort and convenience.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🛏️</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Luxury Rooms</h3>
                <p class="text-gray-600">Elegantly appointed rooms with premium bedding, modern amenities, and breathtaking views.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🍽️</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Fine Dining</h3>
                <p class="text-gray-600">Award-winning restaurant and bar serving international and local cuisine prepared by expert chefs.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🏊</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Swimming Pool</h3>
                <p class="text-gray-600">Olympic-sized infinity pool with sun loungers, perfect for relaxation and recreation.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">💆</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Spa & Wellness</h3>
                <p class="text-gray-600">Full-service spa offering massages, facials, and holistic wellness treatments.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">📶</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">High-Speed WiFi</h3>
                <p class="text-gray-600">Complimentary high-speed internet throughout the entire property for seamless connectivity.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🚗</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Valet Parking</h3>
                <p class="text-gray-600">Convenient valet parking service with secure underground garage facilities.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🎉</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Event Spaces</h3>
                <p class="text-gray-600">Versatile conference halls and banquet facilities perfect for weddings and corporate events.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🚴</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Fitness Center</h3>
                <p class="text-gray-600">State-of-the-art gymnasium with personal training and group fitness classes.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-8">
                <div class="text-4xl mb-4">🛎️</div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">Concierge Service</h3>
                <p class="text-gray-600">24/7 concierge assistance for reservations, bookings, and local recommendations.</p>
            </div>
        </div>
    </div>
</div>
@endsection

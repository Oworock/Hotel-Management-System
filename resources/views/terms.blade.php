@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Terms & Conditions</h1>
        
        <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">1. Booking & Reservations</h2>
                <p>All reservations are subject to availability and confirmation. Prices are quoted per room, per night, and may be subject to applicable taxes.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">2. Cancellation Policy</h2>
                <p>Cancellations must be made at least 24 hours before check-in to receive a full refund. Cancellations made within 24 hours may be subject to a one-night charge.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">3. Check-In & Check-Out</h2>
                <p>Standard check-in time is 2:00 PM and check-out time is 11:00 AM. Early check-in and late check-out may be available upon request at an additional cost.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">4. Guest Conduct</h2>
                <p>Guests are expected to comply with all hotel rules and policies. Disruptive behavior, smoking in non-designated areas, or damage to property may result in immediate eviction without refund.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">5. Liability</h2>
                <p>The hotel is not responsible for loss, theft, or damage to personal property. We recommend using in-room safes for valuables.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">6. Payment Terms</h2>
                <p>Payment is due upon booking unless otherwise agreed. We accept all major credit cards and other payment methods as displayed during checkout.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">7. Amendment Rights</h2>
                <p>We reserve the right to modify these terms at any time. Continued use of the platform constitutes acceptance of updated terms.</p>
            </section>
        </div>
    </div>
</div>
@endsection

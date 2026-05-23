@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="container mx-auto px-4 py-16 max-w-4xl">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Privacy Policy</h1>
        
        <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">1. Information We Collect</h2>
                <p>We collect information you provide directly to us, such as when you make a booking, create an account, or contact our support team. This includes:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Contact information (name, email, phone number)</li>
                    <li>Booking details (check-in/check-out dates, room preferences)</li>
                    <li>Payment information (processed securely through payment gateways)</li>
                    <li>Guest preferences and special requests</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">2. How We Use Your Information</h2>
                <p>We use the collected information to:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Process and manage your reservations</li>
                    <li>Provide customer support</li>
                    <li>Send booking confirmations and updates</li>
                    <li>Improve our services and user experience</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">3. Data Security</h2>
                <p>We implement industry-standard security measures to protect your personal information, including encryption, secure servers, and restricted access protocols.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">4. Your Rights</h2>
                <p>You have the right to access, modify, or delete your personal information at any time. Contact us for assistance with these requests.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">5. Contact Us</h2>
                <p>For privacy-related questions, please contact us at <strong>privacy@stayflow.com</strong></p>
            </section>
        </div>
    </div>
</div>
@endsection

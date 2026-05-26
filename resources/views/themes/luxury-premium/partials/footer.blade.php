<footer class="luxury-site-footer">
    <div class="luxury-footer-brand">{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel') }}</div>
    <div class="luxury-footer-grid">
        <p>{!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('global_footer', \App\Models\Setting::getValue('welcome_description', 'Private service, elegant rooms, and memorable arrivals.'))) !!}</p>
        <nav>
            <a href="/rooms">Suites</a>
            <a href="/services">Dining & Spa</a>
            <a href="/faqs">Guest Notes</a>
            <a href="/contact">Concierge</a>
        </nav>
        <p>{{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach, Suite A') }}<br>{{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}</p>
    </div>
</footer>

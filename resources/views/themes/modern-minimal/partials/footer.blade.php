<footer class="modern-site-footer">
    <div class="modern-footer-grid">
        <div>
            <strong>{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel') }}</strong>
            <p>{!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('global_footer', \App\Models\Setting::getValue('welcome_description', 'A refined stay with thoughtful service and simple booking.'))) !!}</p>
        </div>
        <nav>
            <a href="/rooms">Rooms</a>
            <a href="/services">Services</a>
            <a href="/gallery">Gallery</a>
            <a href="/blog">Blog</a>
            <a href="/contact">Contact</a>
        </nav>
        <address>
            {{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach, Suite A') }}<br>
            {{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }}<br>
            {{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}
        </address>
    </div>
</footer>

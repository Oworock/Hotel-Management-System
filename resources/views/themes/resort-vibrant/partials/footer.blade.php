<footer class="resort-site-footer">
    <div>
        <strong>{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Resort') }}</strong>
        <p>{!! \App\Models\Setting::getValue('global_footer', \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') . ' · ' . \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com')) !!}</p>
    </div>
    <nav>
        <a href="/rooms">Rooms</a>
        <a href="/services">Activities</a>
        <a href="/gallery">Photos</a>
        <a href="/contact">Find Us</a>
    </nav>
</footer>

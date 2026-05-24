<footer class="boutique-site-footer">
    <div class="boutique-footer-card">
        <h3>{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria') }}</h3>
        <p>{!! \App\Models\Setting::getValue('global_footer', \App\Models\Setting::getValue('welcome_description', 'A personal stay with warm details and thoughtful service.')) !!}</p>
    </div>
    <div class="boutique-footer-links">
        <a href="/rooms">Rooms</a>
        <a href="/gallery">Gallery</a>
        <a href="/faqs">FAQs</a>
        <a href="/contact">Contact</a>
    </div>
    <p class="boutique-footer-address">{{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach, Suite A') }}</p>
</footer>

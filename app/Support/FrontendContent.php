<?php

namespace App\Support;

use App\Models\Setting;

class FrontendContent
{
    public static function fields(): array
    {
        return [
            'global_header' => ['label' => 'Announcement Bar', 'group' => 'Global', 'type' => 'text', 'default' => 'Book direct with Aetheria Grand Hotel for flexible stays, priority guest support, and seasonal offers.'],
            'global_footer' => ['label' => 'Footer Copyright / Note', 'group' => 'Global', 'type' => 'textarea', 'default' => '© 2026 Aetheria Grand Hotel. Crafted for restful stays, memorable dining, and thoughtful service.'],
            'hero_title' => ['label' => 'Homepage Hero Title', 'group' => 'Homepage', 'type' => 'text', 'default' => 'Luxury Awaits You at Aetheria Grand'],
            'hero_subtitle' => ['label' => 'Homepage Hero Subtitle', 'group' => 'Homepage', 'type' => 'textarea', 'default' => 'Experience absolute peace, beach side views, refined rooms, and attentive service from arrival to checkout.'],
            'welcome_title' => ['label' => 'Homepage Welcome Title', 'group' => 'Homepage', 'type' => 'text', 'default' => 'Experience Paradise'],
            'welcome_description' => ['label' => 'Homepage Welcome Description', 'group' => 'Homepage', 'type' => 'textarea', 'default' => 'A luxury sanctuary where contemporary design meets pristine nature. Located on golden coastal sands, Aetheria Grand Hotel brings together calm rooms, curated dining, wellness spaces, and responsive guest support for business trips, family breaks, and weekend escapes.'],
            'about_title' => ['label' => 'About Section Title', 'group' => 'About', 'type' => 'text', 'default' => 'A Luxury Oasis of Peace'],
            'about_description' => ['label' => 'About Section Description', 'group' => 'About', 'type' => 'textarea', 'default' => 'Escape the ordinary at Aetheria Grand Hotel, where quiet design, warm service, and reliable hospitality create a stay that feels personal.'],
            'about_history_text' => ['label' => 'About History Text', 'group' => 'About', 'type' => 'textarea', 'default' => 'Founded in 2012, Aetheria Grand Hotel has grown into a trusted destination for guests who value comfort, clarity, and attentive service. Over the years, we have hosted leisure travellers, business guests, families, and private events while maintaining a simple promise: every stay should feel calm, well prepared, and easy to manage.'],
            'frontend_about_title' => ['label' => 'About Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'About Aetheria Grand Hotel'],
            'frontend_about_subtitle' => ['label' => 'About Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Thoughtful hospitality, comfortable rooms, and guest support shaped around every stay.'],
            'frontend_rooms_title' => ['label' => 'Rooms Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Rooms & Suites'],
            'frontend_rooms_subtitle' => ['label' => 'Rooms Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Choose the room type that fits your stay, compare amenities, and reserve directly.'],
            'frontend_rooms_body' => ['label' => 'Rooms Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'Browse available room categories with clear rates, capacity, amenities, and live availability. Each room is prepared for comfort, privacy, and an easy arrival experience.'],
            'frontend_services_title' => ['label' => 'Services Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Services & Amenities'],
            'frontend_services_subtitle' => ['label' => 'Services Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Everything needed for a comfortable, memorable stay.'],
            'frontend_services_body' => ['label' => 'Services Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'From concierge support and airport transfers to dining, wellness, housekeeping, and event assistance, our services are designed to remove friction before, during, and after your stay.'],
            'frontend_gallery_title' => ['label' => 'Gallery Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Photo Gallery'],
            'frontend_gallery_subtitle' => ['label' => 'Gallery Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Explore the property, rooms, dining, and facilities before you arrive.'],
            'frontend_gallery_body' => ['label' => 'Gallery Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'View selected spaces across the hotel, including guest rooms, dining areas, wellness spaces, lounges, outdoor areas, and arrival points.'],
            'frontend_faqs_title' => ['label' => 'FAQ Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Frequently Asked Questions'],
            'frontend_faqs_subtitle' => ['label' => 'FAQ Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Helpful answers before you arrive.'],
            'frontend_faqs_body' => ['label' => 'FAQ Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'Find quick answers about check-in, check-out, payments, cancellations, amenities, transport, special requests, and guest support.'],
            'frontend_blog_title' => ['label' => 'Blog Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Hotel Blog'],
            'frontend_blog_subtitle' => ['label' => 'Blog Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Travel notes, hotel updates, and practical guides for planning a better stay.'],
            'frontend_blog_body' => ['label' => 'Blog Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'Read helpful articles on room selection, direct booking, hotel amenities, guest planning, local travel, and ways to make each stay smoother.'],
            'frontend_testimonials_title' => ['label' => 'Testimonials Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Guest Stories'],
            'frontend_testimonials_subtitle' => ['label' => 'Testimonials Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'What guests are saying about their stays.'],
            'frontend_testimonials_body' => ['label' => 'Testimonials Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'Explore real guest feedback from business travellers, couples, families, event guests, and returning visitors.'],
            'frontend_contact_title' => ['label' => 'Contact Page Title', 'group' => 'Page Titles', 'type' => 'text', 'default' => 'Contact Reservations & Guest Support'],
            'frontend_contact_subtitle' => ['label' => 'Contact Page Subtitle', 'group' => 'Page Titles', 'type' => 'textarea', 'default' => 'Reach our reservations and guest support team.'],
            'frontend_contact_body' => ['label' => 'Contact Page Intro', 'group' => 'Page Bodies', 'type' => 'textarea', 'default' => 'Speak with our team about room reservations, directions, special requests, events, group stays, airport transfers, and arrival arrangements.'],
            'frontend_privacy_title' => ['label' => 'Privacy Page Title', 'group' => 'Legal Pages', 'type' => 'text', 'default' => 'Privacy Policy'],
            'frontend_privacy_subtitle' => ['label' => 'Privacy Page Subtitle', 'group' => 'Legal Pages', 'type' => 'textarea', 'default' => 'How we handle guest and booking information.'],
            'frontend_privacy_body' => ['label' => 'Privacy Page Body', 'group' => 'Legal Pages', 'type' => 'textarea', 'default' => 'Aetheria Grand Hotel collects guest information only where it is needed to manage reservations, confirm payments, provide stay support, respond to enquiries, and improve hotel operations. Booking details, contact information, preferences, and service requests are handled with care and protected using reasonable administrative and technical safeguards. We do not sell guest information. Information may be shared with trusted service providers only when required for payment processing, communication, security, legal compliance, or delivery of requested hotel services.'],
            'frontend_terms_title' => ['label' => 'Terms Page Title', 'group' => 'Legal Pages', 'type' => 'text', 'default' => 'Terms & Conditions'],
            'frontend_terms_subtitle' => ['label' => 'Terms Page Subtitle', 'group' => 'Legal Pages', 'type' => 'textarea', 'default' => 'Booking, stay, and platform terms.'],
            'frontend_terms_body' => ['label' => 'Terms Page Body', 'group' => 'Legal Pages', 'type' => 'textarea', 'default' => 'Reservations are subject to room availability, selected rate conditions, payment confirmation, cancellation rules, taxes, and property policies. Guests are responsible for providing accurate booking information, observing check-in and check-out times, respecting hotel facilities, and settling any additional charges incurred during the stay. The hotel may update room assignments, service availability, or operational policies where necessary to protect guest comfort, safety, and service quality.'],
        ];
    }

    public static function defaults(): array
    {
        return collect(self::fields())->mapWithKeys(fn ($meta, $key) => [$key => $meta['default']])->all();
    }

    public static function default(string $key, ?string $fallback = null): ?string
    {
        return self::fields()[$key]['default'] ?? $fallback;
    }

    public static function get(string $key, ?string $fallback = null): ?string
    {
        $default = self::default($key, $fallback);
        $value = Setting::getValue($key, $default);

        return filled($value) ? $value : $default;
    }
}

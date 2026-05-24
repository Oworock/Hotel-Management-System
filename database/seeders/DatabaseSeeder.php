<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Users
        \App\Models\User::create([
            'name' => 'Elizabeth Vance',
            'email' => 'super@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $demoContent = (bool) config('app.demo_content', true);

        $this->call(ThemeSeeder::class);

        if ($demoContent) {
        \App\Models\User::create([
            'name' => 'Gregory House',
            'email' => 'admin@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'John Watson',
            'email' => 'staff1@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'Clara Oswald',
            'email' => 'staff2@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'Arthur Dent',
            'email' => 'customer@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'Pam Beesly',
            'email' => 'receptionist@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'receptionist',
            'status' => 'active',
        ]);

        \App\Models\User::create([
            'name' => 'Chef Auguste',
            'email' => 'chef@hotel.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'kitchen_manager',
            'status' => 'active',
        ]);
        }

        // Seed settings
        \App\Models\Setting::setValue('hotel_name', 'Aetheria Grand Hotel');
        \App\Models\Setting::setValue('currency', 'USD');
        \App\Models\Setting::setValue('tax_rate', '12');
        \App\Models\Setting::setValue('check_in_time', '14:00');
        \App\Models\Setting::setValue('check_out_time', '11:00');
        \App\Models\Setting::setValue('contact_email', 'info@aetheriagrand.com');
        \App\Models\Setting::setValue('contact_phone', '+1 (555) 123-4567');
        
        // Brand & Styling
        \App\Models\Setting::setValue('primary_color', '#6e44ff');
        \App\Models\Setting::setValue('secondary_color', '#f44496');
        \App\Models\Setting::setValue('platform_logo', '<i class="fa-solid fa-hotel"></i> Aetheria');
        \App\Models\Setting::setValue('logo_type', 'text');
        \App\Models\Setting::setValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria');
        \App\Models\Setting::setValue('logo_image', '');
        \App\Models\Setting::setValue('auth_background_image', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1800');
        \App\Models\Setting::setValue('global_header', '✨ Welcome to Aetheria Grand Hotel - Book directly to get 15% off and free breakfast!');
        \App\Models\Setting::setValue('global_footer', '© 2026 Aetheria Grand Hotel. All rights reserved.');
        
        // Home Page & About Page CMS content
        \App\Models\Setting::setValue('hero_title', 'Luxury Awaits You at Aetheria Grand');
        \App\Models\Setting::setValue('hero_subtitle', 'Experience absolute peace, beach side views, and unmatched butler services.');
        \App\Models\Setting::setValue('welcome_title', 'Experience Paradise');
        \App\Models\Setting::setValue('welcome_description', 'A luxury sanctuary where contemporary design meets pristine nature. Located on the golden sands of our private beach, Aetheria offers an escape from the ordinary.');
        \App\Models\Setting::setValue('about_title', 'A Luxury Oasis of Peace');
        \App\Models\Setting::setValue('about_description', 'Escape the ordinary at Aetheria resorts, where luxury meets tranquility.');
        \App\Models\Setting::setValue('about_history_text', 'Founded in 2012, Aetheria has grown to represent elite hospitality standards. Over the decade, we have hosted thousands of guests, establishing ourselves as a premium destination for luxury travelers.');
        \App\Models\Setting::setValue('services_list', json_encode([
            ['icon' => 'fa-bell-concierge', 'name' => '24/7 Butler Service', 'description' => 'Personalized attention for every guest requirement.'],
            ['icon' => 'fa-water-ladder', 'name' => 'Infinity Ocean Pool', 'description' => 'A heated outdoor infinity pool facing the ocean.'],
            ['icon' => 'fa-utensils', 'name' => 'Fine Dining Restaurant', 'description' => 'Exquisite culinary delights prepared by award-winning chefs.'],
            ['icon' => 'fa-spa', 'name' => 'Revitalizing Spa & Wellness', 'description' => 'Massage therapies and organic skin treatments.']
        ]));
        \App\Models\Setting::setValue('physical_address', 'Golden Coast Beach Boulevard, Suite A, Victoria');
        \App\Models\Setting::setValue('map_address', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153153!3d-37.81627977975179!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d15f4b162f4!2sMelbourne%20VIC%2C%20Australia!5e0!3m2!1sen!2sus!4v1625072000000!5m2!1sen!2sus');

        if (! $demoContent) {
            return;
        }

        // Seed Hero Slides
        \App\Models\HeroSlide::create([
            'title' => 'Luxury Awaits You',
            'subtitle' => 'Experience absolute peace, beach side views, and unmatched butler services.',
            'image_path' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=1600',
            'button_text' => 'Book Now',
            'button_link' => '#rooms',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        \App\Models\HeroSlide::create([
            'title' => 'Indulge in Exquisite Comfort',
            'subtitle' => 'Unwind in our signature suites with private jacuzzi and ocean view balconies.',
            'image_path' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1600',
            'button_text' => 'Explore Rooms',
            'button_link' => '/rooms',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        \App\Models\HeroSlide::create([
            'title' => 'Award-Winning Fine Dining',
            'subtitle' => 'Savor local fresh culinary dishes prepared by Michelin-star chefs.',
            'image_path' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1600',
            'button_text' => 'About Our Resort',
            'button_link' => '/about',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // SMTP settings
        \App\Models\Setting::setValue('mail_host', 'smtp.mailtrap.io');
        \App\Models\Setting::setValue('mail_port', '2525');
        \App\Models\Setting::setValue('mail_username', 'smtp_user_demo');
        \App\Models\Setting::setValue('mail_password', 'smtp_pass_demo');
        \App\Models\Setting::setValue('mail_encryption', 'tls');
        \App\Models\Setting::setValue('mail_from_address', 'noreply@aetheriagrand.com');

        // Payment gateway settings
        \App\Models\Setting::setValue('active_payment_gateway', 'disabled');
        \App\Models\Setting::setValue('paystack_public_key', '');
        \App\Models\Setting::setValue('paystack_secret_key', '');
        \App\Models\Setting::setValue('flutterwave_public_key', '');
        \App\Models\Setting::setValue('flutterwave_secret_key', '');

        // Seed Room Types
        $standard = \App\Models\RoomType::create([
            'name' => 'Standard Cozy Room',
            'description' => 'A beautifully compact room featuring a comfortable double bed, working desk, and modern bathroom amenities. Perfect for solo travelers or couples.',
            'base_price' => 120.00,
            'capacity' => 2,
            'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Flat-screen TV', 'Mini Fridge', 'Coffee Maker'],
            'images' => ['standard_1.jpg']
        ]);

        $deluxe = \App\Models\RoomType::create([
            'name' => 'Deluxe Garden View',
            'description' => 'Spacious room with a king-sized plush bed, a relaxing lounge chair, and a balcony facing our lush botanical gardens.',
            'base_price' => 185.00,
            'capacity' => 3,
            'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Balcony', 'King Size Bed', 'Mini Bar', 'Bathrobes & Slippers'],
            'images' => ['deluxe_1.jpg']
        ]);

        $suite = \App\Models\RoomType::create([
            'name' => 'Aetheria Ocean Suite',
            'description' => 'Our signature penthouse suite overlooking the pristine ocean. Features a separate master bedroom, living room, private kitchenette, and a jacuzzi.',
            'base_price' => 340.00,
            'capacity' => 4,
            'amenities' => ['Free Wi-Fi', 'Air Conditioning', 'Ocean View', 'Private Jacuzzi', 'Kitchenette', '24/7 Butler Service', 'Mini Bar'],
            'images' => ['suite_1.jpg']
        ]);

        // Seed Rooms
        \App\Models\Room::create(['room_number' => '101', 'room_type_id' => $standard->id, 'status' => 'available']);
        \App\Models\Room::create(['room_number' => '102', 'room_type_id' => $standard->id, 'status' => 'available']);
        \App\Models\Room::create(['room_number' => '103', 'room_type_id' => $standard->id, 'status' => 'dirty']);
        \App\Models\Room::create(['room_number' => '201', 'room_type_id' => $deluxe->id, 'status' => 'available']);
        \App\Models\Room::create(['room_number' => '202', 'room_type_id' => $deluxe->id, 'status' => 'available']);
        \App\Models\Room::create(['room_number' => '203', 'room_type_id' => $deluxe->id, 'status' => 'maintenance']);
        \App\Models\Room::create(['room_number' => '301', 'room_type_id' => $suite->id, 'status' => 'available']);
        \App\Models\Room::create(['room_number' => '302', 'room_type_id' => $suite->id, 'status' => 'available']);

        foreach (range(104, 116) as $roomNumber) {
            \App\Models\Room::create([
                'room_number' => (string) $roomNumber,
                'room_type_id' => $standard->id,
                'status' => in_array($roomNumber, [111, 115], true) ? 'dirty' : 'available',
            ]);
        }

        foreach (range(204, 216) as $roomNumber) {
            \App\Models\Room::create([
                'room_number' => (string) $roomNumber,
                'room_type_id' => $deluxe->id,
                'status' => in_array($roomNumber, [209, 214], true) ? 'maintenance' : 'available',
            ]);
        }

        foreach (range(303, 310) as $roomNumber) {
            \App\Models\Room::create([
                'room_number' => (string) $roomNumber,
                'room_type_id' => $suite->id,
                'status' => $roomNumber === 307 ? 'dirty' : 'available',
            ]);
        }

        $author = \App\Models\User::where('role', 'admin')->first() ?? \App\Models\User::where('role', 'super_admin')->first();

        $blogPosts = [
            [
                'title' => 'How to Choose the Perfect Hotel Room for a Weekend Escape',
                'slug' => 'choose-perfect-hotel-room-weekend-escape',
                'excerpt' => 'A practical guide to selecting the right room type, amenities, and booking window for a smoother weekend stay.',
                'content' => "Choosing the right hotel room starts with the purpose of your trip. Couples may prefer a quieter floor, a larger bed, and a view, while business travelers often need reliable WiFi, a desk, and quick access to breakfast.\n\nBefore booking, compare room size, cancellation policy, check-in times, and included amenities. A lower nightly price may not be better if breakfast, parking, or late checkout are excluded.\n\nAt Aetheria Grand Hotel, our room categories are designed around different guest needs, from compact stays to signature suites with extra space and elevated service.",
                'meta_title' => 'Choose the Perfect Hotel Room for a Weekend Escape',
                'meta_description' => 'Learn how to choose the best hotel room for a weekend escape by comparing amenities, room size, booking terms, location, and guest needs.',
                'meta_keywords' => 'hotel room, weekend escape, hotel booking tips',
                'focus_keyword' => 'hotel room',
            ],
            [
                'title' => 'Five Amenities Guests Should Look for Before Booking a Hotel',
                'slug' => 'five-amenities-guests-should-look-before-booking-hotel',
                'excerpt' => 'From flexible check-in to wellness spaces, these amenities can turn a standard reservation into a memorable stay.',
                'content' => "Amenities define the quality of a stay long before a guest enters the room. High-speed internet, secure parking, responsive front desk support, and quality dining options remain essential for most travelers.\n\nGuests should also consider wellness amenities, housekeeping standards, accessibility, and whether the hotel can support special requests. These details help prevent friction during the stay.\n\nThe best hotels do not simply list amenities; they deliver them consistently, clearly, and with helpful staff support.",
                'meta_title' => 'Five Hotel Amenities Guests Should Check Before Booking',
                'meta_description' => 'Discover five important hotel amenities to review before booking, including WiFi, dining, parking, wellness spaces, and guest support.',
                'meta_keywords' => 'hotel amenities, hotel booking, guest experience',
                'focus_keyword' => 'hotel amenities',
            ],
            [
                'title' => 'Why Direct Hotel Booking Can Improve Your Stay',
                'slug' => 'direct-hotel-booking-improve-stay',
                'excerpt' => 'Booking directly with a hotel can improve communication, flexibility, and access to exclusive offers.',
                'content' => "Direct hotel booking gives guests a clearer relationship with the property. Requests for early check-in, room preferences, dietary needs, and special occasions are easier to manage when the hotel receives the reservation directly.\n\nMany hotels also reserve their strongest offers for direct bookings, including packages, flexible policies, complimentary upgrades, or breakfast benefits.\n\nGuests still need to compare rates, but direct booking often creates a smoother service experience from reservation to checkout.",
                'meta_title' => 'Why Direct Hotel Booking Can Improve Your Stay',
                'meta_description' => 'See how direct hotel booking can improve guest communication, unlock flexible options, and provide better access to hotel offers.',
                'meta_keywords' => 'direct hotel booking, hotel offers, booking benefits',
                'focus_keyword' => 'direct hotel booking',
            ],
            [
                'title' => 'A Simple Guide to Planning a Luxury Hotel Stay',
                'slug' => 'simple-guide-planning-luxury-hotel-stay',
                'excerpt' => 'Plan a luxury hotel stay with better timing, room selection, dining reservations, and special requests.',
                'content' => "A luxury hotel stay works best when it is planned around the experience you want. Decide whether the trip is for rest, celebration, business, dining, wellness, or family time before choosing a room.\n\nReserve dining, spa sessions, airport pickup, and special arrangements early. Share arrival details with the hotel so the team can prepare the room and support a smooth check-in.\n\nLuxury is not only about the room; it is the combination of timing, service, comfort, and thoughtful details.",
                'meta_title' => 'Simple Guide to Planning a Luxury Hotel Stay',
                'meta_description' => 'Plan a luxury hotel stay with smart tips for room selection, dining reservations, spa bookings, arrival details, and special requests.',
                'meta_keywords' => 'luxury hotel stay, hotel planning, luxury travel',
                'focus_keyword' => 'luxury hotel stay',
            ],
        ];

        foreach ($blogPosts as $post) {
            \App\Models\BlogPost::create(array_merge($post, [
                'author_id' => $author?->id,
                'published_at' => now()->subDays(rand(1, 18)),
                'is_published' => true,
            ]));
        }

        $faqs = [
            ['What time is check-in?', 'Standard check-in begins at 2:00 PM. Early check-in may be available based on room readiness.'],
            ['What time is check-out?', 'Standard check-out is 11:00 AM. Late checkout can be requested through reception.'],
            ['Do you offer airport pickup?', 'Yes. Airport pickup can be arranged before arrival through our guest services team.'],
            ['Is breakfast included?', 'Breakfast availability depends on the selected package or rate plan. Direct booking offers may include breakfast.'],
            ['Do rooms include WiFi?', 'Yes. Complimentary high-speed WiFi is available in guest rooms and public areas.'],
            ['Can I cancel or modify my booking?', 'Cancellation and modification rules depend on the selected rate plan and booking channel.'],
            ['Do you have parking?', 'Yes. Secure guest parking is available, and valet service may be arranged at reception.'],
            ['Are children allowed?', 'Yes. Families are welcome, and selected room types can accommodate children.'],
            ['Do you allow pets?', 'Pet policies vary by room type and availability. Please contact the hotel before booking.'],
            ['Can I request a special setup?', 'Yes. We can help with birthdays, anniversaries, business arrivals, and other special setups.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            \App\Models\FAQ::create([
                'question' => $question,
                'answer' => $answer,
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        foreach ([
            ['Amara Okafor', 'Business Traveler', 'The check-in was fast, the room was spotless, and the staff handled every request with real care.'],
            ['Daniel Brooks', 'Weekend Guest', 'A beautiful stay from arrival to checkout. The suite felt premium and the breakfast service was excellent.'],
            ['Priya Shah', 'Family Guest', 'Our family had enough room, quick support from reception, and a calm atmosphere throughout the stay.'],
            ['Miguel Santos', 'Event Guest', 'The property was polished, the event support was organized, and the guest rooms were very comfortable.'],
        ] as $testimonial) {
            \App\Models\Testimonial::create([
                'guest_name' => $testimonial[0],
                'guest_title' => $testimonial[1],
                'content' => $testimonial[2],
                'rating' => 5,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        foreach ([
            ['Ocean View Suite', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200', 'Signature suite with a refined ocean-facing layout.'],
            ['Infinity Pool Deck', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=1200', 'A relaxing pool deck designed for slow afternoons.'],
            ['Fine Dining Room', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=1200', 'Elegant restaurant space for breakfast, dinner, and private dining.'],
            ['Wellness Spa', 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=1200', 'Calm treatment rooms for massage and recovery.'],
            ['Lobby Lounge', 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?q=80&w=1200', 'Bright lobby lounge for arrivals and informal meetings.'],
            ['Garden Terrace', 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=1200', 'Outdoor terrace for quiet mornings and evening drinks.'],
        ] as $index => $photo) {
            \App\Models\Gallery::create([
                'title' => $photo[0],
                'image' => $photo[1],
                'description' => $photo[2],
                'order' => $index + 1,
                'is_active' => true,
            ]);
        }

        // Seed Tuck Shop & Restaurant Products if the plugin class exists
        if (class_exists('\Plugins\EcommerceTuckShop\Models\Product')) {
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Premium Soft Drink',
                'description' => 'Refreshing chilled soda can (Cola/Lemon-Lime).',
                'price' => 2.50,
                'image_path' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?q=80&w=300',
                'type' => 'tuck_shop',
                'category' => 'Beverages',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Artisanal Chocolate Bar',
                'description' => 'Rich dark chocolate bar with sea salt.',
                'price' => 3.00,
                'image_path' => 'https://images.unsplash.com/photo-1549007994-cb92ca817bc7?q=80&w=300',
                'type' => 'tuck_shop',
                'category' => 'Snacks',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Handcrafted Potato Chips',
                'description' => 'Lightly salted kettle cooked potato chips.',
                'price' => 1.50,
                'image_path' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?q=80&w=300',
                'type' => 'tuck_shop',
                'category' => 'Snacks',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Mineral Spring Water',
                'description' => 'Chilled natural spring water bottle (500ml).',
                'price' => 1.00,
                'image_path' => 'https://images.unsplash.com/photo-1560011961-4ab41261de01?q=80&w=300',
                'type' => 'tuck_shop',
                'category' => 'Beverages',
                'is_available' => true,
            ]);

            // Seed Restaurant Dishes
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Steak Frites',
                'description' => 'Grilled premium ribeye steak with compound butter and crispy French fries.',
                'price' => 28.00,
                'image_path' => 'https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=300',
                'type' => 'restaurant',
                'category' => 'Main Course',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Truffle Mushroom Pasta',
                'description' => 'Creamy tagliatelle pasta with wild mushrooms and black truffle oil.',
                'price' => 22.00,
                'image_path' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?q=80&w=300',
                'type' => 'restaurant',
                'category' => 'Main Course',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Classic Caesar Salad',
                'description' => 'Crisp romaine lettuce, garlic croutons, parmesan cheese, and creamy dressing.',
                'price' => 14.00,
                'image_path' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?q=80&w=300',
                'type' => 'restaurant',
                'category' => 'Starters',
                'is_available' => true,
            ]);
            \Plugins\EcommerceTuckShop\Models\Product::create([
                'name' => 'Chocolate Lava Fondant',
                'description' => 'Warm chocolate cake with a molten center, served with vanilla bean ice cream.',
                'price' => 9.50,
                'image_path' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?q=80&w=300',
                'type' => 'restaurant',
                'category' => 'Desserts',
                'is_available' => true,
            ]);
        }

        $this->call(DemoContentSeeder::class);
    }
}

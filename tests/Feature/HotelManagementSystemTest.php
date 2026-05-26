<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\StaffShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class HotelManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function validGuestProfilePayload(): array
    {
        return [
            'phone_country_code' => '+234',
            'phone' => '8012345678',
            'date_of_birth' => '1990-01-15',
            'nationality' => 'Nigerian',
            'country_of_residence' => 'Nigeria',
            'address_line1' => '12 Victoria Island Road',
            'city' => 'Lagos',
            'id_type' => 'passport',
            'id_number' => 'A12345678',
            'emergency_contact_name' => 'Mary Customer',
            'emergency_contact_relationship' => 'Sister',
            'emergency_contact_phone_country_code' => '+234',
            'emergency_contact_phone' => '8023456789',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic settings needed for the application
        Setting::setValue('hotel_name', 'Test Grand Hotel');
        Setting::setValue('currency', 'USD');
        Setting::setValue('tax_rate', '12');
        Setting::setValue('check_in_time', '14:00');
        Setting::setValue('check_out_time', '11:00');
    }

    /**
     * Test Super Admin login redirect.
     */
    public function test_super_admin_login_redirect(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('super-admin/dashboard');

        // Follow the redirect to verify no 500/infinite loop errors occur after login
        $this->get('super-admin/dashboard')->assertStatus(200);
    }

    /**
     * Test Admin login redirect.
     */
    public function test_admin_login_redirect(): void
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('admin/dashboard');
        $this->get('admin/dashboard')->assertStatus(200);
    }

    /**
     * Test Staff login redirect.
     */
    public function test_staff_login_redirect(): void
    {
        $user = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('staff/dashboard');
        $this->get('staff/dashboard')->assertStatus(200);
    }

    /**
     * Test Customer login redirect.
     */
    public function test_customer_login_redirect(): void
    {
        $user = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('customer/dashboard');
        $this->get('customer/dashboard')->assertStatus(200);
    }

    /**
     * Test registration creates customer.
     */
    public function test_registration_creates_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('customer/dashboard');
        
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    /**
     * Test Super Admin settings and user management.
     */
    public function test_super_admin_can_update_settings_and_manage_users(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 1. Update settings
        $response = $this->actingAs($superAdmin)->post('/super-admin/settings', [
            'hotel_name' => 'Ocean Palace Resorts',
            'currency' => 'EUR',
            'tax_rate' => '15',
            'check_in_time' => '15:00',
            'check_out_time' => '10:00',
            'contact_email' => 'admin@oceanpalace.com',
            'contact_phone' => '123-456-7890',
            'active_payment_gateway' => 'card_simulation',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Ocean Palace Resorts', Setting::getValue('hotel_name'));
        $this->assertEquals('15', Setting::getValue('tax_rate'));

        // 2. Create user (e.g. Admin)
        $response = $this->actingAs($superAdmin)->post('/super-admin/users', [
            'name' => 'New Admin',
            'email' => 'newadmin@test.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@test.com',
            'role' => 'admin',
        ]);

        // 3. Toggle user status
        $newUser = User::where('email', 'newadmin@test.com')->first();
        $response = $this->actingAs($superAdmin)->post("/super-admin/users/{$newUser->id}/toggle");
        
        $response->assertRedirect();
        $this->assertEquals('inactive', $newUser->fresh()->status);
    }

    /**
     * Test Admin room types and rooms management.
     */
    public function test_admin_can_manage_rooms_and_view_metrics(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 1. Store Room Type
        $response = $this->actingAs($admin)->post('/admin/room-types', [
            'name' => 'Premium Suite',
            'description' => 'Luxury ocean views',
            'base_price' => '250.00',
            'capacity' => 4,
            'amenities' => ['TV', 'WiFi', 'Mini Bar'],
        ]);

        $response->assertRedirect();
        $roomType = RoomType::where('name', 'Premium Suite')->first();
        $this->assertNotNull($roomType);
        $this->assertEquals(250.00, $roomType->base_price);

        // 2. Store Room
        $response = $this->actingAs($admin)->post('/admin/rooms', [
            'room_number' => '501',
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        $response->assertRedirect();
        $room = Room::where('room_number', '501')->first();
        $this->assertNotNull($room);
        $this->assertEquals('available', $room->status);

        // 3. Update Room
        $response = $this->actingAs($admin)->post("/admin/rooms/{$room->id}/update", [
            'room_number' => '501-A',
            'room_type_id' => $roomType->id,
            'status' => 'maintenance',
        ]);

        $response->assertRedirect();
        $this->assertEquals('501-A', $room->fresh()->room_number);
        $this->assertEquals('maintenance', $room->fresh()->status);

        // 4. Delete Room
        $response = $this->actingAs($admin)->post("/admin/rooms/{$room->id}/delete");
        $response->assertRedirect();
        $this->assertNull(Room::find($room->id));
    }

    /**
     * Test Staff attendance shift log and housekeeping updates.
     */
    public function test_staff_can_clock_in_out_and_update_housekeeping(): void
    {
        $staff = User::create([
            'name' => 'John Staff',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $roomType = RoomType::create([
            'name' => 'Standard Cozy Room',
            'description' => 'Cozy room',
            'base_price' => 120.00,
            'capacity' => 2,
            'amenities' => ['WiFi'],
        ]);
        $room = Room::create([
            'room_number' => '102',
            'room_type_id' => $roomType->id,
            'status' => 'dirty',
        ]);

        // 1. Clock In
        $response = $this->actingAs($staff)->post('/staff/clock-in');
        $response->assertRedirect();
        $shift = StaffShift::where('user_id', $staff->id)->first();
        $this->assertNotNull($shift);
        $this->assertNotNull($shift->clock_in_at);
        $this->assertNull($shift->clock_out_at);

        // 2. Housekeeping Update
        $response = $this->actingAs($staff)->post("/staff/rooms/{$room->id}/housekeeping", [
            'status' => 'available',
        ]);
        $response->assertRedirect();
        $this->assertEquals('available', $room->fresh()->status);

        // 3. Clock Out
        $response = $this->actingAs($staff)->post('/staff/clock-out');
        $response->assertRedirect();
        $this->assertNotNull($shift->fresh()->clock_out_at);
    }

    /**
     * Test complete customer reservation, booking payment, and check-in/out loop.
     */
    public function test_customer_can_book_and_pay_and_check_in_out(): void
    {
        $customer = User::create([
            'name' => 'Arthur Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $roomType = RoomType::create([
            'name' => 'Standard Room',
            'description' => 'Comfortable double bed',
            'base_price' => 100.00,
            'capacity' => 2,
            'amenities' => ['Free Wi-Fi', 'Air Conditioning'],
        ]);

        $room = Room::create([
            'room_number' => '101',
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        // 1. Booking Checkout Creation
        $response = $this->actingAs($customer)->post('/customer/book', [
            'room_type_id' => $roomType->id,
            'check_in_date' => Carbon::today()->format('Y-m-d'),
            'check_out_date' => Carbon::tomorrow()->format('Y-m-d'), // 1 night stay
            'guests' => 1,
        ] + $this->validGuestProfilePayload());

        $booking = Booking::where('customer_id', $customer->id)->first();
        $this->assertNotNull($booking);
        $response->assertRedirect(route('customer.payment', $booking->id));

        // Subtotal = 100, Tax = 12%, Total = 112
        $this->assertEquals(112.00, $booking->total_price);
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals('unpaid', $booking->payment_status);

        // 2. Perform Payment
        $response = $this->actingAs($customer)->post("/customer/payment/{$booking->id}", [
            'card_number' => '1111222233334444',
            'card_name' => 'Arthur Customer',
            'card_expiry' => '12/28',
            'card_cvv' => '123',
        ]);

        $response->assertRedirect(route('customer.bookings'));
        $booking = $booking->fresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 112.00,
            'status' => 'completed',
        ]);

        // 3. Online Check-in
        $response = $this->actingAs($customer)->post("/customer/bookings/{$booking->id}/check-in");
        $response->assertRedirect();
        
        $this->assertEquals('checked_in', $booking->fresh()->status);
        $this->assertEquals('booked', $room->fresh()->status);

        // 4. Online Check-out
        $response = $this->actingAs($customer)->post("/customer/bookings/{$booking->id}/check-out");
        $response->assertRedirect();

        $this->assertEquals('checked_out', $booking->fresh()->status);
        // Room status becomes dirty after checking out
        $this->assertEquals('dirty', $room->fresh()->status);
    }

    /**
     * Test Admin room type update and deletion constraints.
     */
    public function test_admin_can_update_and_delete_room_type(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $roomType = RoomType::create([
            'name' => 'Deluxe Suite',
            'description' => 'A grand room',
            'base_price' => 150.00,
            'capacity' => 2,
            'amenities' => ['WiFi'],
        ]);

        // 1. Update Room Type
        $response = $this->actingAs($admin)->post("/admin/room-types/{$roomType->id}/update", [
            'name' => 'Updated Deluxe Suite',
            'description' => 'An even grander room',
            'base_price' => 175.00,
            'capacity' => 3,
            'amenities' => ['WiFi', 'TV'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('room_types', [
            'id' => $roomType->id,
            'name' => 'Updated Deluxe Suite',
            'base_price' => 175.00,
        ]);

        // 2. Prevent Deletion when rooms are linked
        $room = Room::create([
            'room_number' => '202',
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        $response = $this->actingAs($admin)->post("/admin/room-types/{$roomType->id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotNull(RoomType::find($roomType->id));

        // 3. Allow Deletion when no rooms are linked
        $room->delete();
        $response = $this->actingAs($admin)->post("/admin/room-types/{$roomType->id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertNull(RoomType::find($roomType->id));
    }

    /**
     * Test Admin can update room status directly.
     */
    public function test_admin_can_update_room_status_directly(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $roomType = RoomType::create([
            'name' => 'Deluxe Suite',
            'description' => 'A grand room',
            'base_price' => 150.00,
            'capacity' => 2,
            'amenities' => ['WiFi'],
        ]);

        $room = Room::create([
            'room_number' => '303',
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        $response = $this->actingAs($admin)->post("/admin/rooms/{$room->id}/status", [
            'status' => 'maintenance',
        ]);

        $response->assertRedirect();
        $this->assertEquals('maintenance', $room->fresh()->status);
    }

    /**
     * Test payment gateway rendering switches based on setting value.
     */
    public function test_payment_gateway_rendering_switches(): void
    {
        $customer = User::create([
            'name' => 'Arthur Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $roomType = RoomType::create([
            'name' => 'Standard Room',
            'description' => 'Comfortable double bed',
            'base_price' => 100.00,
            'capacity' => 2,
            'amenities' => ['Free Wi-Fi'],
        ]);

        $room = Room::create([
            'room_number' => '101',
            'room_type_id' => $roomType->id,
            'status' => 'available',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::today(),
            'check_out_date' => Carbon::tomorrow(),
            'total_price' => 112.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // 1. Default/Card Simulation switch
        Setting::setValue('active_payment_gateway', 'card_simulation');
        $response = $this->actingAs($customer)->get("/customer/payment/{$booking->id}");
        $response->assertStatus(200);
        $response->assertSee('Process Payment');
        $response->assertSee('Card Holder');

        // 2. Paystack Gateway Switch
        Setting::setValue('active_payment_gateway', 'paystack');
        $response = $this->actingAs($customer)->get("/customer/payment/{$booking->id}");
        $response->assertStatus(200);
        $response->assertSee('Paystack Gateway Active');
        $response->assertSee('Pay via Paystack Popup');

        // 3. Flutterwave Gateway Switch
        Setting::setValue('active_payment_gateway', 'flutterwave');
        $response = $this->actingAs($customer)->get("/customer/payment/{$booking->id}");
        $response->assertStatus(200);
        $response->assertSee('Flutterwave Gateway Active');
        $response->assertSee('Pay via Flutterwave Modal');
    }

    /**
     * Test Admin can update minor CMS settings.
     */
    public function test_admin_can_update_minor_cms_content(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post('/admin/frontend/minor-content', [
            'hero_subtitle' => 'Unmatched sunset views and ocean breeze',
            'welcome_description' => 'A grand sanctuary nestled in nature',
            'contact_phone' => '111-222-3333',
            'contact_email' => 'info@newresort.com',
            'global_header' => 'Announcement bar text',
            'global_footer' => 'Footer copy text',
            'testimonials_list' => '[]',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Unmatched sunset views and ocean breeze', Setting::getValue('hero_subtitle'));
        $this->assertEquals('111-222-3333', Setting::getValue('contact_phone'));
    }

    /**
     * Test Admin can update slide minor details.
     */
    public function test_admin_can_update_slide_minor_details(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $slide = \App\Models\HeroSlide::create([
            'image_path' => '/uploads/test.jpg',
            'title' => 'Initial Title',
            'subtitle' => 'Initial Subtitle',
            'button_text' => 'Click Here',
            'button_link' => '/link',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/frontend/slider/{$slide->id}", [
            'title' => 'Updated Slide Title',
            'subtitle' => 'Updated Slide Subtitle',
            'button_text' => 'New Button Text',
            'button_link' => '/new-link',
            'sort_order' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $slide = $slide->fresh();
        $this->assertEquals('Updated Slide Title', $slide->title);
        $this->assertEquals('Updated Slide Subtitle', $slide->subtitle);
        $this->assertEquals(2, $slide->sort_order);
        $this->assertTrue($slide->is_active);
    }

    /**
     * Test User profile editing.
     */
    public function test_user_can_view_and_update_profile(): void
    {
        $user = User::create([
            'name' => 'Profile User',
            'email' => 'profile@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // 1. View profile page
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profile User');

        // 2. Update profile details
        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Profile User',
            'email' => 'updated_profile@test.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $user = $user->fresh();
        $this->assertEquals('Updated Profile User', $user->name);
        $this->assertEquals('updated_profile@test.com', $user->email);
        $this->assertTrue(\Hash::check('newpassword123', $user->password));
    }

    /**
     * Test Super Admin can update another user's details and role.
     */
    public function test_super_admin_can_update_user_role_and_details(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Staff Member',
            'email' => 'staff_member@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $response = $this->actingAs($superAdmin)->post(route('super_admin.users.update', $user->id), [
            'name' => 'Promoted Staff Member',
            'email' => 'promoted_staff@test.com',
            'role' => 'admin',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $user = $user->fresh();
        $this->assertEquals('Promoted Staff Member', $user->name);
        $this->assertEquals('promoted_staff@test.com', $user->email);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue(\Hash::check('newpassword123', $user->password));
    }

    /**
     * Test Custom Pages CMS CRUD and guest rendering.
     */
    public function test_pages_cms_crud_and_rendering(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // 1. Create a page
        $response = $this->actingAs($superAdmin)->post(route('super_admin.pages.store'), [
            'title' => 'Test CMS Page',
            'slug' => 'test-cms-page',
            'content' => '<p>This is a test dynamic page content.</p>',
            'is_active' => '1',
            'show_in_nav' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'slug' => 'test-cms-page',
            'title' => 'Test CMS Page',
            'is_active' => true,
        ]);

        $page = \App\Models\Page::where('slug', 'test-cms-page')->first();
        $this->assertNotNull($page);

        // 2. Render the page publicly
        $response = $this->get(route('frontend.page', 'test-cms-page'));
        $response->assertStatus(200);
        $response->assertSee('Test CMS Page');
        $response->assertSee('This is a test dynamic page content.');

        // 3. Update the page
        $response = $this->actingAs($superAdmin)->post(route('super_admin.pages.update', $page->id), [
            'title' => 'Updated CMS Page',
            'slug' => 'updated-cms-page',
            'content' => 'Updated content text.',
            'is_active' => '1',
            // Omitting show_in_nav since it's unchecked
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'slug' => 'updated-cms-page',
            'title' => 'Updated CMS Page',
            'show_in_nav' => false,
        ]);

        // 4. Delete the page
        $response = $this->actingAs($superAdmin)->post(route('super_admin.pages.delete', $page->id));
        $response->assertRedirect();
        $this->assertNull(\App\Models\Page::find($page->id));
    }

    /**
     * Test Admin can upload room type images.
     */
    public function test_admin_can_upload_room_type_images(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $image1 = \Illuminate\Http\UploadedFile::fake()->image('room_deluxe_1.jpg');
        $image2 = \Illuminate\Http\UploadedFile::fake()->image('room_deluxe_2.jpg');

        $response = $this->actingAs($admin)->post('/admin/room-types', [
            'name' => 'Deluxe Suite Images Test',
            'description' => 'Image upload testing',
            'base_price' => '300.00',
            'capacity' => 2,
            'amenities' => ['WiFi'],
            'images' => [$image1, $image2]
        ]);

        $response->assertRedirect();
        $roomType = RoomType::where('name', 'Deluxe Suite Images Test')->first();
        $this->assertNotNull($roomType);
        $this->assertCount(2, $roomType->images);
        $this->assertStringContainsString('/uploads/rooms/room_', $roomType->images[0]);

        // Cleanup uploaded files
        foreach ($roomType->images as $imgPath) {
            $fullPath = public_path($imgPath);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    /**
     * Test Admin can access all 8 of their dashboard menus.
     */
    public function test_admin_dashboard_menus_access(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $routes = [
            'admin.dashboard',
            'admin.rooms',
            'admin.bookings',
            'admin.guests',
            'admin.payments',
            'admin.reports',
            'admin.users',
            'admin.settings',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($admin)->get(route($routeName));
            $response->assertStatus(200);
        }
    }
}

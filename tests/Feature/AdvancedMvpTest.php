<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Page;
use Plugins\EcommerceTuckShop\Models\Product;
use Plugins\EcommerceTuckShop\Models\Order;
use Plugins\EcommerceTuckShop\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdvancedMvpTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $admin;
    protected $receptionist;
    protected $kitchenManager;
    protected $customer;
    protected $roomType;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::setValue('hotel_name', 'StayFlow Luxury Hotel');
        Setting::setValue('currency', 'USD');
        Setting::setValue('tax_rate', '12');
        Setting::setValue('check_in_time', '14:00');
        Setting::setValue('check_out_time', '11:00');
        Setting::setValue('ecommerce_shipping_fee', '0.00');

        // Create different roles
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->admin = User::create([
            'name' => 'Hotel Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->receptionist = User::create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@test.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'status' => 'active',
        ]);

        $this->kitchenManager = User::create([
            'name' => 'Chef User',
            'email' => 'chef@test.com',
            'password' => bcrypt('password'),
            'role' => 'kitchen_manager',
            'status' => 'active',
        ]);

        $this->customer = User::create([
            'name' => 'Jane Guest',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
            'loyalty_points' => 100, // Starts with 100 points ($10 value)
        ]);

        // Create standard room type and room
        $this->roomType = RoomType::create([
            'name' => 'Suite Room',
            'description' => 'A luxury suite room',
            'base_price' => 200.00,
            'capacity' => 4,
            'amenities' => ['WiFi', 'TV', 'MiniBar'],
        ]);

        $this->room = Room::create([
            'room_number' => '201',
            'room_type_id' => $this->roomType->id,
            'status' => 'available',
        ]);
    }

    protected function tearDown(): void
    {
        \App\Providers\PluginServiceProvider::$testDisabled = false;
        parent::tearDown();
    }

    /**
     * Test user impersonation.
     */
    public function test_super_admin_can_impersonate_user_and_leave(): void
    {
        // 1. Try to impersonate as Super Admin
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/users/{$this->customer->id}/impersonate");
        $response->assertRedirect('/customer/dashboard');
        $response->assertSessionHas('impersonated_by', $this->superAdmin->id);

        $this->assertEquals($this->customer->id, auth()->id());

        // 2. Leave impersonation
        $response = $this->post('/impersonate/leave');
        $response->assertRedirect('/super-admin/dashboard');
        $response->assertSessionMissing('impersonated_by');

        $this->assertEquals($this->superAdmin->id, auth()->id());
    }

    /**
     * Test SEO settings.
     */
    public function test_seo_page_configurations(): void
    {
        // 1. Create a page with SEO tags as Super Admin
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/pages', [
            'title' => 'Test SEO Page',
            'slug' => 'test-seo-page',
            'content' => '<p>This page is SEO friendly</p>',
            'is_active' => '1',
            'show_in_nav' => '1',
            'meta_title' => 'SEO Title Tag',
            'meta_description' => 'SEO Description Tag content',
            'meta_keywords' => 'keyword1, keyword2, keyword3',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'slug' => 'test-seo-page',
            'meta_title' => 'SEO Title Tag',
            'meta_description' => 'SEO Description Tag content',
            'meta_keywords' => 'keyword1, keyword2, keyword3',
        ]);

        // 2. View the page frontend and see if it outputs correct headers
        $response = $this->get('/pages/test-seo-page');
        $response->assertStatus(200);
        $response->assertSee('SEO Title Tag');
        $response->assertSee('SEO Description Tag content');
        $response->assertSee('keyword1, keyword2, keyword3');
    }

    /**
     * Test E-commerce cart session actions and checking out.
     */
    public function test_ecommerce_cart_actions(): void
    {
        // Create products
        $item1 = Product::create([
            'name' => 'Tuck Shop Chips',
            'description' => 'Yummy potato chips',
            'price' => 5.00,
            'type' => 'tuck_shop',
            'is_available' => true,
        ]);

        $item2 = Product::create([
            'name' => 'Restaurant Pizza',
            'description' => 'Delicious Pepperoni Pizza',
            'price' => 15.00,
            'type' => 'restaurant',
            'is_available' => true,
        ]);

        // 1. Add item to cart
        $response = $this->post('/cart/add', [
            'product_id' => $item1->id,
            'quantity' => 2,
        ]);
        $response->assertRedirect();
        
        $cart = session()->get('cart');
        $this->assertNotNull($cart);
        $this->assertArrayHasKey($item1->id, $cart);
        $this->assertEquals(2, $cart[$item1->id]['quantity']);

        // 2. Add second item
        $response = $this->post('/cart/add', [
            'product_id' => $item2->id,
            'quantity' => 1,
        ]);
        $response->assertRedirect();

        $cart = session()->get('cart');
        $this->assertArrayHasKey($item2->id, $cart);
        $this->assertEquals(1, $cart[$item2->id]['quantity']);

        // 3. Remove first item
        $response = $this->post('/cart/remove', [
            'product_id' => $item1->id,
        ]);
        $response->assertRedirect();

        $cart = session()->get('cart');
        $this->assertArrayNotHasKey($item1->id, $cart);
        $this->assertArrayHasKey($item2->id, $cart);
    }

    /**
     * Test public checkout (non-guest / guest Cash on Delivery / Card).
     */
    public function test_ecommerce_public_checkout_card_payment(): void
    {
        $pizza = Product::create([
            'name' => 'Restaurant Pizza',
            'description' => 'Pizza',
            'price' => 15.00,
            'type' => 'restaurant',
            'is_available' => true,
        ]);

        // Add to cart
        $this->post('/cart/add', [
            'product_id' => $pizza->id,
            'quantity' => 2, // Subtotal = 30.00
        ]);

        // Checkout via Credit Card
        $response = $this->post('/checkout', [
            'customer_name' => 'Walk-in Buyer',
            'customer_email' => 'walkin@example.com',
            'customer_phone' => '1234567890',
            'delivery_type' => 'takeaway',
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('/shop');
        $response->assertSessionHas('success');
        
        // Tax rate is 12%, subtotal 30.00, total = 33.60
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Walk-in Buyer',
            'total_price' => 33.60,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'status' => 'pending',
        ]);

        // Verify cart is cleared
        $this->assertNull(session()->get('cart'));
    }

    /**
     * Test active guest guest checkout with "Charge to Room".
     */
    public function test_checked_in_guest_can_charge_to_room(): void
    {
        // 1. Create a confirmed and checked-in booking for customer
        $booking = Booking::create([
            'customer_id' => $this->customer->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today(),
            'check_out_date' => Carbon::tomorrow(),
            'total_price' => 224.00, // 200 room + 12% tax
            'status' => 'checked_in',
            'payment_status' => 'paid',
        ]);

        // 2. Add product to cart
        $coke = Product::create([
            'name' => 'Tuck Shop Coca-Cola',
            'price' => 3.00,
            'type' => 'tuck_shop',
            'is_available' => true,
        ]);

        $this->actingAs($this->customer)->post('/cart/add', [
            'product_id' => $coke->id,
            'quantity' => 3, // Subtotal = 9.00
        ]);

        // 3. Checkout with room_charge
        $response = $this->actingAs($this->customer)->post('/checkout', [
            'customer_name' => 'Jane Guest',
            'customer_email' => 'customer@test.com',
            'customer_phone' => '987654321',
            'delivery_type' => 'room',
            'delivery_details' => 'Room 201',
            'payment_method' => 'room_charge',
        ]);

        $response->assertRedirect('/shop');
        
        // Tax = 1.08, total = 10.08
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Guest',
            'booking_id' => $booking->id,
            'total_price' => 10.08,
            'payment_method' => 'room_charge',
            'payment_status' => 'unpaid',
        ]);

        // 4. Verify booking total price incremented by 10.08
        $this->assertEquals(234.08, $booking->fresh()->total_price);
    }

    /**
     * Test loyalty points calculations, earnings, and redemptions on bookings.
     */
    public function test_loyalty_points_flow(): void
    {
        // Points redemption logic:
        // Customer has 100 points ($10 value).
        // Standard price is 200.00.
        // Tax rate is 12%.
        // Coupon discount is also applied if given, but let's test loyalty only first.
        // Subtotal = 200.00
        // Redeem Loyalty checkbox checked: 100 points = $10 discount.
        // Discounted Subtotal = 190.00.
        // Tax = 190.00 * 12% = 22.80.
        // Total price = 212.80.

        // 1. Process booking with loyalty points checked
        $response = $this->actingAs($this->customer)->post('/customer/book', [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'redeem_loyalty' => '1',
        ]);

        // Redirects to payment page
        $booking = Booking::orderBy('id', 'desc')->first();
        $response->assertRedirect("/customer/payment/{$booking->id}");

        $this->assertEquals(100, $booking->loyalty_points_redeemed);
        $this->assertEquals(10.00, $booking->loyalty_discount_amount);
        $this->assertEquals(212.80, $booking->total_price);

        // Verify customer loyalty points decremented instantly
        $this->assertEquals(0, $this->customer->fresh()->loyalty_points);

        // 2. Perform payment, which triggers points earning (1 pt per $10 spent on final booking price)
        // 212.80 / 10 = 21 points earned
        $response = $this->actingAs($this->customer)->post("/customer/payment/{$booking->id}", [
            'card_name' => 'Jane Guest',
            'card_number' => '1111222233334444',
            'card_expiry' => '12/28',
            'card_cvv' => '123',
        ]);

        $response->assertRedirect('/customer/bookings');
        $this->assertEquals('paid', $booking->fresh()->payment_status);
        $this->assertEquals(21, $booking->fresh()->loyalty_points_earned);

        // Verify customer loyalty points credited
        $this->assertEquals(21, $this->customer->fresh()->loyalty_points);
    }

    /**
     * Test booking with add-ons.
     */
    public function test_booking_with_addons(): void
    {
        // 1. Process booking with Add-ons: Airport Shuttle ($30) and Gourmet Breakfast ($20 per night)
        // Subtotal = 200.00
        // Add-ons = 30 + (20 * 1 night) = 50.00
        // finalSubtotal = 250.00
        // Tax = 250 * 12% = 30.00
        // Total = 280.00
        $response = $this->actingAs($this->customer)->post('/customer/book', [
            'room_type_id' => $this->roomType->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'addons' => ['airport_shuttle', 'gourmet_breakfast'],
        ]);

        $booking = Booking::orderBy('id', 'desc')->first();
        $response->assertRedirect("/customer/payment/{$booking->id}");

        $this->assertEquals(280.00, $booking->total_price);
        $this->assertNotNull($booking->addons);
        
        $decodedAddons = json_decode($booking->addons, true);
        $this->assertCount(2, $decodedAddons);
        $this->assertEquals('Airport Shuttle', $decodedAddons[0]['name']);
        $this->assertEquals('Gourmet Breakfast', $decodedAddons[1]['name']);
    }

    /**
     * Test Kitchen Manager dashboard and order status updates.
     */
    public function test_kitchen_manager_dashboard_flow(): void
    {
        // Create an order containing restaurant items
        $pizza = Product::create([
            'name' => 'Restaurant Pizza',
            'price' => 15.00,
            'type' => 'restaurant',
            'is_available' => true,
        ]);

        $order = Order::create([
            'customer_name' => 'Guest in 202',
            'delivery_type' => 'room',
            'delivery_details' => 'Room 202',
            'total_price' => 16.80,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $pizza->id,
            'quantity' => 1,
            'price' => 15.00,
        ]);

        // 1. Access dashboard as chef (kitchen_manager role)
        $response = $this->actingAs($this->kitchenManager)->get('/kitchen/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Chef User');
        $response->assertSee('Guest in 202');
        $response->assertSee('Room 202');
        $response->assertSee('Restaurant Pizza');

        // 2. Update status to preparing
        $response = $this->actingAs($this->kitchenManager)->post("/kitchen/orders/{$order->id}/status", [
            'status' => 'preparing',
        ]);
        $response->assertRedirect('/kitchen/dashboard');
        $this->assertEquals('preparing', $order->fresh()->status);

        // 3. Update status to delivered (since CoD, it should transition payment_status to paid)
        $response = $this->actingAs($this->kitchenManager)->post("/kitchen/orders/{$order->id}/status", [
            'status' => 'delivered',
        ]);
        $response->assertRedirect('/kitchen/dashboard');
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    /**
     * Test dynamic function permissions and routing for custom roles.
     */
    public function test_custom_role_permissions_and_routing(): void
    {
        // 1. Create a custom role 'laundry_man' with manage_rooms permission
        $laundryMan = User::create([
            'name' => 'Laundry Specialist',
            'email' => 'laundry@test.com',
            'password' => bcrypt('password'),
            'role' => 'laundry_man',
            'status' => 'active',
            'functions' => ['manage_rooms'],
        ]);

        // Check if hasFunction resolves manage_rooms to true, but others to false
        $this->assertTrue($laundryMan->hasFunction('manage_rooms'));
        $this->assertFalse($laundryMan->hasFunction('manage_guests'));

        // 2. Try to access rooms page with laundry_man account (Should allow)
        $response = $this->actingAs($laundryMan)->get('/admin/rooms');
        $response->assertStatus(200);

        // 3. Try to access bookings page with laundry_man account (Should deny since role doesn't have manage_bookings)
        $response = $this->actingAs($laundryMan)->get('/admin/bookings');
        $response->assertStatus(403);

        // 4. Test redirection fallback on login for custom role
        auth()->logout();
        $response = $this->post('/login', [
            'email' => 'laundry@test.com',
            'password' => 'password',
        ]);
        $response->assertRedirect('/admin/rooms');
        $this->get('/admin/rooms')->assertStatus(200);
    }

    /**
     * Test Tuck Shop Manager and Restaurant Manager scoping for products and orders.
     */
    public function test_manager_scoping_for_products_and_orders(): void
    {
        // Create tuck shop manager and restaurant manager users
        $tuckShopManager = User::create([
            'name' => 'Tuck Shop Manager',
            'email' => 'tuck@test.com',
            'password' => bcrypt('password'),
            'role' => 'tuck_shop_manager',
            'status' => 'active',
        ]);

        $restaurantManager = User::create([
            'name' => 'Restaurant Manager',
            'email' => 'restaurant@test.com',
            'password' => bcrypt('password'),
            'role' => 'restaurant_manager',
            'status' => 'active',
        ]);

        // Create a tuck_shop product and a restaurant product
        $tuckProduct = Product::create([
            'name' => 'Tuck Chips',
            'price' => 2.50,
            'type' => 'tuck_shop',
            'is_available' => true,
        ]);

        $restProduct = Product::create([
            'name' => 'Rest Pizza',
            'price' => 12.00,
            'type' => 'restaurant',
            'is_available' => true,
        ]);

        // 1. Tuck shop manager fetches products list - should only see tuck shop products
        $response = $this->actingAs($tuckShopManager)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Tuck Chips');
        $response->assertDontSee('Rest Pizza');

        // 2. Restaurant manager fetches products list - should only see restaurant products
        $response = $this->actingAs($restaurantManager)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Rest Pizza');
        $response->assertDontSee('Tuck Chips');

        // 3. Tuck shop manager tries to create a restaurant product - should be blocked
        $response = $this->actingAs($tuckShopManager)->post('/admin/products', [
            'name' => 'Illegal Pizza',
            'price' => 15.00,
            'type' => 'restaurant',
            'is_available' => 1,
        ]);
        $response->assertStatus(403);

        // 4. Test storing tuck shop product as Tuck Shop manager (should succeed)
        $response = $this->actingAs($tuckShopManager)->post('/admin/products', [
            'name' => 'Legal Soda',
            'price' => 3.00,
            'type' => 'tuck_shop',
            'is_available' => 1,
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Legal Soda',
            'type' => 'tuck_shop',
        ]);
    }

    /**
     * Test plugin dynamic registration, QA execution, and enabling/disabling.
     */
    public function test_plugin_dynamic_registration_and_qa(): void
    {
        // 1. Create a plugin record in the database
        $plugin = \App\Models\Plugin::create([
            'name' => 'EcommerceTuckShop',
            'version' => '1.0.0',
            'description' => 'E-commerce and Tuck Shop/Restaurant system for guest and public ordering.',
            'service_provider' => 'Plugins\\EcommerceTuckShop\\EcommerceTuckShopServiceProvider',
            'is_enabled' => true,
        ]);

        // 2. Access the plugins dashboard as Super Admin - should see the plugin
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/plugins');
        $response->assertStatus(200);
        $response->assertSee('EcommerceTuckShop');

        // 3. Run the QA suite - should return JSON with 100% pass (all 5 checks)
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/plugins/{$plugin->id}/qa");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results' => [
                '*' => [
                    'name',
                    'passed',
                    'message'
                ]
            ]
        ]);
        
        $data = $response->json('results');
        $this->assertCount(5, $data);
        foreach ($data as $check) {
            $this->assertTrue($check['passed'], "QA assertion failed: " . $check['name'] . " - " . $check['message']);
        }

        // 4. Toggle the is_enabled state - should redirect back and change state
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/plugins/{$plugin->id}/toggle");
        $response->assertStatus(302);
        $this->assertFalse($plugin->fresh()->is_enabled);
    }

    /**
     * Test that when the plugin is disabled, accessing plugin routes returns 404
     * and the core system/home page loads successfully without routing errors.
     */
    public function test_system_without_plugin_routes(): void
    {
        // 1. Force the plugin configuration to be test_disabled via static variable
        \App\Providers\PluginServiceProvider::$testDisabled = true;

        // 2. Refresh the application to reload service providers and routes without the plugin
        $this->refreshApplication();

        // 2.5. Re-run migrations since refreshing the application drops the SQLite in-memory connection
        $this->artisan('migrate');

        // 3. Try to access shop index - should return 404 since routes are not registered
        $response = $this->get('/shop');
        $response->assertStatus(404);

        // 4. Try to access restaurant index - should return 404
        $response = $this->get('/restaurant');
        $response->assertStatus(404);

        // 5. Try to access admin/products - should return 404
        $response = $this->actingAs($this->superAdmin)->get('/admin/products');
        $response->assertStatus(404);

        // 6. Access core home page - should load successfully (status 200)
        $response = $this->get('/');
        $response->assertStatus(200);

        // Reset the static variable to ensure it doesn't leak into subsequent tests
        \App\Providers\PluginServiceProvider::$testDisabled = false;
    }

    /**
     * Test walk-in booking by admin.
     */
    public function test_walkin_booking_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/bookings/walkin', [
            'customer_type' => 'new',
            'name' => 'Admin Walkin Guest',
            'email' => 'admin_walkin@test.com',
            'phone' => '555-0199',
            'nationality' => 'UK',
            'room_type_id' => $this->roomType->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'payment_received' => '1',
            'payment_method' => 'Cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'admin_walkin@test.com',
            'role' => 'customer',
        ]);

        $this->assertDatabaseHas('bookings', [
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);
    }

    /**
     * Test walk-in booking by receptionist.
     */
    public function test_walkin_booking_by_receptionist(): void
    {
        $response = $this->actingAs($this->receptionist)->post('/receptionist/bookings/walkin', [
            'customer_type' => 'existing',
            'customer_id' => $this->customer->id,
            'room_type_id' => $this->roomType->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'payment_received' => '1',
            'payment_method' => 'Card',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);
    }

    /**
     * Test settings hardening for standard admin vs super admin.
     */
    public function test_admin_settings_hardening(): void
    {
        // 1. Standard admin tries to update settings with SMTP/Gateway credentials
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'hotel_name' => 'StayFlow Luxury Hotel New',
            'currency' => 'USD',
            'tax_rate' => '12',
            'check_in_time' => '14:00',
            'check_out_time' => '11:00',
            'contact_email' => 'admin@test.com',
            'contact_phone' => '1234567890',
            // SMTP/Gateway keys:
            'mail_host' => 'smtp.hacker.com',
            'paystack_secret_key' => 'sk_hacked',
        ]);

        $response->assertRedirect();
        $this->assertEquals('StayFlow Luxury Hotel New', Setting::getValue('hotel_name'));
        $this->assertNull(Setting::getValue('mail_host'));
        $this->assertNull(Setting::getValue('paystack_secret_key'));

        // 2. Super admin updates settings with SMTP/Gateway credentials (should succeed)
        $response = $this->actingAs($this->superAdmin)->post('/admin/settings', [
            'hotel_name' => 'StayFlow Luxury Hotel Super',
            'currency' => 'USD',
            'tax_rate' => '12',
            'check_in_time' => '14:00',
            'check_out_time' => '11:00',
            'contact_email' => 'admin@test.com',
            'contact_phone' => '1234567890',
            'active_payment_gateway' => 'card_simulation',
            'mail_host' => 'smtp.legit.com',
            'paystack_secret_key' => 'sk_legit',
        ]);

        $response->assertRedirect();
        $this->assertEquals('smtp.legit.com', Setting::getValue('mail_host'));
        $this->assertEquals('sk_legit', Setting::getValue('paystack_secret_key'));
    }

    /**
     * Test e-commerce standalone settings and checkout calculations.
     */
    public function test_ecommerce_standalone_settings(): void
    {
        // 1. Update standalone e-commerce settings
        $response = $this->actingAs($this->superAdmin)->post('/admin/ecommerce/settings', [
            'ecommerce_store_name' => 'Custom Shop Name',
            'ecommerce_store_email' => 'custom@shop.com',
            'ecommerce_shipping_fee' => '10.00',
            'ecommerce_free_shipping_limit' => '100.00',
            'ecommerce_tax_rate' => '15.00',
            'ecommerce_store_status' => 'open',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Custom Shop Name', Setting::getValue('ecommerce_store_name'));
        $this->assertEquals('10.00', Setting::getValue('ecommerce_shipping_fee'));
        $this->assertEquals('15.00', Setting::getValue('ecommerce_tax_rate'));

        // 2. Create product
        $product = Product::create([
            'name' => 'Premium Goods',
            'price' => 50.00,
            'type' => 'tuck_shop',
            'is_available' => true,
        ]);

        // 3. Add to cart
        $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1, // Subtotal = 50.00
        ]);

        // 4. Checkout via Card (payment_status becomes paid)
        // Subtotal = 50.00 (< 100 limit, so 10.00 shipping fee applies)
        // Tax = 50 * 15% = 7.50
        // Total = 50 + 10 + 7.5 = 67.50
        $response = $this->post('/checkout', [
            'customer_name' => 'John Custom',
            'customer_email' => 'john@custom.com',
            'customer_phone' => '123456789',
            'delivery_type' => 'takeaway',
            'payment_method' => 'card',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'John Custom',
            'total_price' => 67.50,
            'payment_method' => 'card',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Test plugin deletion and dynamic database purge.
     */
    public function test_plugin_deletion_db_purge(): void
    {
        $plugin = \App\Models\Plugin::create([
            'name' => 'EcommerceTuckShop',
            'version' => '1.0.0',
            'description' => 'E-commerce and Tuck Shop/Restaurant system for guest and public ordering.',
            'service_provider' => 'Plugins\\EcommerceTuckShop\\EcommerceTuckShopServiceProvider',
            'is_enabled' => true,
        ]);

        \Illuminate\Support\Facades\DB::table('migrations')->insert([
            'migration' => '2026_05_22_000000_create_shop_and_restaurant_tables',
            'batch' => 1
        ]);

        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('products'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('orders'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('order_items'));

        \Illuminate\Support\Facades\File::partialMock()
            ->shouldReceive('exists')
            ->with(base_path('plugins/EcommerceTuckShop'))
            ->andReturn(true);
        \Illuminate\Support\Facades\File::partialMock()
            ->shouldReceive('deleteDirectory')
            ->with(base_path('plugins/EcommerceTuckShop'))
            ->andReturn(true);

        $response = $this->actingAs($this->superAdmin)->post("/super-admin/plugins/{$plugin->id}/delete");
        $response->assertRedirect();

        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('products'));
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('orders'));
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('order_items'));

        $this->assertDatabaseMissing('migrations', [
            'migration' => '2026_05_22_000000_create_shop_and_restaurant_tables'
        ]);

        $this->assertDatabaseMissing('plugins', [
            'name' => 'EcommerceTuckShop'
        ]);

        // Restore tables for other tests
        \Illuminate\Support\Facades\Artisan::call('migrate');
    }
}


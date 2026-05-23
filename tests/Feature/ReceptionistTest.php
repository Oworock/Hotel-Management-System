<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReceptionistTest extends TestCase
{
    use RefreshDatabase;

    protected $receptionist;
    protected $roomType;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::setValue('hotel_name', 'Test Aetheria HMS');
        Setting::setValue('currency', 'USD');
        Setting::setValue('tax_rate', '12');
        Setting::setValue('check_in_time', '14:00');
        Setting::setValue('check_out_time', '11:00');

        // Create a default receptionist
        $this->receptionist = User::create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@test.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'status' => 'active',
        ]);

        // Create a standard room type and room
        $this->roomType = RoomType::create([
            'name' => 'Standard Room',
            'description' => 'A cozy standard room',
            'base_price' => 100.00,
            'capacity' => 2,
            'amenities' => ['WiFi', 'TV'],
        ]);

        $this->room = Room::create([
            'room_number' => '101',
            'room_type_id' => $this->roomType->id,
            'status' => 'available',
        ]);
    }

    /**
     * Test login redirects receptionist correctly.
     */
    public function test_receptionist_login_redirect(): void
    {
        $response = $this->post('/login', [
            'email' => 'receptionist@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/receptionist/dashboard');
        $this->get('/receptionist/dashboard')->assertStatus(200);
    }

    /**
     * Test receptionist dashboard metrics.
     */
    public function test_receptionist_dashboard_metrics(): void
    {
        $today = Carbon::today();
        
        $customer = User::create([
            'name' => 'John Guest',
            'email' => 'guest@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        // Create booking arriving today
        $bookingArrival = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'check_in_date' => $today->toDateString(),
            'check_out_date' => $today->copy()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'total_price' => 200.00,
        ]);

        // Create booking departing today (already checked in)
        $dirtyRoom = Room::create([
            'room_number' => '102',
            'room_type_id' => $this->roomType->id,
            'status' => 'booked',
        ]);

        $bookingDeparture = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $dirtyRoom->id,
            'check_in_date' => $today->copy()->subDays(2)->toDateString(),
            'check_out_date' => $today->toDateString(),
            'status' => 'checked_in',
            'payment_status' => 'paid',
            'total_price' => 200.00,
        ]);

        $response = $this->actingAs($this->receptionist)->get('/receptionist/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Receptionist User');
        $response->assertSee('John Guest');
        
        // Assert the stats variables are passed to view
        $response->assertViewHas('arrivalsToday', 1);
        $response->assertViewHas('departuresToday', 1);
        $response->assertViewHas('activeCheckins', 1);
    }

    /**
     * Test access controls / receptionist cannot access admin or superadmin routes.
     */
    public function test_receptionist_cannot_access_unauthorized_routes(): void
    {
        // 1. Try to access Super Admin dashboard
        $response = $this->actingAs($this->receptionist)->get('/super-admin/dashboard');
        $response->assertStatus(403);

        // 2. Try to access Super Admin settings
        $response = $this->actingAs($this->receptionist)->get('/super-admin/settings');
        $response->assertStatus(403);

        // 3. Try to access Admin dashboard
        $response = $this->actingAs($this->receptionist)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test receptionist can check in a guest (only if paid).
     */
    public function test_receptionist_can_check_in_guest_if_paid(): void
    {
        $customer = User::create([
            'name' => 'John Guest',
            'email' => 'guest@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'total_price' => 100.00,
        ]);

        // 1. Trying to check in without payment should fail
        $response = $this->actingAs($this->receptionist)->post("/receptionist/bookings/{$booking->id}/check-in");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('confirmed', $booking->fresh()->status);
        $this->assertEquals('available', $this->room->fresh()->status);

        // 2. Pay for the booking
        $booking->update(['payment_status' => 'paid']);

        // 3. Perform check-in
        $response = $this->actingAs($this->receptionist)->post("/receptionist/bookings/{$booking->id}/check-in");
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('checked_in', $booking->fresh()->status);
        $this->assertEquals('booked', $this->room->fresh()->status);
    }

    /**
     * Test receptionist can check out a guest.
     */
    public function test_receptionist_can_check_out_guest(): void
    {
        $customer = User::create([
            'name' => 'John Guest',
            'email' => 'guest@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->room->update(['status' => 'booked']);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::yesterday()->toDateString(),
            'check_out_date' => Carbon::today()->toDateString(),
            'status' => 'checked_in',
            'payment_status' => 'paid',
            'total_price' => 100.00,
        ]);

        $response = $this->actingAs($this->receptionist)->post("/receptionist/bookings/{$booking->id}/check-out");
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('checked_out', $booking->fresh()->status);
        $this->assertEquals('dirty', $this->room->fresh()->status);
    }

    /**
     * Test receptionist can cancel a booking.
     */
    public function test_receptionist_can_cancel_booking(): void
    {
        $customer = User::create([
            'name' => 'John Guest',
            'email' => 'guest@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_price' => 100.00,
        ]);

        $response = $this->actingAs($this->receptionist)->post("/receptionist/bookings/{$booking->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('available', $this->room->fresh()->status);
    }

    /**
     * Test receptionist can record booking payment.
     */
    public function test_receptionist_can_record_payment(): void
    {
        $customer = User::create([
            'name' => 'John Guest',
            'email' => 'guest@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->toDateString(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_price' => 125.50,
        ]);

        $response = $this->actingAs($this->receptionist)->post("/receptionist/bookings/{$booking->id}/pay");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking = $booking->fresh();
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('confirmed', $booking->status);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 125.50,
            'payment_method' => 'Cash / Counter Card',
            'status' => 'completed',
        ]);
    }

    /**
     * Test receptionist can change room maintenance/housekeeping status.
     */
    public function test_receptionist_can_update_room_status(): void
    {
        $this->assertEquals('available', $this->room->status);

        // Update status to dirty
        $response = $this->actingAs($this->receptionist)->post("/receptionist/rooms/{$this->room->id}/status", [
            'status' => 'dirty',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('dirty', $this->room->fresh()->status);

        // Update status to maintenance
        $response = $this->actingAs($this->receptionist)->post("/receptionist/rooms/{$this->room->id}/status", [
            'status' => 'maintenance',
        ]);

        $response->assertRedirect();
        $this->assertEquals('maintenance', $this->room->fresh()->status);
    }
}

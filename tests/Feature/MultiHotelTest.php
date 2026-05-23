<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Plugins\MultiHotel\Models\Hotel;
use Plugins\MultiHotel\QA\PluginQA;

class MultiHotelTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $adminHotelA;
    protected $adminHotelB;
    protected $receptionistHotelA;
    protected $hotelA;
    protected $hotelB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Hotels (default one gets created by migration anyway, but let's retrieve or create them explicitly)
        // Note: The migration runs automatically because it loads when MultiHotelServiceProvider is registered.
        // The migration creates a default hotel. Let's create two distinct ones for our isolation tests.
        $this->hotelA = Hotel::create([
            'name' => 'Paradise Palms Resort',
            'address' => '101 Ocean Drive',
            'phone' => '+1 555-101-1000',
            'email' => 'palms@test.com',
            'is_active' => true,
        ]);

        $this->hotelB = Hotel::create([
            'name' => 'Alpine Peaks Lodge',
            'address' => '202 Mountain Way',
            'phone' => '+1 555-202-2000',
            'email' => 'peaks@test.com',
            'is_active' => true,
        ]);

        // 2. Create Users
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->adminHotelA = new User([
            'name' => 'Manager Hotel A',
            'email' => 'manager_a@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->adminHotelA->hotel_id = $this->hotelA->id;
        $this->adminHotelA->save();

        $this->adminHotelB = new User([
            'name' => 'Manager Hotel B',
            'email' => 'manager_b@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->adminHotelB->hotel_id = $this->hotelB->id;
        $this->adminHotelB->save();

        $this->receptionistHotelA = new User([
            'name' => 'Receptionist Hotel A',
            'email' => 'receptionist_a@test.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'status' => 'active',
        ]);
        $this->receptionistHotelA->hotel_id = $this->hotelA->id;
        $this->receptionistHotelA->save();
    }

    public function test_guests_cannot_access_hotel_manager(): void
    {
        $response = $this->get('/super-admin/hotels');
        $response->assertRedirect('/login');
    }

    public function test_non_super_admins_cannot_access_hotel_manager(): void
    {
        $response = $this->actingAs($this->receptionistHotelA)->get('/super-admin/hotels');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_hotel_manager(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super-admin/hotels');
        $response->assertStatus(200);
        $response->assertSee('Multi-Hotel Property Manager');
    }

    public function test_super_admin_can_create_hotel(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/hotels', [
            'name' => 'Glacier Bay Lodge',
            'address' => '303 Fjords Lane',
            'phone' => '+1 555-303-3000',
            'email' => 'glacier@test.com',
            'description' => 'Cold but cozy lodge.',
        ]);

        $response->assertRedirect(route('super_admin.hotels'));
        $this->assertDatabaseHas('hotels', [
            'name' => 'Glacier Bay Lodge',
            'address' => '303 Fjords Lane',
        ]);
    }

    public function test_super_admin_can_update_hotel(): void
    {
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/hotels/{$this->hotelA->id}/update", [
            'name' => 'Paradise Palms Resort & Spa',
            'address' => '101 Ocean Drive Suite 5',
            'phone' => $this->hotelA->phone,
            'email' => $this->hotelA->email,
            'description' => 'Newly renovated spa inclusion.',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('super_admin.hotels'));
        $this->assertDatabaseHas('hotels', [
            'id' => $this->hotelA->id,
            'name' => 'Paradise Palms Resort & Spa',
        ]);
    }

    public function test_super_admin_switching_hotel_context(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/hotels/select', [
            'hotel_id' => $this->hotelA->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals($this->hotelA->id, session('active_hotel_id'));

        // Switch to all hotels context
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/hotels/select', [
            'hotel_id' => '',
        ]);

        $response->assertRedirect();
        $this->assertNull(session('active_hotel_id'));
    }

    public function test_query_isolation_between_hotel_scopes(): void
    {
        // 1. Create Room Types in each hotel using direct assignment
        $roomTypeA = new RoomType([
            'name' => 'Ocean View King',
            'base_price' => 250,
            'capacity' => 2,
        ]);
        $roomTypeA->hotel_id = $this->hotelA->id;
        $roomTypeA->save();

        $roomTypeB = new RoomType([
            'name' => 'Mountain Lodge Double',
            'base_price' => 180,
            'capacity' => 4,
        ]);
        $roomTypeB->hotel_id = $this->hotelB->id;
        $roomTypeB->save();

        // 2. Query Room Types acting as Hotel A Manager
        $this->actingAs($this->adminHotelA);
        
        $typesForA = RoomType::all();
        $this->assertTrue($typesForA->contains('id', $roomTypeA->id));
        $this->assertFalse($typesForA->contains('id', $roomTypeB->id));

        // 3. Query Room Types acting as Hotel B Manager
        $this->actingAs($this->adminHotelB);
        
        $typesForB = RoomType::all();
        $this->assertFalse($typesForB->contains('id', $roomTypeA->id));
        $this->assertTrue($typesForB->contains('id', $roomTypeB->id));
    }

    public function test_automatic_creation_observer_sets_hotel_id(): void
    {
        // Create RoomType when authenticated as Hotel A Admin
        $this->actingAs($this->adminHotelA);

        $roomType = RoomType::create([
            'name' => 'Auto Scoped Room',
            'base_price' => 150,
            'capacity' => 2,
        ]);

        // Verify hotel_id is automatically associated with Hotel A
        $this->assertEquals($this->hotelA->id, $roomType->hotel_id);
    }

    public function test_super_admin_creates_user_with_assigned_hotel(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/super-admin/users', [
            'name' => 'Hotel A Staff member',
            'email' => 'staff_a_member@test.com',
            'password' => 'password123',
            'role' => 'receptionist',
            'functions' => ['manage_bookings'],
            'hotel_id' => $this->hotelA->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'staff_a_member@test.com',
            'hotel_id' => $this->hotelA->id,
        ]);
    }

    public function test_super_admin_updates_user_assigned_hotel(): void
    {
        $user = User::create([
            'name' => 'Temporary Staff',
            'email' => 'temp_staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->post("/super-admin/users/{$user->id}/update", [
            'name' => 'Temporary Staff Renamed',
            'email' => 'temp_staff@test.com',
            'role' => 'receptionist',
            'functions' => [],
            'hotel_id' => $this->hotelB->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Temporary Staff Renamed',
            'hotel_id' => $this->hotelB->id,
        ]);
    }

    public function test_plugin_qa_runs_successfully(): void
    {
        $results = PluginQA::run();

        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertTrue($result['passed'], "QA compliance check {$result['name']} failed: " . $result['message']);
        }
    }

    public function test_plugin_deletion_cleanup_works(): void
    {
        $plugin = Plugin::create([
            'name' => 'MultiHotel',
            'version' => '1.0.0',
            'description' => 'Multi Hotel Test',
            'service_provider' => 'Plugins\\MultiHotel\\MultiHotelServiceProvider',
            'is_enabled' => true,
        ]);

        // Verify columns and tables exist
        $this->assertTrue(Schema::hasTable('hotels'));
        $this->assertTrue(Schema::hasColumn('users', 'hotel_id'));

        // Delete plugin via endpoint
        $response = $this->actingAs($this->superAdmin)->post("/super-admin/plugins/{$plugin->id}/delete");
        $response->assertRedirect();

        // Verify columns and tables are dropped successfully
        $this->assertFalse(Schema::hasTable('hotels'));
        $this->assertFalse(Schema::hasColumn('users', 'hotel_id'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Plugins\ChannelManager\Models\OtaChannel;
use Plugins\ChannelManager\Models\ChannelRoomMapping;
use Plugins\ChannelManager\Models\ChannelSyncLog;
use Plugins\ChannelManager\QA\PluginQA;

class ChannelManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $admin;
    protected $staffWithPermission;
    protected $staffWithoutPermission;
    protected $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard user roles
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staffWithPermission = User::create([
            'name' => 'Staff With CM Permission',
            'email' => 'staff_cm@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'functions' => ['manage_channel_manager'],
        ]);

        $this->staffWithoutPermission = User::create([
            'name' => 'Staff Without CM Permission',
            'email' => 'staff_no_cm@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
            'functions' => [],
        ]);

        $this->roomType = RoomType::create([
            'name' => 'Suite Room',
            'base_price' => 200.00,
            'capacity' => 4,
        ]);
    }

    public function test_guests_cannot_access_channel_manager(): void
    {
        $response = $this->get('/admin/channel-manager');
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_staff_cannot_access_channel_manager(): void
    {
        $response = $this->actingAs($this->staffWithoutPermission)->get('/admin/channel-manager');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_channel_manager(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/channel-manager');
        $response->assertStatus(200);
        $response->assertSee('Channel Manager (OTA Sync)');
    }

    public function test_admin_can_access_channel_manager(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/channel-manager');
        $response->assertStatus(200);
    }

    public function test_staff_with_permission_can_access_channel_manager(): void
    {
        $response = $this->actingAs($this->staffWithPermission)->get('/admin/channel-manager');
        $response->assertStatus(200);
    }

    public function test_save_settings_updates_ota_credentials(): void
    {
        // Seeding default channels
        $channel = OtaChannel::create([
            'name' => 'Booking.com',
            'is_connected' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/admin/channel-manager/settings', [
            'channel_id' => $channel->id,
            'api_key' => 'booking_api_key_123',
            'api_secret' => 'booking_secret_xyz',
            'hotel_id' => 'hotel_booking_789',
            'is_connected' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ota_channels', [
            'id' => $channel->id,
            'api_key' => 'booking_api_key_123',
            'is_connected' => true,
        ]);

        $this->assertDatabaseHas('channel_sync_logs', [
            'channel_name' => 'Booking.com',
            'sync_type' => 'save_settings',
            'status' => 'success',
        ]);
    }

    public function test_add_mapping_creates_mapping_and_pushes_availability(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/admin/channel-manager/mappings', [
            'room_type_id' => $this->roomType->id,
            'channel_name' => 'Booking.com',
            'ota_room_id' => 'ota_room_suite_001',
            'rate_multiplier' => 1.15,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('channel_room_mappings', [
            'room_type_id' => $this->roomType->id,
            'channel_name' => 'Booking.com',
            'ota_room_id' => 'ota_room_suite_001',
            'rate_multiplier' => 1.15,
        ]);

        // Push availability log should be generated
        $this->assertDatabaseHas('channel_sync_logs', [
            'channel_name' => 'Booking.com',
            'sync_type' => 'push_availability',
            'status' => 'success',
        ]);
    }

    public function test_delete_mapping_deletes_record(): void
    {
        $mapping = ChannelRoomMapping::create([
            'room_type_id' => $this->roomType->id,
            'channel_name' => 'Airbnb',
            'ota_room_id' => 'ota_room_airbnb_001',
            'rate_multiplier' => 1.00,
        ]);

        $response = $this->actingAs($this->superAdmin)->post("/admin/channel-manager/mappings/{$mapping->id}/delete");
        $response->assertRedirect();
        $this->assertDatabaseMissing('channel_room_mappings', [
            'id' => $mapping->id,
        ]);

        $this->assertDatabaseHas('channel_sync_logs', [
            'channel_name' => 'Airbnb',
            'sync_type' => 'delete_mapping',
        ]);
    }

    public function test_manual_sync_all_when_connected(): void
    {
        $channel = OtaChannel::create([
            'name' => 'Booking.com',
            'is_connected' => true,
        ]);

        $mapping = ChannelRoomMapping::create([
            'room_type_id' => $this->roomType->id,
            'channel_name' => 'Booking.com',
            'ota_room_id' => 'ota_room_suite_001',
            'rate_multiplier' => 1.00,
        ]);

        // Create a physical room
        Room::create([
            'room_number' => '109',
            'room_type_id' => $this->roomType->id,
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/admin/channel-manager/sync', [
            'channel_name' => 'Booking.com',
            'sync_type' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if logs are generated for push and pull
        $this->assertDatabaseHas('channel_sync_logs', [
            'channel_name' => 'Booking.com',
            'sync_type' => 'push_availability',
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('channel_sync_logs', [
            'channel_name' => 'Booking.com',
            'sync_type' => 'pull_bookings',
            'status' => 'success',
        ]);
    }

    public function test_plugin_qa_runs_successfully(): void
    {
        // Setup channels
        OtaChannel::create(['name' => 'Booking.com', 'is_connected' => true]);
        OtaChannel::create(['name' => 'Airbnb', 'is_connected' => false]);
        OtaChannel::create(['name' => 'Expedia', 'is_connected' => false]);

        $results = PluginQA::run();

        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertTrue($result['passed'], "QA check {$result['name']} failed: " . $result['message']);
        }
    }

    public function test_super_admin_can_run_channel_manager_qa_endpoint(): void
    {
        // Setup channels
        OtaChannel::create(['name' => 'Booking.com', 'is_connected' => true]);
        OtaChannel::create(['name' => 'Airbnb', 'is_connected' => false]);
        OtaChannel::create(['name' => 'Expedia', 'is_connected' => false]);

        $plugin = \App\Models\Plugin::create([
            'name' => 'ChannelManager',
            'version' => '1.0.0',
            'description' => 'Channel Manager Integration',
            'service_provider' => 'Plugins\\ChannelManager\\ChannelManagerServiceProvider',
            'is_enabled' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post("/super-admin/plugins/{$plugin->id}/qa");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results' => [
                '*' => [
                    'name',
                    'passed',
                    'message',
                ]
            ]
        ]);

        $data = $response->json();
        foreach ($data['results'] as $result) {
            $this->assertTrue($result['passed'], "QA endpoint check {$result['name']} failed: " . $result['message']);
        }
    }
}

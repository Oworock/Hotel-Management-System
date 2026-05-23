<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WebhookSubscription;
use App\Models\WebhookDeliveryLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ApiToken $apiToken;
    protected RoomType $roomType;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard Super Admin user
        $this->user = User::create([
            'name' => 'Developer User',
            'email' => 'dev@stayflow.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        // Create API Token
        $this->apiToken = ApiToken::create([
            'name' => 'Test Token',
            'user_id' => $this->user->id,
            'token' => 'sf_live_testing_token_value_1234567890'
        ]);

        // Create Room Type and Room
        $this->roomType = RoomType::create([
            'name' => 'Deluxe Suite',
            'description' => 'Beautiful suite',
            'base_price' => 150.00,
            'capacity' => 2
        ]);

        $this->room = Room::create([
            'room_number' => '101',
            'room_type_id' => $this->roomType->id,
            'status' => 'available'
        ]);
    }

    public function test_api_requires_bearer_token()
    {
        $response = $this->getJson('/api/v1/rooms');
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized: Missing or invalid Authorization header. Must be a Bearer token.'
        ]);
    }

    public function test_api_rejects_invalid_bearer_token()
    {
        $response = $this->getJson('/api/v1/rooms', [
            'Authorization' => 'Bearer sf_live_invalid_token'
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Unauthorized: Invalid API token.'
        ]);
    }

    public function test_api_allows_valid_token_and_fetches_rooms()
    {
        $response = $this->getJson('/api/v1/rooms', [
            'Authorization' => 'Bearer sf_live_testing_token_value_1234567890'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'room_number', 'room_type_id', 'status']
            ]
        ]);
    }

    public function test_api_can_fetch_single_room()
    {
        $response = $this->getJson("/api/v1/rooms/{$this->room->id}", [
            'Authorization' => 'Bearer sf_live_testing_token_value_1234567890'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $this->room->id,
                'room_number' => '101'
            ]
        ]);
    }

    public function test_api_can_update_room_status()
    {
        $response = $this->putJson("/api/v1/rooms/{$this->room->id}/status", [
            'status' => 'maintenance'
        ], [
            'Authorization' => 'Bearer sf_live_testing_token_value_1234567890'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => $this->room->id,
                'status' => 'maintenance'
            ]
        ]);

        $this->assertEquals('maintenance', $this->room->fresh()->status);
    }

    public function test_api_can_create_booking_successfully()
    {
        $customer = User::create([
            'name' => 'Guest Person',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        $response = $this->postJson('/api/v1/bookings', [
            'customer_id' => $customer->id,
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ], [
            'Authorization' => 'Bearer sf_live_testing_token_value_1234567890'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => ['id', 'total_price', 'status']
        ]);

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'room_id' => $this->room->id,
        ]);
    }

    public function test_webhook_dispatches_and_logs_successfully()
    {
        // Fake outgoing requests
        Http::fake([
            'https://external.example.com/callback' => Http::response('Webhook Received', 200)
        ]);

        // Create Webhook Subscription
        $subscription = WebhookSubscription::create([
            'name' => 'Test Hook',
            'url' => 'https://external.example.com/callback',
            'secret' => 'whsec_test_secret_12345',
            'events' => ['room.status_updated'],
            'is_active' => true
        ]);

        // Trigger event
        $this->room->status = 'dirty';
        $this->room->save();

        // Assert log was created
        $this->assertDatabaseHas('webhook_delivery_logs', [
            'webhook_subscription_id' => $subscription->id,
            'event' => 'room.status_updated',
            'response_status' => 200,
            'response_body' => 'Webhook Received'
        ]);
    }
}

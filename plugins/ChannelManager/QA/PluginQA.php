<?php

namespace Plugins\ChannelManager\QA;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Plugins\ChannelManager\Models\OtaChannel;
use Plugins\ChannelManager\Models\ChannelRoomMapping;
use Plugins\ChannelManager\Models\ChannelSyncLog;
use Plugins\ChannelManager\Services\ChannelSyncService;

class PluginQA
{
    /**
     * Run all QA checks.
     *
     * @return array
     */
    public static function run(): array
    {
        $results = [];

        // 1. Schema Integrity Check
        $results[] = self::checkSchemaIntegrity();

        // 2. Availability Calculation Check
        $results[] = self::checkAvailabilityCalculation();

        // 3. Sync Logging Check
        $results[] = self::checkLoggingIntegrity();

        return $results;
    }

    private static function checkSchemaIntegrity(): array
    {
        $tables = ['ota_channels', 'channel_room_mappings', 'channel_sync_logs'];
        $missing = [];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        if (!empty($missing)) {
            return [
                'name' => 'Database Schema Integrity',
                'passed' => false,
                'message' => 'Missing database tables: ' . implode(', ', $missing),
            ];
        }

        $requiredColumns = [
            'ota_channels' => ['name', 'api_key', 'api_secret', 'hotel_id', 'is_connected'],
            'channel_room_mappings' => ['room_type_id', 'channel_name', 'ota_room_id', 'rate_multiplier'],
            'channel_sync_logs' => ['channel_name', 'sync_type', 'status', 'message', 'payload'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    return [
                        'name' => 'Database Schema Integrity',
                        'passed' => false,
                        'message' => "Table '{$table}' is missing required column: '{$column}'",
                    ];
                }
            }
        }

        return [
            'name' => 'Database Schema Integrity',
            'passed' => true,
            'message' => 'All tables (ota_channels, channel_room_mappings, channel_sync_logs) and required columns exist.',
        ];
    }

    private static function checkAvailabilityCalculation(): array
    {
        DB::beginTransaction();
        try {
            // Create a temporary room type and rooms
            $roomType = RoomType::create([
                'name' => 'QA Sync Temp Room Type',
                'base_price' => 150.00,
                'capacity' => 2,
            ]);

            $room1 = Room::create([
                'room_number' => 'QA-CH-101',
                'room_type_id' => $roomType->id,
                'status' => 'available',
            ]);

            $room2 = Room::create([
                'room_number' => 'QA-CH-102',
                'room_type_id' => $roomType->id,
                'status' => 'available',
            ]);

            // Try calculating availability with no bookings
            $syncService = new ChannelSyncService();
            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDays(2);

            $availableCount = $syncService->calculateAvailability($roomType->id, $startDate, $endDate);

            if ($availableCount !== 2) {
                throw new \Exception("Expected availability to be 2, but calculated: " . $availableCount);
            }

            // Create a mock user
            $user = User::create([
                'name' => 'QA Booking Guest',
                'email' => 'qa_booking_guest_' . time() . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]);

            // Create a booking that overlaps
            Booking::create([
                'customer_id' => $user->id,
                'room_id' => $room1->id,
                'check_in_date' => $startDate,
                'check_out_date' => $endDate,
                'total_price' => 300.00,
                'status' => 'confirmed',
            ]);

            // Re-calculate availability
            $availableCountAfter = $syncService->calculateAvailability($roomType->id, $startDate, $endDate);

            if ($availableCountAfter !== 1) {
                throw new \Exception("Expected availability to be 1 after booking room 1, but calculated: " . $availableCountAfter);
            }

            DB::rollBack();

            return [
                'name' => 'Channel Manager Availability Calculation',
                'passed' => true,
                'message' => 'Availability calculation successfully computed physical rooms, subtracting confirmed/overlapping bookings.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'name' => 'Channel Manager Availability Calculation',
                'passed' => false,
                'message' => 'Availability check failed: ' . $e->getMessage(),
            ];
        }
    }

    private static function checkLoggingIntegrity(): array
    {
        DB::beginTransaction();
        try {
            $syncService = new ChannelSyncService();
            
            // Perform simulated push to an invalid/non-existent mapping
            $invalidMappingId = 99999;
            $success = $syncService->pushAvailability($invalidMappingId);

            if ($success) {
                throw new \Exception("Pushing availability to invalid mapping ID should return false.");
            }

            // Verify a log entry was generated
            $log = ChannelSyncLog::where('sync_type', 'push_availability')
                ->where('status', 'error')
                ->orderBy('id', 'desc')
                ->first();

            if (!$log) {
                throw new \Exception("Sync log entry was not recorded in channel_sync_logs.");
            }

            if (!str_contains($log->message, "Mapping with ID 99999 not found")) {
                throw new \Exception("Expected sync log message to mention mapping not found, got: " . $log->message);
            }

            DB::rollBack();

            return [
                'name' => 'Channel Manager Logging Integrity',
                'passed' => true,
                'message' => 'Channel sync actions correctly log error messages and status in the database.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'name' => 'Channel Manager Logging Integrity',
                'passed' => false,
                'message' => 'Logging check failed: ' . $e->getMessage(),
            ];
        }
    }
}

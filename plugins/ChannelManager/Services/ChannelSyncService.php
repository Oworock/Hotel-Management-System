<?php

namespace Plugins\ChannelManager\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Plugins\ChannelManager\Models\ChannelRoomMapping;
use Plugins\ChannelManager\Models\ChannelSyncLog;
use Plugins\ChannelManager\Models\OtaChannel;

class ChannelSyncService
{
    /**
     * Calculate available rooms of a specific room type within a date range.
     *
     * @param int $roomTypeId
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return int
     */
    public function calculateAvailability($roomTypeId, $startDate, $endDate): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Find booked rooms during this period
        $bookedRoomIds = Booking::where(function ($query) use ($start, $end) {
            $query->where('check_in_date', '<', $end)
                  ->where('check_out_date', '>', $start);
        })->whereIn('status', ['pending', 'confirmed', 'checked_in'])->pluck('room_id');

        // Calculate available count
        return Room::where('room_type_id', $roomTypeId)
            ->whereNotIn('id', $bookedRoomIds)
            ->where('status', 'available')
            ->count();
    }

    /**
     * Simulate pushing availability count of a specific mapping to the OTA.
     *
     * @param int $mappingId
     * @return bool
     */
    public function pushAvailability(int $mappingId): bool
    {
        $mapping = ChannelRoomMapping::with('roomType')->find($mappingId);
        if (!$mapping) {
            ChannelSyncLog::create([
                'channel_name' => 'Unknown',
                'sync_type' => 'push_availability',
                'status' => 'error',
                'message' => "Mapping with ID {$mappingId} not found.",
                'payload' => null,
            ]);
            return false;
        }

        // Calculate for the next 30 days
        $start = Carbon::today();
        $end = Carbon::today()->addDays(30);
        $availableCount = $this->calculateAvailability($mapping->room_type_id, $start, $end);

        // Simulate pushing to OTA
        $payload = [
            'mapping_id' => $mapping->id,
            'room_type_id' => $mapping->room_type_id,
            'ota_room_id' => $mapping->ota_room_id,
            'available_count' => $availableCount,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ];

        ChannelSyncLog::create([
            'channel_name' => $mapping->channel_name,
            'sync_type' => 'push_availability',
            'status' => 'success',
            'message' => "Simulated push successful for mapping ID {$mapping->id}. Pushed availability count: {$availableCount}",
            'payload' => $payload,
        ]);

        return true;
    }

    /**
     * Simulate pulling guest bookings from an OTA channel.
     *
     * @param string $channelName
     * @return array
     */
    public function pullBookings(string $channelName): array
    {
        $channel = OtaChannel::where('name', $channelName)->first();
        if (!$channel || !$channel->is_connected) {
            ChannelSyncLog::create([
                'channel_name' => $channelName,
                'sync_type' => 'pull_bookings',
                'status' => 'error',
                'message' => "Channel {$channelName} is not connected or registered.",
                'payload' => null,
            ]);
            return [];
        }

        $mappings = ChannelRoomMapping::where('channel_name', $channelName)->get();
        if ($mappings->isEmpty()) {
            ChannelSyncLog::create([
                'channel_name' => $channelName,
                'sync_type' => 'pull_bookings',
                'status' => 'success',
                'message' => "No active room mappings found for channel {$channelName}.",
                'payload' => null,
            ]);
            return [];
        }

        // Setup a mock guest customer if none exists
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Mock OTA Guest',
                'email' => 'ota_guest_' . strtolower($channelName) . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'loyalty_points' => 0,
            ]);
        }

        $importedBookings = [];

        foreach ($mappings as $mapping) {
            // Find availability for tomorrow
            $checkIn = Carbon::tomorrow();
            $checkOut = Carbon::tomorrow()->addDays(2);
            $nights = $checkIn->diffInDays($checkOut);

            // Compute availability
            $bookedRoomIds = Booking::where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)
                      ->where('check_out_date', '>', $checkIn);
            })->whereIn('status', ['pending', 'confirmed', 'checked_in'])->pluck('room_id');

            // Find an available physical room
            $room = Room::where('room_type_id', $mapping->room_type_id)
                ->whereNotIn('id', $bookedRoomIds)
                ->where('status', 'available')
                ->first();

            if ($room) {
                // Determine rate multiplier
                $multiplier = $mapping->rate_multiplier ?: 1.00;
                $basePrice = $mapping->roomType->base_price ?? 100.00;
                $totalPrice = round($basePrice * $nights * $multiplier, 2);

                $pointsEarned = (int)floor($totalPrice / 10);

                // Create the booking
                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkOut,
                    'total_price' => $totalPrice,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'loyalty_points_redeemed' => 0,
                    'loyalty_discount_amount' => 0.00,
                    'loyalty_points_earned' => $pointsEarned,
                ]);

                // Increment customer loyalty points
                $customer->increment('loyalty_points', $pointsEarned);

                $importedBookings[] = $booking;

                // Log details
                $payload = [
                    'booking_id' => $booking->id,
                    'ota_room_id' => $mapping->ota_room_id,
                    'total_price' => $totalPrice,
                    'room_number' => $room->room_number,
                ];

                ChannelSyncLog::create([
                    'channel_name' => $channelName,
                    'sync_type' => 'pull_bookings',
                    'status' => 'success',
                    'message' => "Successfully imported guest reservation from {$channelName} for Room {$room->room_number}.",
                    'payload' => $payload,
                ]);
            } else {
                ChannelSyncLog::create([
                    'channel_name' => $channelName,
                    'sync_type' => 'pull_bookings',
                    'status' => 'error',
                    'message' => "Failed to import reservation: No available physical rooms for room type ID {$mapping->room_type_id}.",
                    'payload' => null,
                ]);
            }
        }

        return $importedBookings;
    }
}

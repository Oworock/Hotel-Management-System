<?php

namespace Plugins\ChannelManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Plugins\ChannelManager\Models\ChannelRoomMapping;
use Plugins\ChannelManager\Models\ChannelSyncLog;
use Plugins\ChannelManager\Models\OtaChannel;
use Plugins\ChannelManager\Services\ChannelSyncService;

class ChannelManagerController extends Controller
{
    protected $syncService;

    public function __construct(ChannelSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function index()
    {
        // Seed default channels if they don't exist
        $channels = ['Booking.com', 'Airbnb', 'Expedia'];
        foreach ($channels as $name) {
            OtaChannel::firstOrCreate(['name' => $name]);
        }

        $otaChannels = OtaChannel::all();
        $mappings = ChannelRoomMapping::with('roomType')->get();
        $roomTypes = RoomType::all();
        $logs = ChannelSyncLog::orderBy('id', 'desc')->take(30)->get();

        $currency = Setting::getValue('currency', 'USD');

        return view('channel_manager.index', compact('otaChannels', 'mappings', 'roomTypes', 'logs', 'currency'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|exists:ota_channels,id',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'hotel_id' => 'nullable|string|max:255',
            'is_connected' => 'nullable|boolean',
        ]);

        $channel = OtaChannel::findOrFail($request->channel_id);
        $channel->update([
            'api_key' => $request->api_key,
            'api_secret' => $request->api_secret,
            'hotel_id' => $request->hotel_id,
            'is_connected' => $request->has('is_connected') ? (bool)$request->is_connected : false,
        ]);

        // Add log
        ChannelSyncLog::create([
            'channel_name' => $channel->name,
            'sync_type' => 'save_settings',
            'status' => 'success',
            'message' => "Updated credentials & connection settings for {$channel->name}.",
            'payload' => null,
        ]);

        return redirect()->back()->with('success', "Credentials for {$channel->name} updated successfully.");
    }

    public function addMapping(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'channel_name' => 'required|string|in:Booking.com,Airbnb,Expedia',
            'ota_room_id' => 'required|string|max:255',
            'rate_multiplier' => 'required|numeric|min:0.5|max:3.0',
        ]);

        // Check if mapping already exists for this room type on this channel
        $exists = ChannelRoomMapping::where('room_type_id', $request->room_type_id)
            ->where('channel_name', $request->channel_name)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "A mapping already exists for this room type on {$request->channel_name}.");
        }

        $mapping = ChannelRoomMapping::create([
            'room_type_id' => $request->room_type_id,
            'channel_name' => $request->channel_name,
            'ota_room_id' => $request->ota_room_id,
            'rate_multiplier' => $request->rate_multiplier,
        ]);

        // Automatically trigger initial push of availability
        $this->syncService->pushAvailability($mapping->id);

        return redirect()->back()->with('success', 'Room mapping created and initial availability push simulated.');
    }

    public function deleteMapping(ChannelRoomMapping $mapping)
    {
        $channelName = $mapping->channel_name;
        $mapping->delete();

        ChannelSyncLog::create([
            'channel_name' => $channelName,
            'sync_type' => 'delete_mapping',
            'status' => 'success',
            'message' => "Deleted room mapping for room type ID {$mapping->room_type_id}.",
            'payload' => null,
        ]);

        return redirect()->back()->with('success', 'Room mapping deleted successfully.');
    }

    public function manualSync(Request $request)
    {
        $request->validate([
            'channel_name' => 'required|string|in:Booking.com,Airbnb,Expedia',
            'sync_type' => 'required|string|in:all,push,pull',
        ]);

        $channelName = $request->channel_name;
        $syncType = $request->sync_type;

        $channel = OtaChannel::where('name', $channelName)->first();
        if (!$channel || !$channel->is_connected) {
            return redirect()->back()->with('error', "Cannot sync: {$channelName} is not connected. Please configure credentials and enable connection.");
        }

        $mappings = ChannelRoomMapping::where('channel_name', $channelName)->get();
        if ($mappings->isEmpty()) {
            return redirect()->back()->with('error', "No room mappings found for {$channelName}. Add a mapping first.");
        }

        $successCount = 0;
        $pullSuccess = false;

        if ($syncType === 'push' || $syncType === 'all') {
            foreach ($mappings as $mapping) {
                if ($this->syncService->pushAvailability($mapping->id)) {
                    $successCount++;
                }
            }
        }

        if ($syncType === 'pull' || $syncType === 'all') {
            $importedBookings = $this->syncService->pullBookings($channelName);
            $pullSuccess = true;
        }

        $msg = "Manual synchronization completed for {$channelName}.";
        if ($syncType === 'push' || $syncType === 'all') {
            $msg .= " Availability pushed for {$successCount} mapping(s).";
        }
        if ($syncType === 'pull' || $syncType === 'all') {
            $count = isset($importedBookings) ? count($importedBookings) : 0;
            $msg .= " Imported {$count} guest booking(s).";
        }

        return redirect()->back()->with('success', $msg);
    }
}

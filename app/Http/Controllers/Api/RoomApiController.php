<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        $rooms = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $rooms
        ]);
    }

    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $room
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }

        $request->validate([
            'status' => 'required|string|in:available,booked,maintenance,dirty'
        ]);

        $room->status = $request->status;
        $room->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Room status updated successfully.',
            'data' => $room
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\StaffShift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Active shift
        $activeShift = StaffShift::where('user_id', $user->id)
            ->whereNull('clock_out_at')
            ->first();

        // Recent shifts
        $recentShifts = StaffShift::where('user_id', $user->id)
            ->orderBy('clock_in_at', 'desc')
            ->take(5)
            ->get();

        // All rooms for housekeeping
        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        return view('staff.dashboard', compact('activeShift', 'recentShifts', 'rooms'));
    }

    public function clockIn()
    {
        $user = auth()->user();

        // Check if already clocked in
        $activeShift = StaffShift::where('user_id', $user->id)
            ->whereNull('clock_out_at')
            ->first();

        if ($activeShift) {
            return redirect()->back()->with('error', 'You are already clocked in.');
        }

        StaffShift::create([
            'user_id' => $user->id,
            'clock_in_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Clocked in successfully. Have a great shift!');
    }

    public function clockOut()
    {
        $user = auth()->user();

        // Find active shift
        $activeShift = StaffShift::where('user_id', $user->id)
            ->whereNull('clock_out_at')
            ->first();

        if (!$activeShift) {
            return redirect()->back()->with('error', 'You do not have an active shift to clock out of.');
        }

        $now = Carbon::now();
        $durationMinutes = $activeShift->clock_in_at->diffInMinutes($now);

        $activeShift->update([
            'clock_out_at' => $now,
            'duration_minutes' => $durationMinutes,
        ]);

        return redirect()->back()->with('success', 'Clocked out successfully. Thank you for your work!');
    }

    public function updateHousekeeping(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        // Keep room booked if guest has not checked out, but allow staff to update
        $room->status = $request->status;
        $room->save();

        return redirect()->back()->with('success', "Room #{$room->room_number} status updated to {$request->status}.");
    }

    public function bookings()
    {
        $bookings = Booking::with(['customer', 'room.roomType'])
            ->orderBy('check_in_date', 'desc')
            ->get();

        return view('staff.bookings', compact('bookings'));
    }

    public function checkInGuest(Booking $booking)
    {
        if ($booking->status === 'checked_in') {
            return redirect()->back()->with('error', 'Guest is already checked in.');
        }

        $booking->update(['status' => 'checked_in']);
        
        // Mark room booked
        $room = $booking->room;
        $room->status = 'booked';
        $room->save();

        return redirect()->back()->with('success', "Guest checked in successfully to Room #{$room->room_number}.");
    }

    public function checkOutGuest(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return redirect()->back()->with('error', 'Cannot check out a guest who is not checked in.');
        }

        $booking->update(['status' => 'checked_out']);
        
        // Mark room dirty (requires housekeeping cleaning)
        $room = $booking->room;
        $room->status = 'dirty';
        $room->save();

        return redirect()->back()->with('success', "Guest checked out from Room #{$room->room_number}. Room has been marked dirty for cleaning.");
    }
}

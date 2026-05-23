<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'coupon_code' => 'nullable|string',
            'addons' => 'nullable|array',
        ]);

        $start = Carbon::parse($request->check_in);
        $end = Carbon::parse($request->check_out);
        $nights = $start->diffInDays($end);

        // Overlapping bookings check
        $bookedRoomIds = Booking::where(function ($query) use ($start, $end) {
            $query->where('check_in_date', '<', $end)
                  ->where('check_out_date', '>', $start);
        })->whereIn('status', ['pending', 'confirmed', 'checked_in'])->pluck('room_id');

        // Find first available room of this type
        $room = Room::where('room_type_id', $request->room_type_id)
            ->whereNotIn('id', $bookedRoomIds)
            ->where('status', 'available')
            ->first();

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'No rooms of this type are available for the selected dates.'
            ], 422);
        }

        // Calculate pricing
        $roomType = RoomType::find($request->room_type_id);
        $subtotal = $roomType->base_price * $nights;
        
        $discountAmount = 0.00;
        $appliedCouponCode = null;
        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon) {
                if ($coupon->isValid()) {
                    $discountAmount = $subtotal * ($coupon->discount_percentage / 100);
                    $appliedCouponCode = $coupon->code;
                }
            }
        }

        $discountedSubtotal = $subtotal - $discountAmount;

        // Addons
        $addonsDetails = [];
        $addonsCost = 0.00;
        if ($request->has('addons') && is_array($request->addons)) {
            foreach ($request->addons as $addon) {
                if ($addon === 'airport_shuttle') {
                    $addonsDetails[] = ['name' => 'Airport Shuttle', 'price' => 30.00, 'type' => 'one_time'];
                    $addonsCost += 30.00;
                } elseif ($addon === 'luxury_spa') {
                    $addonsDetails[] = ['name' => 'Luxury Spa', 'price' => 50.00, 'type' => 'one_time'];
                    $addonsCost += 50.00;
                } elseif ($addon === 'gourmet_breakfast') {
                    $cost = 20.00 * $nights;
                    $addonsDetails[] = ['name' => 'Gourmet Breakfast', 'price' => 20.00, 'type' => 'per_night', 'total' => $cost];
                    $addonsCost += $cost;
                } elseif ($addon === 'premium_minibar') {
                    $addonsDetails[] = ['name' => 'Premium Mini-Bar', 'price' => 40.00, 'type' => 'one_time'];
                    $addonsCost += 40.00;
                }
            }
        }

        $finalSubtotal = $discountedSubtotal + $addonsCost;
        $taxRate = (float)Setting::getValue('tax_rate', '12');
        $taxAmount = $finalSubtotal * ($taxRate / 100);
        $totalPrice = $finalSubtotal + $taxAmount;

        // Create booking
        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'room_id' => $room->id,
            'check_in_date' => $start,
            'check_out_date' => $end,
            'total_price' => $totalPrice,
            'coupon_code' => $appliedCouponCode,
            'discount_amount' => $discountAmount,
            'loyalty_points_redeemed' => 0,
            'loyalty_discount_amount' => 0.00,
            'addons' => count($addonsDetails) > 0 ? json_encode($addonsDetails) : null,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully.',
            'data' => $booking
        ], 201);
    }

    public function checkIn(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.'
            ], 404);
        }

        if ($request->has('room_id')) {
            $request->validate([
                'room_id' => 'required|exists:rooms,id'
            ]);
            $booking->room_id = $request->room_id;
        }

        $booking->status = 'checked_in';
        $booking->save();

        // Update room status
        if ($booking->room) {
            $booking->room->status = 'booked';
            $booking->room->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Booking checked in successfully.',
            'data' => $booking
        ]);
    }

    public function checkOut($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.'
            ], 404);
        }

        $booking->status = 'checked_out';
        $booking->save();

        // Update room status
        if ($booking->room) {
            $booking->room->status = 'dirty';
            $booking->room->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Booking checked out successfully.',
            'data' => $booking
        ]);
    }
}

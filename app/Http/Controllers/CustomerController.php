<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $checkIn = $request->input('check_in_date');
        $checkOut = $request->input('check_out_date');
        
        $taxRate = (float)Setting::getValue('tax_rate', '12');
        $currency = Setting::getValue('currency', 'USD');

        $roomTypes = RoomType::all();

        $searched = false;
        $nights = 1;

        if ($checkIn && $checkOut) {
            $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
            ]);

            $start = Carbon::parse($checkIn);
            $end = Carbon::parse($checkOut);
            $nights = $start->diffInDays($end);
            $searched = true;

            // Find overlapping booked rooms
            $bookedRoomIds = Booking::where(function ($query) use ($start, $end) {
                $query->where('check_in_date', '<', $end)
                      ->where('check_out_date', '>', $start);
            })->whereIn('status', ['pending', 'confirmed', 'checked_in'])->pluck('room_id');

            // Attach available count to each room type
            foreach ($roomTypes as $type) {
                $availableCount = Room::where('room_type_id', $type->id)
                    ->whereNotIn('id', $bookedRoomIds)
                    ->where('status', 'available')
                    ->count();
                $type->available_count = $availableCount;
            }
        } else {
            // Default check availability logic - just count rooms that are available in general
            foreach ($roomTypes as $type) {
                $type->available_count = Room::where('room_type_id', $type->id)
                    ->where('status', 'available')
                    ->count();
            }
        }

        return view('customer.dashboard', compact('roomTypes', 'checkIn', 'checkOut', 'searched', 'nights', 'taxRate', 'currency'));
    }

    public function showBookingForm(RoomType $roomType, Request $request)
    {
        $checkIn = $request->query('check_in_date', Carbon::today()->format('Y-m-d'));
        $checkOut = $request->query('check_out_date', Carbon::tomorrow()->format('Y-m-d'));
        
        $taxRate = (float)Setting::getValue('tax_rate', '12');
        $currency = Setting::getValue('currency', 'USD');

        // Fetch active, unexpired coupons
        $coupons = \App\Models\Coupon::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expire_at')
                      ->orWhere('expire_at', '>=', now()->startOfDay());
            })->get();

        return view('customer.book', compact('roomType', 'checkIn', 'checkOut', 'taxRate', 'currency', 'coupons'));
    }

    public function processBooking(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'coupon_code' => 'nullable|string',
            'addons' => 'nullable|array',
            'redeem_loyalty' => 'nullable|boolean',
        ]);

        $start = Carbon::parse($request->check_in_date);
        $end = Carbon::parse($request->check_out_date);
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
            return redirect()->back()->with('error', 'No rooms of this type are available for the selected dates.');
        }

        // Calculate pricing
        $roomType = RoomType::find($request->room_type_id);
        $subtotal = $roomType->base_price * $nights;
        
        $discountAmount = 0.00;
        $appliedCouponCode = null;
        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon) {
                if (!$coupon->isValid()) {
                    return redirect()->back()->with('error', 'The coupon code is invalid or has expired.');
                }
                $discountAmount = $subtotal * ($coupon->discount_percentage / 100);
                $appliedCouponCode = $coupon->code;
            }
        }

        $discountedSubtotal = $subtotal - $discountAmount;

        // Loyalty redemption logic
        $loyaltyPointsRedeemed = 0;
        $loyaltyDiscountAmount = 0.00;
        $customer = $request->user();
        if ($request->boolean('redeem_loyalty') && $customer && $customer->loyalty_points > 0) {
            $userPoints = $customer->loyalty_points;
            $userPointsValue = $userPoints * 0.10;
            if ($userPointsValue >= $discountedSubtotal) {
                $loyaltyDiscountAmount = $discountedSubtotal;
                $loyaltyPointsRedeemed = (int)ceil($loyaltyDiscountAmount / 0.10);
                $loyaltyDiscountAmount = $loyaltyPointsRedeemed * 0.10;
            } else {
                $loyaltyPointsRedeemed = $userPoints;
                $loyaltyDiscountAmount = $loyaltyPointsRedeemed * 0.10;
            }
            
            // Deduct immediately to prevent double spending
            $customer->decrement('loyalty_points', $loyaltyPointsRedeemed);
        }

        $discountedSubtotal = $discountedSubtotal - $loyaltyDiscountAmount;

        // Add-ons logic
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
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in_date' => $start,
            'check_out_date' => $end,
            'total_price' => $totalPrice,
            'coupon_code' => $appliedCouponCode,
            'discount_amount' => $discountAmount,
            'loyalty_points_redeemed' => $loyaltyPointsRedeemed,
            'loyalty_discount_amount' => $loyaltyDiscountAmount,
            'addons' => count($addonsDetails) > 0 ? json_encode($addonsDetails) : null,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return redirect()->route('customer.payment', $booking->id);
    }

    public function verifyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = \App\Models\Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code not found.'
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code is expired or inactive.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'discount_percentage' => (float)$coupon->discount_percentage,
            'message' => 'Coupon applied successfully!'
        ]);
    }

    public function showPaymentForm(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('customer.bookings')->with('success', 'This booking is already paid.');
        }

        $currency = Setting::getValue('currency', 'USD');

        return view('customer.payment', compact('booking', 'currency'));
    }

    public function processPayment(Booking $booking, Request $request)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'card_number' => 'required|string|min:16',
            'card_name' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string|min:3|max:4',
        ]);

        // Create mockup payment record
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => 'Credit Card',
            'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(6))),
            'status' => 'completed',
        ]);

        // Calculate loyalty points earned (1 point per $10 spent)
        $pointsEarned = (int)floor($booking->total_price / 10);

        // Update booking details
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'loyalty_points_earned' => $pointsEarned,
        ]);

        if ($booking->customer) {
            $booking->customer->increment('loyalty_points', $pointsEarned);
        }

        return redirect()->route('customer.bookings')->with('success', 'Payment successful! Your booking is confirmed.');
    }

    public function bookings()
    {
        $bookings = Booking::with(['room.roomType'])
            ->where('customer_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return view('customer.bookings', compact('bookings'));
    }

    public function selfCheckIn(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $today = Carbon::today();
        
        // Ensure check-in is allowed (on or after check-in date, but before check-out)
        if ($today->lt($booking->check_in_date) || $today->gte($booking->check_out_date)) {
            return redirect()->back()->with('error', 'Online check-in is only available on your check-in date.');
        }

        if ($booking->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Please complete the payment before checking in.');
        }

        if ($booking->status === 'checked_in') {
            return redirect()->back()->with('success', 'You are already checked in.');
        }

        $booking->update(['status' => 'checked_in']);
        
        // Update room status
        $room = $booking->room;
        $room->status = 'booked';
        $room->save();

        return redirect()->back()->with('success', 'Self check-in complete. Welcome to your room!');
    }

    public function selfCheckOut(Booking $booking)
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($booking->status !== 'checked_in') {
            return redirect()->back()->with('error', 'You must check-in first before checking out.');
        }

        $booking->update(['status' => 'checked_out']);
        
        // Update room to dirty
        $room = $booking->room;
        $room->status = 'dirty';
        $room->save();

        return redirect()->back()->with('success', 'Self check-out complete. Thank you for staying with us!');
    }
}

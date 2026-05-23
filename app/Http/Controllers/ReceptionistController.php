<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceptionistController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Dashboard Stats
        $arrivalsToday = Booking::whereDate('check_in_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $departuresToday = Booking::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $activeCheckins = Booking::where('status', 'checked_in')->count();

        // Room Stats
        $availableRooms = Room::where('status', 'available')->count();
        $bookedRooms = Room::where('status', 'booked')->count();
        $dirtyRooms = Room::where('status', 'dirty')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        // List today's action items
        $todayArrivals = Booking::with(['customer', 'room.roomType'])
            ->whereDate('check_in_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('id', 'desc')
            ->get();

        $todayDepartures = Booking::with(['customer', 'room.roomType'])
            ->whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->orderBy('id', 'desc')
            ->get();

        return view('receptionist.dashboard', compact(
            'arrivalsToday',
            'departuresToday',
            'activeCheckins',
            'availableRooms',
            'bookedRooms',
            'dirtyRooms',
            'maintenanceRooms',
            'todayArrivals',
            'todayDepartures'
        ));
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['customer', 'room.roomType']);

        // Search by customer name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by booking status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('id', 'desc')->paginate(15);
        $roomTypes = \App\Models\RoomType::all();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $coupons = \App\Models\Coupon::where('is_active', true)->get();
        return view('receptionist.bookings', compact('bookings', 'roomTypes', 'customers', 'coupons'));
    }

    public function storeWalkInBooking(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_type,existing|nullable|exists:users,id',
            'name' => 'required_if:customer_type,new|nullable|string|max:255',
            'email' => 'required_if:customer_type,new|nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'coupon_code' => 'nullable|string',
            'addons' => 'nullable|array',
            'payment_received' => 'nullable|boolean',
            'payment_method' => 'required_if:payment_received,1|nullable|string',
        ]);

        if ($request->customer_type === 'new') {
            $customer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'phone' => $request->phone,
                'nationality' => $request->nationality,
                'loyalty_points' => 0,
            ]);
        } else {
            $customer = User::findOrFail($request->customer_id);
        }

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
            return redirect()->back()->with('error', 'No rooms of this type are available for the selected dates.')->withInput();
        }

        // Calculate pricing
        $roomType = \App\Models\RoomType::find($request->room_type_id);
        $subtotal = $roomType->base_price * $nights;
        
        $discountAmount = 0.00;
        $appliedCouponCode = null;
        if ($request->filled('coupon_code')) {
            $coupon = \App\Models\Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon) {
                if (!$coupon->isValid()) {
                    return redirect()->back()->with('error', 'The coupon code is invalid or has expired.')->withInput();
                }
                $discountAmount = $subtotal * ($coupon->discount_percentage / 100);
                $appliedCouponCode = $coupon->code;
            }
        }

        $discountedSubtotal = $subtotal - $discountAmount;

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
        $taxRate = (float)\App\Models\Setting::getValue('tax_rate', '12');
        $taxAmount = $finalSubtotal * ($taxRate / 100);
        $totalPrice = $finalSubtotal + $taxAmount;

        $paymentReceived = $request->boolean('payment_received');
        $bookingStatus = $paymentReceived ? 'confirmed' : 'pending';
        $paymentStatus = $paymentReceived ? 'paid' : 'unpaid';
        $pointsEarned = $paymentReceived ? (int)floor($totalPrice / 10) : 0;

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in_date' => $start,
            'check_out_date' => $end,
            'total_price' => $totalPrice,
            'coupon_code' => $appliedCouponCode,
            'discount_amount' => $discountAmount,
            'loyalty_points_redeemed' => 0,
            'loyalty_discount_amount' => 0.00,
            'addons' => count($addonsDetails) > 0 ? json_encode($addonsDetails) : null,
            'status' => $bookingStatus,
            'payment_status' => $paymentStatus,
            'loyalty_points_earned' => $pointsEarned,
        ]);

        if ($paymentReceived) {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalPrice,
                'payment_method' => $request->input('payment_method', 'Cash'),
                'transaction_id' => 'TXN-WLK-' . strtoupper(bin2hex(random_bytes(6))),
                'status' => 'completed',
            ]);
            $customer->increment('loyalty_points', $pointsEarned);
        }

        return redirect()->back()->with('success', 'Walk-in booking created successfully.');
    }

    public function checkInGuest(Booking $booking)
    {
        if ($booking->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Guest has not paid for the booking. Please accept payment first.');
        }

        if ($booking->status === 'checked_in') {
            return redirect()->back()->with('success', 'Guest is already checked in.');
        }

        $booking->update(['status' => 'checked_in']);
        
        $room = $booking->room;
        if ($room) {
            $room->update(['status' => 'booked']);
        }

        return redirect()->back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOutGuest(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return redirect()->back()->with('error', 'Cannot check out a guest who is not checked in.');
        }

        $booking->update(['status' => 'checked_out']);
        
        $room = $booking->room;
        if ($room) {
            $room->update(['status' => 'dirty']);
        }

        return redirect()->back()->with('success', 'Guest checked out successfully. Room marked as dirty for housekeeping.');
    }

    public function cancelBooking(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'checked_out'])) {
            return redirect()->back()->with('error', 'Cannot cancel a booking that has already checked in or checked out.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Booking is already cancelled.');
        }

        // Restore loyalty points
        if ($booking->loyalty_points_redeemed > 0 && $booking->customer) {
            $booking->customer->increment('loyalty_points', $booking->loyalty_points_redeemed);
        }

        // Deduct earned loyalty points if any
        if ($booking->loyalty_points_earned > 0 && $booking->customer) {
            $booking->customer->decrement('loyalty_points', min($booking->loyalty_points_earned, $booking->customer->loyalty_points));
        }

        $booking->update(['status' => 'cancelled']);
        
        $room = $booking->room;
        if ($room) {
            $room->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    public function recordPayment(Booking $booking)
    {
        if ($booking->payment_status === 'paid') {
            return redirect()->back()->with('success', 'This booking is already paid.');
        }

        // Create mockup payment record
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => 'Cash / Counter Card',
            'transaction_id' => 'TXN-RCV-' . strtoupper(bin2hex(random_bytes(6))),
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

        return redirect()->back()->with('success', 'Payment of ' . number_format($booking->total_price, 2) . ' accepted successfully. Booking is now confirmed.');
    }

    public function guests(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('id', 'desc')->paginate(15);
        return view('receptionist.guests', compact('guests'));
    }

    public function rooms(Request $request)
    {
        $query = Room::with('roomType');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('room_number')->get();
        return view('receptionist.rooms', compact('rooms'));
    }

    public function updateRoomStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        $room->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Room #{$room->room_number} status updated to {$request->status}.");
    }
}

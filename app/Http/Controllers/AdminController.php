<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\StaffShift;
use App\Models\User;
use App\Models\Setting;
use App\Models\HeroSlide;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Business Metrics
        $totalEarnings = Payment::where('status', 'completed')->sum('amount');
        
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'booked')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $activeStaffCount = User::where('role', 'staff')
            ->whereHas('shifts', function($query) {
                $query->whereNull('clock_out_at');
            })->count();

        // Room Status Summary
        $roomStatuses = [
            'available' => Room::where('status', 'available')->count(),
            'booked' => $occupiedRooms,
            'dirty' => Room::where('status', 'dirty')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        // Recent Bookings
        $recentBookings = Booking::with(['customer', 'room.roomType'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Advanced Analytics Compilation
        $averageRevenue = Booking::where('status', '!=', 'cancelled')->avg('total_price') ?? 0;
        
        $bookingsForDuration = Booking::where('status', '!=', 'cancelled')->get();
        $totalNights = 0;
        $completedCount = $bookingsForDuration->count();
        foreach ($bookingsForDuration as $b) {
            $start = \Carbon\Carbon::parse($b->check_in_date);
            $end = \Carbon\Carbon::parse($b->check_out_date);
            $totalNights += $start->diffInDays($end);
        }
        $averageDuration = $completedCount > 0 ? round($totalNights / $completedCount, 1) : 0;

        $revenueByRoomType = [];
        $roomTypes = RoomType::all();
        foreach ($roomTypes as $type) {
            $revenue = Booking::whereHas('room', function($query) use ($type) {
                $query->where('room_type_id', $type->id);
            })->where('payment_status', 'paid')->sum('total_price');
            $revenueByRoomType[$type->name] = $revenue;
        }

        $activeShifts = StaffShift::with('user')
            ->whereNull('clock_out_at')
            ->get();

        $dirtyRooms = Room::with('roomType')
            ->where('status', 'dirty')
            ->get();

        $settings = [
            'hero_subtitle' => Setting::getValue('hero_subtitle', 'Experience absolute peace, beach side views, and unmatched butler services.'),
            'welcome_description' => Setting::getValue('welcome_description', 'A luxury sanctuary where contemporary design meets pristine nature. Located on the golden sands of our private beach, Aetheria offers an escape from the ordinary.'),
            'contact_phone' => Setting::getValue('contact_phone', '+1 (555) 123-4567'),
            'contact_email' => Setting::getValue('contact_email', 'info@aetheriagrand.com'),
            'global_header' => Setting::getValue('global_header', '✨ Welcome to Aetheria Grand Hotel - Book directly to get 15% off and free breakfast!'),
            'global_footer' => Setting::getValue('global_footer', '© 2026 Aetheria Grand Hotel. All rights reserved.'),
            'testimonials_list' => Setting::getValue('testimonials_list', '[]'),
        ];
        
        $slides = HeroSlide::orderBy('sort_order')->get();

        return view('admin.dashboard', compact(
            'totalEarnings',
            'occupancyRate',
            'activeStaffCount',
            'roomStatuses',
            'recentBookings',
            'totalRooms',
            'averageRevenue',
            'averageDuration',
            'revenueByRoomType',
            'activeShifts',
            'dirtyRooms',
            'settings',
            'slides'
        ));
    }

    public function rooms()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::all();
        return view('admin.rooms', compact('rooms', 'roomTypes'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        Room::create($request->all());

        return redirect()->back()->with('success', 'Room added successfully.');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        $room->update($request->all());

        return redirect()->back()->with('success', 'Room details updated.');
    }

    public function deleteRoom(Room $room)
    {
        $room->delete();
        return redirect()->back()->with('success', 'Room deleted.');
    }

    public function updateRoomStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,booked,maintenance,dirty',
        ]);

        $room->status = $request->status;
        $room->save();

        return redirect()->back()->with('success', "Room #{$room->room_number} status updated to {$request->status}.");
    }

    public function storeRoomType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $fileName = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/rooms'), $fileName);
                $uploadedImages[] = '/uploads/rooms/' . $fileName;
            }
        }

        RoomType::create([
            'name' => $request->name,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'capacity' => $request->capacity,
            'amenities' => $request->amenities ?? [],
            'images' => !empty($uploadedImages) ? $uploadedImages : ['default.jpg']
        ]);

        return redirect()->back()->with('success', 'Room type created successfully.');
    }

    public function updateRoomType(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096'
        ]);

        $uploadedImages = $roomType->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old uploaded images
            if (is_array($roomType->images)) {
                foreach ($roomType->images as $oldImage) {
                    if ($oldImage !== 'default.jpg' && str_starts_with($oldImage, '/uploads/')) {
                        @unlink(public_path($oldImage));
                    }
                }
            }
            $uploadedImages = [];
            foreach ($request->file('images') as $file) {
                $fileName = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/rooms'), $fileName);
                $uploadedImages[] = '/uploads/rooms/' . $fileName;
            }
        }

        $roomType->update([
            'name' => $request->name,
            'description' => $request->description,
            'base_price' => $request->base_price,
            'capacity' => $request->capacity,
            'amenities' => $request->amenities ?? [],
            'images' => $uploadedImages,
        ]);

        return redirect()->back()->with('success', 'Room type updated successfully.');
    }

    public function deleteRoomType(RoomType $roomType)
    {
        if ($roomType->rooms()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete room type because rooms are associated with it.');
        }

        $roomType->delete();
        return redirect()->back()->with('success', 'Room type deleted successfully.');
    }

    public function shifts()
    {
        $shifts = StaffShift::with('user')
            ->orderBy('clock_in_at', 'desc')
            ->paginate(15);
            
        return view('admin.shifts', compact('shifts'));
    }

    public function updateMinorContent(Request $request)
    {
        $data = $request->validate([
            'hero_subtitle' => 'nullable|string|max:500',
            'welcome_description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'global_header' => 'nullable|string',
            'global_footer' => 'nullable|string',
            'testimonials_list' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->back()->with('success', 'Minor content updated successfully.');
    }

    public function updateSlideMinor(Request $request, HeroSlide $slide)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $slide->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Hero slide details updated successfully.');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['customer', 'room.roomType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('id', 'desc')->paginate(15);
        $roomTypes = RoomType::all();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $coupons = \App\Models\Coupon::where('is_active', true)->get();
        return view('admin.bookings', compact('bookings', 'roomTypes', 'customers', 'coupons'));
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

        $start = \Carbon\Carbon::parse($request->check_in_date);
        $end = \Carbon\Carbon::parse($request->check_out_date);
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
        $roomType = RoomType::find($request->room_type_id);
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
        $taxRate = (float)Setting::getValue('tax_rate', '12');
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
            return redirect()->back()->with('error', 'Guest has not paid for the booking.');
        }

        $booking->update(['status' => 'checked_in']);
        
        $room = $booking->room;
        if ($room) {
            $room->update(['status' => 'booked']);
        }

        return redirect()->back()->with('success', 'Guest has been checked in successfully.');
    }

    public function checkOutGuest(Booking $booking)
    {
        $booking->update(['status' => 'checked_out']);
        
        $room = $booking->room;
        if ($room) {
            $room->update(['status' => 'dirty']);
        }

        return redirect()->back()->with('success', 'Guest has been checked out successfully.');
    }

    public function cancelBooking(Booking $booking)
    {
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

    public function guests(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nationality', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.guests', compact('guests'));
    }

    public function toggleGuestBlacklist(User $guest)
    {
        $guest->is_blacklisted = !$guest->is_blacklisted;
        $guest->save();

        $status = $guest->is_blacklisted ? 'blacklisted' : 'whitelisted';
        return redirect()->back()->with('success', "Guest has been {$status} successfully.");
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['booking.customer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('booking.customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $payments = $query->orderBy('id', 'desc')->paginate(15);
        return view('admin.payments', compact('payments'));
    }

    public function refundPayment(Payment $payment)
    {
        if ($payment->status === 'refunded') {
            return redirect()->back()->with('error', 'Payment has already been refunded.');
        }

        $payment->update(['status' => 'refunded']);
        
        $booking = $payment->booking;
        if ($booking) {
            $booking->update(['payment_status' => 'refunded', 'status' => 'cancelled']);
            $room = $booking->room;
            if ($room) {
                $room->update(['status' => 'available']);
            }
        }

        return redirect()->back()->with('success', 'Payment has been refunded successfully.');
    }

    public function reports(Request $request)
    {
        // Earnings analytics
        $totalEarnings = Payment::where('status', 'completed')->sum('amount');
        $refundedAmount = Payment::where('status', 'refunded')->sum('amount');
        
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'booked')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Group payments by month (mock representation for charts)
        $monthlyEarnings = Payment::select(\DB::raw('strftime("%m", created_at) as month'), \DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Convert month numbers to names
        $months = [];
        $earningsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $m = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $earningsData[] = $monthlyEarnings[$m] ?? 0.00;
        }

        return view('admin.reports', compact('totalEarnings', 'refundedAmount', 'occupancyRate', 'months', 'earningsData'));
    }

    public function exportReports(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=stayflow_hms_payout_report.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $payments = Payment::with(['booking.customer'])->get();

        $callback = function() use($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Transaction ID', 'Guest Name', 'Amount', 'Payment Method', 'Date', 'Status']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->transaction_id,
                    $payment->booking->customer->name ?? 'N/A',
                    $payment->amount,
                    $payment->payment_method,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function users(Request $request)
    {
        $users = User::where('role', 'staff')->orderBy('id', 'desc')->paginate(10);
        $shifts = StaffShift::with('user')->orderBy('id', 'desc')->paginate(15);
        return view('admin.users', compact('users', 'shifts'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'staff',
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Staff account created successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->back()->with('success', "Staff status updated to {$user->status}.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Staff account updated successfully.');
    }

    public function settingsForm()
    {
        $settings = [
            'hotel_name' => Setting::getValue('hotel_name', 'Aetheria Grand Hotel'),
            'currency' => Setting::getValue('currency', 'USD'),
            'tax_rate' => Setting::getValue('tax_rate', '12'),
            'check_in_time' => Setting::getValue('check_in_time', '14:00'),
            'check_out_time' => Setting::getValue('check_out_time', '11:00'),
            'contact_email' => Setting::getValue('contact_email', 'info@aetheriagrand.com'),
            'contact_phone' => Setting::getValue('contact_phone', '+1 (555) 123-4567'),
            'map_address' => Setting::getValue('map_address'),
            'primary_color' => Setting::getValue('primary_color', '#6e44ff'),
            'secondary_color' => Setting::getValue('secondary_color', '#f44496'),
            'logo_type' => Setting::getValue('logo_type', 'text'),
            'logo_text' => Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria'),
            'logo_image' => Setting::getValue('logo_image', ''),
            'global_header' => Setting::getValue('global_header', '✨ Welcome to Aetheria Grand Hotel'),
            'global_footer' => Setting::getValue('global_footer', '© 2026 Aetheria Grand Hotel. All rights reserved.'),
            
            // Hotel settings keys
            'hero_subtitle' => Setting::getValue('hero_subtitle', 'Experience Luxury & Paradise'),
            'welcome_description' => Setting::getValue('welcome_description', 'Nestled in a serene oasis, Aetheria Grand Hotel offers the ultimate blend of elegance, comfort, and world-class service.'),
            'testimonials_list' => Setting::getValue('testimonials_list', '[]'),

            'mail_host' => Setting::getValue('mail_host', 'smtp.mailtrap.io'),
            'mail_port' => Setting::getValue('mail_port', '2525'),
            'mail_username' => Setting::getValue('mail_username', 'smtp_user_demo'),
            'mail_password' => Setting::getValue('mail_password', 'smtp_pass_demo'),
            'mail_encryption' => Setting::getValue('mail_encryption', 'tls'),
            'mail_from_address' => Setting::getValue('mail_from_address', 'noreply@aetheriagrand.com'),
            
            'active_payment_gateway' => Setting::getValue('active_payment_gateway', 'card_simulation'),
            'paystack_public_key' => Setting::getValue('paystack_public_key'),
            'paystack_secret_key' => Setting::getValue('paystack_secret_key'),
            'flutterwave_public_key' => Setting::getValue('flutterwave_public_key'),
            'flutterwave_secret_key' => Setting::getValue('flutterwave_secret_key'),

            'sms_gateway_provider' => Setting::getValue('sms_gateway_provider', 'disabled'),
            'twilio_sid' => Setting::getValue('twilio_sid'),
            'twilio_token' => Setting::getValue('twilio_token'),
            'twilio_from' => Setting::getValue('twilio_from'),
            'vonage_api_key' => Setting::getValue('vonage_api_key'),
            'vonage_api_secret' => Setting::getValue('vonage_api_secret'),
            'vonage_from' => Setting::getValue('vonage_from'),
            'custom_sms_url' => Setting::getValue('custom_sms_url'),
            'custom_sms_method' => Setting::getValue('custom_sms_method', 'POST'),
            'custom_sms_headers' => Setting::getValue('custom_sms_headers', '{}'),
            'custom_sms_payload' => Setting::getValue('custom_sms_payload', '{}'),
        ];

        $slides = HeroSlide::orderBy('sort_order')->get();

        return view('admin.settings', compact('settings', 'slides'));
    }

    public function updateSettings(Request $request)
    {
        $isSuper = auth()->user()->isSuperAdmin();

        $rules = [
            'hotel_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:50',
            'map_address' => 'nullable|string',
            
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'logo_type' => 'nullable|in:text,image',
            'logo_text' => 'nullable|string',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'global_header' => 'nullable|string',
            'global_footer' => 'nullable|string',
            
            // Hotel settings keys
            'hero_subtitle' => 'nullable|string|max:255',
            'welcome_description' => 'nullable|string',
            'testimonials_list' => 'nullable|string',
        ];

        if ($isSuper) {
            $rules = array_merge($rules, [
                'mail_host' => 'nullable|string',
                'mail_port' => 'nullable|string',
                'mail_username' => 'nullable|string',
                'mail_password' => 'nullable|string',
                'mail_encryption' => 'nullable|string',
                'mail_from_address' => 'nullable|string',
                
                'active_payment_gateway' => 'required|in:card_simulation,paystack,flutterwave',
                'paystack_public_key' => 'nullable|string',
                'paystack_secret_key' => 'nullable|string',
                'flutterwave_public_key' => 'nullable|string',
                'flutterwave_secret_key' => 'nullable|string',

                'sms_gateway_provider' => 'nullable|in:disabled,twilio,vonage,custom',
                'twilio_sid' => 'nullable|string',
                'twilio_token' => 'nullable|string',
                'twilio_from' => 'nullable|string',
                'vonage_api_key' => 'nullable|string',
                'vonage_api_secret' => 'nullable|string',
                'vonage_from' => 'nullable|string',
                'custom_sms_url' => 'nullable|string',
                'custom_sms_method' => 'nullable|in:GET,POST',
                'custom_sms_headers' => 'nullable|string',
                'custom_sms_payload' => 'nullable|string',
            ]);
        }

        $data = $request->validate($rules);

        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            Setting::setValue('logo_image', '/uploads/' . $fileName);
        }

        foreach ($data as $key => $value) {
            if ($key !== 'logo_image') {
                Setting::setValue($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Hotel and settings configurations updated successfully.');
    }

    public function sliderStore(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'slide_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/slides'), $fileName);
            $imagePath = '/uploads/slides/' . $fileName;
        }

        HeroSlide::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Hero slide added successfully.');
    }

    public function sliderUpdate(Request $request, HeroSlide $slide)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($slide->image_path && str_starts_with($slide->image_path, '/uploads/')) {
                @unlink(public_path($slide->image_path));
            }

            $file = $request->file('image');
            $fileName = 'slide_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/slides'), $fileName);
            $data['image_path'] = '/uploads/slides/' . $fileName;
        }

        $slide->update($data);

        return redirect()->back()->with('success', 'Hero slide updated successfully.');
    }

    public function sliderDelete(HeroSlide $slide)
    {
        if ($slide->image_path && str_starts_with($slide->image_path, '/uploads/')) {
            @unlink(public_path($slide->image_path));
        }

        $slide->delete();

        return redirect()->back()->with('success', 'Hero slide deleted successfully.');
    }
}


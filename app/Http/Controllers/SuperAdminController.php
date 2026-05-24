<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $activeStaffCount = User::where('role', 'staff')->where('status', 'active')->count();
        $adminCount = User::where('role', 'admin')->count();

        // Compile Advanced System-Wide Analytics
        $totalEarnings = \App\Models\Payment::where('status', 'completed')->sum('amount');
        $totalBookings = \App\Models\Booking::count();
        $totalRooms = \App\Models\Room::count();
        $occupiedRooms = \App\Models\Room::whereIn('status', ['booked', 'occupied'])->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        
        $bookingsGrouped = \App\Models\Booking::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $recentBookings = \App\Models\Booking::with(['room', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('super_admin.dashboard', compact(
            'usersCount', 'activeStaffCount', 'adminCount',
            'totalEarnings', 'totalBookings', 'occupancyRate', 'bookingsGrouped', 'recentBookings'
        ));
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
            'physical_address' => Setting::getValue('physical_address', 'Golden Coast Beach Boulevard, Suite A, Victoria'),
            
            'primary_color' => Setting::getValue('primary_color', '#6e44ff'),
            'secondary_color' => Setting::getValue('secondary_color', '#f44496'),
            'platform_logo' => Setting::getValue('platform_logo', '<i class="fa-solid fa-hotel"></i> Aetheria'),
            'logo_type' => Setting::getValue('logo_type', 'text'),
            'logo_text' => Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria'),
            'logo_image' => Setting::getValue('logo_image', ''),
            'auth_background_image' => Setting::getValue('auth_background_image', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1800'),
            'global_header' => Setting::getValue('global_header', '✨ Welcome to Aetheria Grand Hotel'),
            'global_footer' => Setting::getValue('global_footer', '© 2026 Aetheria Grand Hotel. All rights reserved.'),
            
            'hero_title' => Setting::getValue('hero_title', 'Luxury Awaits You at Aetheria Grand'),
            'hero_subtitle' => Setting::getValue('hero_subtitle', 'Experience absolute peace and unmatched service.'),
            'welcome_title' => Setting::getValue('welcome_title', 'Experience Paradise'),
            'welcome_description' => Setting::getValue('welcome_description', 'A luxury sanctuary...'),
            'about_title' => Setting::getValue('about_title', 'A Luxury Oasis of Peace'),
            'about_description' => Setting::getValue('about_description', 'Escape the ordinary at Aetheria resorts, where luxury meets tranquility.'),
            'about_history_text' => Setting::getValue('about_history_text', 'Founded in 2012...'),
            'map_address' => Setting::getValue('map_address'),
            
            'mail_host' => Setting::getValue('mail_host', 'smtp.mailtrap.io'),
            'mail_port' => Setting::getValue('mail_port', '2525'),
            'mail_username' => Setting::getValue('mail_username', 'smtp_user_demo'),
            'mail_password' => Setting::getValue('mail_password', 'smtp_pass_demo'),
            'mail_encryption' => Setting::getValue('mail_encryption', 'tls'),
            'mail_from_address' => Setting::getValue('mail_from_address', 'noreply@aetheriagrand.com'),
            
            'active_payment_gateway' => Setting::getValue('active_payment_gateway', 'disabled'),
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

            'meta_title' => Setting::getValue('meta_title', 'StayFlow - Premium Hotel Management System'),
            'meta_description' => Setting::getValue('meta_description', 'Experience premier accommodation and elite booking facilities.'),
            'meta_keywords' => Setting::getValue('meta_keywords', 'hotel, booking, resort, luxury suite'),
            
            'promo_popup_enabled' => Setting::getValue('promo_popup_enabled', '0'),
            'promo_popup_title' => Setting::getValue('promo_popup_title', 'Special Offer!'),
            'promo_popup_content' => Setting::getValue('promo_popup_content', 'Get an exclusive discount today. Copy the coupon below and use it at booking!'),
            'promo_popup_image' => Setting::getValue('promo_popup_image', ''),
            'promo_popup_coupon' => Setting::getValue('promo_popup_coupon', ''),
        ];

        $slides = \App\Models\HeroSlide::orderBy('sort_order')->get();
        $pages = Page::orderBy('id', 'desc')->get();
        $coupons = \App\Models\Coupon::orderBy('id', 'desc')->get();

        return view('super_admin.settings', compact('settings', 'slides', 'pages', 'coupons'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'hotel_name' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:50',
            'physical_address' => 'nullable|string|max:1000',
            
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'platform_logo' => 'nullable|string',
            'logo_type' => 'nullable|in:text,image',
            'logo_text' => 'nullable|string',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'auth_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'global_header' => 'nullable|string',
            'global_footer' => 'nullable|string',
            
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'welcome_title' => 'nullable|string|max:255',
            'welcome_description' => 'nullable|string',
            'about_title' => 'nullable|string|max:255',
            'about_description' => 'nullable|string',
            'about_history_text' => 'nullable|string',
            'map_address' => 'nullable|string',
            
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|string',
            
            'active_payment_gateway' => 'nullable|in:disabled,paystack,flutterwave,card_simulation',
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
            'custom_sms_header_keys' => 'nullable|array',
            'custom_sms_header_values' => 'nullable|array',
            'custom_sms_payload_keys' => 'nullable|array',
            'custom_sms_payload_values' => 'nullable|array',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:1000',

            'promo_popup_title' => 'nullable|string|max:255',
            'promo_popup_content' => 'nullable|string',
            'promo_popup_image' => 'nullable|string|max:500',
            'promo_popup_coupon' => 'nullable|string|max:50',
        ]);

        if (!isset($data['sms_gateway_provider'])) {
            $data['sms_gateway_provider'] = Setting::getValue('sms_gateway_provider', 'disabled');
        }

        $data = $this->normalizeCustomSmsSettings($request, $data);

        if (!isset($data['active_payment_gateway'])) {
            $data['active_payment_gateway'] = Setting::getValue('active_payment_gateway', 'disabled');
        }

        if (!isset($data['logo_type'])) {
            $data['logo_type'] = 'text';
        }

        if (($data['active_payment_gateway'] ?? null) === 'card_simulation') {
            $data['active_payment_gateway'] = 'disabled';
        }

        Setting::setValue('promo_popup_enabled', $request->has('promo_popup_enabled') ? '1' : '0');

        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            Setting::setValue('logo_image', '/uploads/' . $fileName);
        }

        if ($request->hasFile('auth_background_image')) {
            $file = $request->file('auth_background_image');
            $fileName = 'auth_bg_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            Setting::setValue('auth_background_image', '/uploads/' . $fileName);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, ['logo_image', 'auth_background_image'], true)) {
                Setting::setValue($key, $value);
            }
        }

        return redirect()->back()->with('success', 'System configurations updated successfully.');
    }

    protected function normalizeCustomSmsSettings(Request $request, array $data): array
    {
        unset(
            $data['custom_sms_header_keys'],
            $data['custom_sms_header_values'],
            $data['custom_sms_payload_keys'],
            $data['custom_sms_payload_values']
        );

        if ($request->has('custom_sms_header_keys')) {
            $data['custom_sms_headers'] = $this->smsPairsToJson(
                $request->input('custom_sms_header_keys', []),
                $request->input('custom_sms_header_values', [])
            );
        }

        if ($request->has('custom_sms_payload_keys')) {
            $data['custom_sms_payload'] = $this->smsPairsToJson(
                $request->input('custom_sms_payload_keys', []),
                $request->input('custom_sms_payload_values', [])
            );
        }

        return $data;
    }

    protected function smsPairsToJson(array $keys, array $values): string
    {
        $pairs = [];

        foreach ($keys as $index => $key) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }

            $pairs[$key] = (string) ($values[$index] ?? '');
        }

        return json_encode($pairs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function users()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        $hotels = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('hotels')) {
            $hotels = \Plugins\MultiHotel\Models\Hotel::all();
        }
        return view('super_admin.users', compact('users', 'hotels'));
    }

    public function storeUser(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'max:255'],
            'functions' => ['nullable', 'array'],
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('hotels')) {
            $rules['hotel_id'] = ['nullable', 'integer', 'exists:hotels,id'];
        }

        $request->validate($rules);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->functions = $request->input('functions', []);
        $user->status = 'active';

        if (\Illuminate\Support\Facades\Schema::hasTable('hotels') && $request->has('hotel_id')) {
            $user->hotel_id = $request->hotel_id;
        }

        $user->save();

        return redirect()->back()->with('success', 'User account created successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->back()->with('success', "User account status updated to {$user->status}.");
    }

    public function updateUser(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'max:255'],
            'password' => ['nullable', Rules\Password::defaults()],
            'functions' => ['nullable', 'array'],
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('hotels')) {
            $rules['hotel_id'] = ['nullable', 'integer', 'exists:hotels,id'];
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->functions = $request->input('functions', []);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('hotels')) {
            $user->hotel_id = $request->hotel_id;
        }

        $user->save();

        return redirect()->back()->with('success', 'User account updated successfully.');
    }

    public function testSms(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        $result = \App\Services\SmsService::send($request->phone_number, $request->message);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Test SMS sent successfully: ' . $result['message']);
        }

        return redirect()->back()->with('error', 'Failed to send Test SMS: ' . $result['message']);
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

        \App\Models\HeroSlide::create([
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

    public function sliderUpdate(Request $request, \App\Models\HeroSlide $slide)
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

    public function sliderDelete(\App\Models\HeroSlide $slide)
    {
        if ($slide->image_path && str_starts_with($slide->image_path, '/uploads/')) {
            @unlink(public_path($slide->image_path));
        }

        $slide->delete();

        return redirect()->back()->with('success', 'Hero slide deleted successfully.');
    }

    public function storePage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug|max:255|regex:/^[a-z0-9-]+$/i',
            'content' => 'required|string',
            'is_active' => 'nullable',
            'show_in_nav' => 'nullable',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ], [
            'slug.regex' => 'The slug format is invalid (letters, numbers, and hyphens only).'
        ]);

        Page::create([
            'title' => $request->title,
            'slug' => strtolower($request->slug),
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
            'show_in_nav' => $request->has('show_in_nav'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return redirect()->back()->with('success', 'Page created successfully.');
    }

    public function updatePage(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:pages,slug,' . $page->id . '|max:255|regex:/^[a-z0-9-]+$/i',
            'content' => 'required|string',
            'is_active' => 'nullable',
            'show_in_nav' => 'nullable',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ], [
            'slug.regex' => 'The slug format is invalid (letters, numbers, and hyphens only).'
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => strtolower($request->slug),
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
            'show_in_nav' => $request->has('show_in_nav'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return redirect()->back()->with('success', 'Page updated successfully.');
    }

    public function deletePage(Page $page)
    {
        $page->delete();
        return redirect()->back()->with('success', 'Page deleted successfully.');
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50|regex:/^[A-Z0-9_-]+$/i',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expire_at' => 'nullable|date',
            'is_active' => 'nullable',
        ], [
            'code.regex' => 'The coupon code format is invalid (uppercase letters, numbers, hyphens, and underscores only).'
        ]);

        \App\Models\Coupon::create([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expire_at' => $request->expire_at,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Coupon promo code created successfully.');
    }

    public function updateCoupon(Request $request, \App\Models\Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id . '|max:50|regex:/^[A-Z0-9_-]+$/i',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expire_at' => 'nullable|date',
            'is_active' => 'nullable',
        ], [
            'code.regex' => 'The coupon code format is invalid (uppercase letters, numbers, hyphens, and underscores only).'
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expire_at' => $request->expire_at,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Coupon promo code updated successfully.');
    }

    public function deleteCoupon(\App\Models\Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->back()->with('success', 'Coupon promo code deleted successfully.');
    }

    public function impersonate(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot impersonate yourself.');
        }

        $request->session()->put('impersonated_by', auth()->id());
        auth()->login($user);

        switch ($user->role) {
            case 'super_admin':
                return redirect()->route('super_admin.dashboard')->with('success', "Now impersonating {$user->name}.");
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', "Now impersonating {$user->name}.");
            case 'receptionist':
                return redirect()->route('receptionist.dashboard')->with('success', "Now impersonating {$user->name}.");
            case 'kitchen_manager':
                return \Illuminate\Support\Facades\Route::has('kitchen.dashboard')
                    ? redirect()->route('kitchen.dashboard')->with('success', "Now impersonating {$user->name}.")
                    : redirect()->route('home')->with('success', "Now impersonating {$user->name}.");
            case 'tuck_shop_manager':
            case 'restaurant_manager':
                return \Illuminate\Support\Facades\Route::has('admin.products')
                    ? redirect()->route('admin.products')->with('success', "Now impersonating {$user->name}.")
                    : redirect()->route('home')->with('success', "Now impersonating {$user->name}.");
            case 'staff':
                return redirect()->route('staff.dashboard')->with('success', "Now impersonating {$user->name}.");
            case 'customer':
                return redirect()->route('customer.dashboard')->with('success', "Now impersonating {$user->name}.");
            default:
                if ($user->hasFunction('manage_channel_manager') && \Illuminate\Support\Facades\Route::has('admin.channel_manager.index')) {
                    return redirect()->route('admin.channel_manager.index')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('kitchen_dashboard') && \Illuminate\Support\Facades\Route::has('kitchen.dashboard')) {
                    return redirect()->route('kitchen.dashboard')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_bookings')) {
                    return redirect()->route('receptionist.dashboard')->with('success', "Now impersonating {$user->name}.");
                }
                if (($user->hasFunction('manage_tuck_shop') || $user->hasFunction('manage_restaurant')) && \Illuminate\Support\Facades\Route::has('admin.products')) {
                    return redirect()->route('admin.products')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_rooms')) {
                    return redirect()->route('admin.rooms')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_guests')) {
                    return redirect()->route('admin.guests')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_payments')) {
                    return redirect()->route('admin.payments')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_reports')) {
                    return redirect()->route('admin.reports')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_users')) {
                    return redirect()->route('admin.users')->with('success', "Now impersonating {$user->name}.");
                }
                if ($user->hasFunction('manage_settings')) {
                    return redirect()->route('admin.settings')->with('success', "Now impersonating {$user->name}.");
                }
                return redirect()->route('customer.dashboard')->with('success', "Now impersonating {$user->name}.");
        }
    }

    public function leaveImpersonate(Request $request)
    {
        if (!$request->session()->has('impersonated_by')) {
            return redirect()->route('home')->with('error', 'Not impersonating.');
        }

        $adminId = $request->session()->pull('impersonated_by');
        $admin = User::findOrFail($adminId);
        auth()->login($admin);

        return redirect()->route('super_admin.dashboard')->with('success', 'Returned to Super Admin account.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\HeroSlide;
use App\Models\Page;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $checkIn = $request->input('check_in_date');
        $checkOut = $request->input('check_out_date');
        $guestsCount = $request->input('guests', 1);

        $hotelName = Setting::getValue('hotel_name', 'Aetheria Grand Hotel');
        $currency = Setting::getValue('currency', 'USD');
        $heroTitle = Setting::getValue('hero_title', 'Luxury Awaits You at Aetheria Grand');
        $heroSubtitle = Setting::getValue('hero_subtitle', 'Experience absolute peace, beach side views, and unmatched butler services.');
        $welcomeTitle = Setting::getValue('welcome_title', 'Experience Paradise');
        $welcomeDescription = Setting::getValue('welcome_description', 'A luxury sanctuary where contemporary design meets pristine nature.');
        $mapAddress = Setting::getValue('map_address');
        $contactEmail = Setting::getValue('contact_email', 'info@aetheriagrand.com');
        $contactPhone = Setting::getValue('contact_phone', '+1 (555) 123-4567');

        // Load active slider slides
        $slides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();

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

            // Find booked rooms
            $bookedRoomIds = \App\Models\Booking::where(function ($query) use ($start, $end) {
                $query->where('check_in_date', '<', $end)
                      ->where('check_out_date', '>', $start);
            })->whereIn('status', ['pending', 'confirmed', 'checked_in'])->pluck('room_id');

            foreach ($roomTypes as $type) {
                $type->available_count = Room::where('room_type_id', $type->id)
                    ->whereNotIn('id', $bookedRoomIds)
                    ->where('status', 'available')
                    ->count();
            }
        } else {
            // Default count of available rooms
            foreach ($roomTypes as $type) {
                $type->available_count = Room::where('room_type_id', $type->id)
                    ->where('status', 'available')
                    ->count();
            }
        }

        return view('home', compact(
            'roomTypes', 'checkIn', 'checkOut', 'guestsCount', 'searched', 'nights',
            'hotelName', 'currency', 'heroTitle', 'heroSubtitle', 'welcomeTitle', 'welcomeDescription',
            'mapAddress', 'contactEmail', 'contactPhone', 'slides'
        ));
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }

    public function rooms()
    {
        $roomTypes = RoomType::all();
        foreach ($roomTypes as $type) {
            $type->available_count = Room::where('room_type_id', $type->id)
                ->where('status', 'available')
                ->count();
        }
        return view('rooms', compact('roomTypes'));
    }

    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('page', compact('page'));
    }

    public function faqs()
    {
        $faqs = \App\Models\FAQ::where('is_active', true)->orderBy('order')->get();
        return view('faqs', compact('faqs'));
    }

    public function testimonials()
    {
        $testimonials = \App\Models\Testimonial::where('is_active', true)->latest()->get();
        return view('testimonials', compact('testimonials'));
    }

    public function gallery()
    {
        $photos = \App\Models\Gallery::where('is_active', true)->orderBy('order')->get();
        return view('gallery', compact('photos'));
    }

    public function blog()
    {
        $posts = \App\Models\BlogPost::where('is_published', true)->with('author')->latest('published_at')->get();
        return view('blog.index', compact('posts'));
    }

    public function showBlogPost($slug)
    {
        $post = \App\Models\BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('blog.show', compact('post'));
    }

    public function services()
    {
        return view('services');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }
}

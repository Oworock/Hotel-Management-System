<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Helpers\ThemeHelper;
use App\Support\FrontendContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        $heroTitle = FrontendContent::get('hero_title');
        $heroSubtitle = FrontendContent::get('hero_subtitle');
        $welcomeTitle = FrontendContent::get('welcome_title');
        $welcomeDescription = FrontendContent::get('welcome_description');
        $contactData = $this->contactData();
        $mapAddress = $contactData['mapEmbedUrl'];
        $contactEmail = $contactData['contactEmail'];
        $contactPhone = $contactData['contactPhone'];
        $physicalAddress = $contactData['physicalAddress'];

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

        return view(ThemeHelper::getThemeView('home'), compact(
            'roomTypes', 'checkIn', 'checkOut', 'guestsCount', 'searched', 'nights',
            'hotelName', 'currency', 'heroTitle', 'heroSubtitle', 'welcomeTitle', 'welcomeDescription',
            'mapAddress', 'physicalAddress', 'contactEmail', 'contactPhone', 'slides'
        ));
    }

    public function about()
    {
        return $this->themePage('about', [
            'title' => FrontendContent::get('frontend_about_title'),
            'subtitle' => FrontendContent::get('frontend_about_subtitle'),
            'aboutTitle' => FrontendContent::get('about_title'),
            'aboutDescription' => FrontendContent::get('about_description'),
            'aboutHistoryText' => FrontendContent::get('about_history_text'),
        ]);
    }

    public function contact()
    {
        return $this->themePage('contact', array_merge([
            'title' => FrontendContent::get('frontend_contact_title'),
            'subtitle' => FrontendContent::get('frontend_contact_subtitle'),
            'pageBody' => FrontendContent::get('frontend_contact_body'),
        ], $this->contactData()));
    }

    public function rooms()
    {
        $roomTypes = RoomType::all();
        foreach ($roomTypes as $type) {
            $type->available_count = Room::where('room_type_id', $type->id)
                ->where('status', 'available')
                ->count();
        }
        return $this->themePage('rooms', [
            'title' => FrontendContent::get('frontend_rooms_title'),
            'subtitle' => FrontendContent::get('frontend_rooms_subtitle'),
            'pageBody' => FrontendContent::get('frontend_rooms_body'),
            'roomTypes' => $roomTypes,
            'currency' => Setting::getValue('currency', 'USD'),
        ]);
    }

    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return $this->themePage('page', [
            'title' => $page->title,
            'subtitle' => $page->meta_description,
            'page' => $page,
        ]);
    }

    public function faqs()
    {
        $faqs = \App\Models\FAQ::where('is_active', true)->orderBy('order')->get();
        return $this->themePage('faqs', [
            'title' => FrontendContent::get('frontend_faqs_title'),
            'subtitle' => FrontendContent::get('frontend_faqs_subtitle'),
            'pageBody' => FrontendContent::get('frontend_faqs_body'),
            'faqs' => $faqs,
        ]);
    }

    public function testimonials()
    {
        $testimonials = \App\Models\Testimonial::where('is_active', true)->latest()->get();
        return $this->themePage('testimonials', [
            'title' => FrontendContent::get('frontend_testimonials_title'),
            'subtitle' => FrontendContent::get('frontend_testimonials_subtitle'),
            'pageBody' => FrontendContent::get('frontend_testimonials_body'),
            'testimonials' => $testimonials,
        ]);
    }

    public function gallery()
    {
        $photos = \App\Models\Gallery::where('is_active', true)->orderBy('order')->get();
        return $this->themePage('gallery', [
            'title' => FrontendContent::get('frontend_gallery_title'),
            'subtitle' => FrontendContent::get('frontend_gallery_subtitle'),
            'pageBody' => FrontendContent::get('frontend_gallery_body'),
            'photos' => $photos,
        ]);
    }

    public function blog()
    {
        $posts = \App\Models\BlogPost::where('is_published', true)->with('author')->latest('published_at')->get();
        return $this->themePage('blog', [
            'title' => FrontendContent::get('frontend_blog_title'),
            'subtitle' => FrontendContent::get('frontend_blog_subtitle'),
            'pageBody' => FrontendContent::get('frontend_blog_body'),
            'posts' => $posts,
        ]);
    }

    public function showBlogPost($slug)
    {
        $post = \App\Models\BlogPost::where('slug', $slug)->where('is_published', true)->with('author')->firstOrFail();
        $relatedPosts = \App\Models\BlogPost::where('is_published', true)
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();
        $roomTypes = RoomType::limit(3)->get();
        foreach ($roomTypes as $type) {
            $type->available_count = Room::where('room_type_id', $type->id)->where('status', 'available')->count();
        }

        return $this->themePage('blog-show', [
            'title' => $post->title,
            'subtitle' => $post->excerpt,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'roomTypes' => $roomTypes,
            'contactPhone' => Setting::getValue('contact_phone', '+1 (555) 123-4567'),
        ]);
    }

    public function services()
    {
        return $this->themePage('services', [
            'title' => FrontendContent::get('frontend_services_title'),
            'subtitle' => FrontendContent::get('frontend_services_subtitle'),
            'pageBody' => FrontendContent::get('frontend_services_body'),
        ]);
    }

    public function privacy()
    {
        return $this->themePage('privacy', [
            'title' => FrontendContent::get('frontend_privacy_title'),
            'subtitle' => FrontendContent::get('frontend_privacy_subtitle'),
            'body' => FrontendContent::get('frontend_privacy_body'),
        ]);
    }

    public function terms()
    {
        return $this->themePage('terms', [
            'title' => FrontendContent::get('frontend_terms_title'),
            'subtitle' => FrontendContent::get('frontend_terms_subtitle'),
            'body' => FrontendContent::get('frontend_terms_body'),
        ]);
    }

    protected function themePage(string $pageType, array $data = [])
    {
        return view(ThemeHelper::getThemeView('public-page'), array_merge([
            'pageType' => $pageType,
            'title' => 'Welcome',
            'subtitle' => null,
            'hotelName' => Setting::getValue('hotel_name', 'Aetheria Grand Hotel'),
            'currency' => Setting::getValue('currency', 'USD'),
            'globalFooter' => FrontendContent::get('global_footer'),
            'welcomeDescription' => FrontendContent::get('welcome_description'),
            'pageBody' => null,
        ], $data));
    }

    protected function contactData(): array
    {
        $hotel = $this->activeHotel();

        return [
            'contactEmail' => $hotel?->email ?: Setting::getValue('contact_email', 'info@aetheriagrand.com'),
            'contactPhone' => $hotel?->phone ?: Setting::getValue('contact_phone', '+1 (555) 123-4567'),
            'physicalAddress' => $hotel?->address ?: Setting::getValue('physical_address', 'Golden Coast Beach Boulevard, Suite A, Victoria'),
            'mapEmbedUrl' => $hotel?->map_embed_url ?: Setting::getValue('map_address'),
            'mapAddress' => $hotel?->map_embed_url ?: Setting::getValue('map_address'),
        ];
    }

    protected function activeHotel(): ?object
    {
        if (!class_exists(\Plugins\MultiHotel\Models\Hotel::class) || !Schema::hasTable('hotels')) {
            return null;
        }

        $hotelId = session('active_hotel_id');

        if (!$hotelId) {
            return null;
        }

        return \Plugins\MultiHotel\Models\Hotel::where('is_active', true)->find($hotelId);
    }
}

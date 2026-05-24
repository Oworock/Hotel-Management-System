<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Language;
use App\Models\Currency;
use App\Models\Amenity;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function themes()
    {
        $themes = Theme::all();
        $activeTheme = Theme::where('is_active', true)->first();
        return view('super_admin.themes', compact('themes', 'activeTheme'));
    }

    public function activateTheme(Theme $theme)
    {
        Theme::where('is_active', true)->update(['is_active' => false]);
        $theme->update(['is_active' => true]);
        return back()->with('success', 'Theme activated successfully');
    }

    public function updateThemeColors(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'background' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'text' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $colors = $theme->colors ?? [];
        $colors = array_merge($colors, $validated);
        $theme->update(['colors' => $colors]);

        return back()->with('success', 'Colors updated successfully');
    }

    public function languages()
    {
        $languages = Language::orderBy('order')->get();
        return view('super_admin.languages', compact('languages'));
    }

    public function storeLanguage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:languages|max:5',
            'flag_emoji' => 'nullable|string|max:2',
        ]);

        Language::create($validated);
        return back()->with('success', 'Language added successfully');
    }

    public function updateLanguage(Request $request, Language $language)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|unique:languages,code,{$language->id}|max:5",
            'flag_emoji' => 'nullable|string|max:2',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        if ($request->input('is_default')) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($validated);
        return back()->with('success', 'Language updated successfully');
    }

    public function deleteLanguage(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete default language');
        }
        $language->delete();
        return back()->with('success', 'Language deleted successfully');
    }

    public function currencies()
    {
        $currencies = Currency::all();
        return view('super_admin.currencies', compact('currencies'));
    }

    public function storeCurrency(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:currencies|max:3',
            'symbol' => 'required|string|max:5',
            'exchange_rate' => 'required|numeric|min:0.01',
        ]);

        Currency::create($validated);
        return back()->with('success', 'Currency added successfully');
    }

    public function updateCurrency(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => "required|string|unique:currencies,code,{$currency->id}|max:3",
            'symbol' => 'required|string|max:5',
            'exchange_rate' => 'required|numeric|min:0.01',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if ($request->input('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        $currency->update($validated);
        return back()->with('success', 'Currency updated successfully');
    }

    public function deleteCurrency(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'Cannot delete default currency');
        }
        $currency->delete();
        return back()->with('success', 'Currency deleted successfully');
    }

    public function amenities()
    {
        $amenities = Amenity::orderBy('order')->get();
        return view('super_admin.amenities', compact('amenities'));
    }

    public function storeAmenity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'category' => 'required|in:room,hotel,dining,activity,service',
            'order' => 'integer|min:0',
        ]);

        Amenity::create($validated);
        return back()->with('success', 'Amenity added successfully');
    }

    public function updateAmenity(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'category' => 'required|in:room,hotel,dining,activity,service',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $amenity->update($validated);
        return back()->with('success', 'Amenity updated successfully');
    }

    public function deleteAmenity(Amenity $amenity)
    {
        $amenity->delete();
        return back()->with('success', 'Amenity deleted successfully');
    }

    public function specialOffers()
    {
        $offers = SpecialOffer::latest()->get();
        return view('super_admin.special-offers', compact('offers'));
    }

    public function storeSpecialOffer(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:discount_percent,discount_fixed,free_nights,free_upgrade',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'max_bookings' => 'nullable|integer|min:1',
        ]);

        SpecialOffer::create($validated);
        return back()->with('success', 'Special offer created successfully');
    }

    public function updateSpecialOffer(Request $request, SpecialOffer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:discount_percent,discount_fixed,free_nights,free_upgrade',
            'value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'max_bookings' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $offer->update($validated);
        return back()->with('success', 'Special offer updated successfully');
    }

    public function deleteSpecialOffer(SpecialOffer $offer)
    {
        $offer->delete();
        return back()->with('success', 'Special offer deleted successfully');
    }
}

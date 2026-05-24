<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Language;
use App\Models\Translation;
use App\Helpers\TranslationHelper;
use App\Models\Currency;
use App\Models\Amenity;
use App\Models\RoomType;
use App\Models\SpecialOffer;
use App\Models\NavigationMenuItem;
use App\Models\Page;
use App\Support\FrontendContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ThemeController extends Controller
{
    public function themes()
    {
        $this->normalizeActiveTheme();

        $themes = Theme::orderByDesc('is_active')->orderByDesc('is_default')->orderBy('name')->get();
        $activeTheme = Theme::where('is_active', true)->first();
        return view('super_admin.themes', compact('themes', 'activeTheme'));
    }

    public function frontendContent()
    {
        $contentKeys = $this->frontendContentKeys();
        $settings = [];

        foreach ($contentKeys as $key => $meta) {
            $settings[$key] = FrontendContent::get($key, $meta['default']);
        }

        $menuItems = Schema::hasTable('navigation_menu_items')
            ? NavigationMenuItem::with('page')->orderBy('order')->orderBy('id')->get()
            : collect();
        $pages = Schema::hasTable('pages')
            ? Page::where('is_active', true)->orderBy('title')->get()
            : collect();
        $routeOptions = $this->publicRouteOptions();

        return view('super_admin.frontend-content', compact('contentKeys', 'settings', 'menuItems', 'pages', 'routeOptions'));
    }

    public function updateFrontendContent(Request $request)
    {
        $contentKeys = $this->frontendContentKeys();
        $rules = [];
        foreach ($contentKeys as $key => $meta) {
            $rules[$key] = 'nullable|string';
        }

        $data = $request->validate($rules);
        $submittedKeys = collect(array_keys($data))->intersect(array_keys($contentKeys));

        if ($submittedKeys->isEmpty()) {
            return back()->withErrors(['frontend_content' => 'No frontend content fields were submitted. Nothing was changed.']);
        }

        foreach ($submittedKeys as $key) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value === '') {
                $value = FrontendContent::get($key, $contentKeys[$key]['default'] ?? '');
            }

            \App\Models\Setting::setValue($key, $value);
        }

        return back()->with('success', 'Frontend text updated successfully.');
    }

    public function prefillFrontendContent()
    {
        $updated = 0;

        foreach (FrontendContent::defaults() as $key => $value) {
            \App\Models\Setting::setValue($key, $value);
            $updated++;
        }

        return redirect()
            ->route('super_admin.frontend_content')
            ->with('success', "{$updated} frontend content fields loaded with demo text.");
    }

    public function storeNavigationMenuItem(Request $request)
    {
        abort_unless(Schema::hasTable('navigation_menu_items'), 503, 'Navigation menu table has not been migrated yet.');
        NavigationMenuItem::create($this->validateNavigationMenuItem($request));

        return back()->with('success', 'Menu item added successfully.');
    }

    public function updateNavigationMenuItem(Request $request, NavigationMenuItem $menuItem)
    {
        abort_unless(Schema::hasTable('navigation_menu_items'), 503, 'Navigation menu table has not been migrated yet.');
        $menuItem->update($this->validateNavigationMenuItem($request));

        return back()->with('success', 'Menu item updated successfully.');
    }

    public function deleteNavigationMenuItem(NavigationMenuItem $menuItem)
    {
        abort_unless(Schema::hasTable('navigation_menu_items'), 503, 'Navigation menu table has not been migrated yet.');
        $menuItem->delete();

        return back()->with('success', 'Menu item deleted successfully.');
    }

    protected function validateNavigationMenuItem(Request $request): array
    {
        $validated = $request->validate([
            'label' => 'required|string|max:80',
            'type' => 'required|in:route,url,page',
            'url' => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:120',
            'page_id' => 'nullable|exists:pages,id',
            'target' => 'nullable|in:_self,_blank',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['target'] = $validated['target'] ?? '_self';
        $validated['order'] = $validated['order'] ?? 0;

        if ($validated['type'] !== 'url') {
            $validated['url'] = null;
        }
        if ($validated['type'] !== 'route') {
            $validated['route_name'] = null;
        }
        if ($validated['type'] !== 'page') {
            $validated['page_id'] = null;
        }

        return $validated;
    }

    protected function frontendContentKeys(): array
    {
        return FrontendContent::fields();
    }

    protected function publicRouteOptions(): array
    {
        return [
            'home' => 'Home',
            'rooms' => 'Rooms',
            'services' => 'Services',
            'gallery' => 'Gallery',
            'faqs' => 'FAQs',
            'blog.index' => 'Blog',
            'about' => 'About',
            'contact' => 'Contact',
            'testimonials' => 'Testimonials',
            'privacy' => 'Privacy',
            'terms' => 'Terms',
        ];
    }

    public function activateTheme(Theme $theme)
    {
        DB::transaction(function () use ($theme) {
            Theme::query()->update(['is_active' => false]);
            $theme->update(['is_active' => true]);
            $this->syncThemeSettings($theme);
        });

        Cache::forget('active_theme');

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

        if ($theme->is_active) {
            $this->syncThemeSettings($theme);
        }

        Cache::forget('active_theme');

        return back()->with('success', 'Colors updated successfully');
    }

    public function updateThemeContent(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'hero_label' => 'nullable|string|max:120',
            'booking_title' => 'nullable|string|max:120',
            'side_title' => 'nullable|string|max:120',
            'side_text' => 'nullable|string|max:500',
            'footer_note' => 'nullable|string|max:500',
            'nav_cta_label' => 'nullable|string|max:40',
            'show_plugin_links' => 'nullable|boolean',
        ]);

        $settings = $theme->settings ?? [];
        $settings['content'] = [
            'hero_label' => $validated['hero_label'] ?? null,
            'booking_title' => $validated['booking_title'] ?? null,
            'side_title' => $validated['side_title'] ?? null,
            'side_text' => $validated['side_text'] ?? null,
            'footer_note' => $validated['footer_note'] ?? null,
            'nav_cta_label' => $validated['nav_cta_label'] ?? null,
            'show_plugin_links' => $request->boolean('show_plugin_links', true),
        ];

        $theme->update(['settings' => $settings]);
        Cache::forget('active_theme');

        return back()->with('success', 'Theme content updated successfully');
    }

    protected function syncThemeSettings(Theme $theme): void
    {
        $colors = $theme->colors ?? [];

        foreach (['primary' => 'primary_color', 'secondary' => 'secondary_color'] as $themeKey => $settingKey) {
            if (!empty($colors[$themeKey])) {
                \App\Models\Setting::setValue($settingKey, $colors[$themeKey]);
            }
        }
    }

    protected function normalizeActiveTheme(): void
    {
        $activeThemes = Theme::where('is_active', true)->latest('updated_at')->get();

        if ($activeThemes->count() === 1) {
            return;
        }

        $themeToKeep = $activeThemes->first()
            ?? Theme::where('is_default', true)->first()
            ?? Theme::first();

        if (!$themeToKeep) {
            return;
        }

        DB::transaction(function () use ($themeToKeep) {
            Theme::whereKeyNot($themeToKeep->getKey())->update(['is_active' => false]);
            $themeToKeep->update(['is_active' => true]);
            $this->syncThemeSettings($themeToKeep);
        });

        Cache::forget('active_theme');
    }

    public function languages()
    {
        TranslationHelper::ensureEnglishCatalog();
        $languages = Language::orderBy('order')->get();
        $translations = Schema::hasTable('translations')
            ? Translation::orderBy('locale')->orderBy('group')->orderBy('key')->paginate(40)
            : collect();
        return view('super_admin.languages', compact('languages', 'translations'));
    }

    public function setLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|max:10',
        ]);

        if (Schema::hasTable('languages')) {
            $exists = Language::where('code', $validated['locale'])->where('is_active', true)->exists();
            abort_unless($exists, 404);
        }

        session(['locale' => $validated['locale']]);

        return back();
    }

    public function storeTranslation(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|max:10',
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:255',
            'source_text' => 'nullable|string',
            'value' => 'nullable|string',
        ]);

        Translation::updateOrCreate(
            ['locale' => $validated['locale'], 'key' => $validated['key']],
            [
                'group' => $validated['group'],
                'source_text' => $validated['source_text'] ?? ($validated['locale'] === 'en' ? ($validated['value'] ?? '') : null),
                'value' => $validated['value'] ?? '',
            ]
        );

        Cache::forget("translation.{$validated['locale']}.{$validated['key']}");

        return back()->with('success', 'Translation saved successfully');
    }

    public function updateTranslation(Request $request, Translation $translation)
    {
        $validated = $request->validate([
            'locale' => 'required|string|max:10',
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:255',
            'source_text' => 'nullable|string',
            'value' => 'nullable|string',
        ]);

        $oldLocale = $translation->locale;
        $oldKey = $translation->key;
        $translation->update($validated);

        Cache::forget("translation.{$oldLocale}.{$oldKey}");
        Cache::forget("translation.{$translation->locale}.{$translation->key}");

        return back()->with('success', 'Translation updated successfully');
    }

    public function syncTranslations()
    {
        $created = TranslationHelper::ensureEnglishCatalog();

        Language::where('code', '!=', 'en')->pluck('code')->each(function ($locale) {
            TranslationHelper::copyEnglishToLocale($locale);
        });

        return back()->with('success', "Translation catalog synced. {$created} new English strings added.");
    }

    public function deleteTranslation(Translation $translation)
    {
        $locale = $translation->locale;
        $key = $translation->key;
        $translation->delete();
        Cache::forget("translation.{$locale}.{$key}");

        return back()->with('success', 'Translation deleted successfully');
    }

    public function storeLanguage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:languages|max:5',
            'flag_emoji' => 'nullable|string|max:2',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');

        if ($validated['is_default']) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language = Language::create($validated);
        TranslationHelper::copyEnglishToLocale($language->code);

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

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');

        $language->update($validated);
        TranslationHelper::copyEnglishToLocale($language->code);

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
        $amenities = Amenity::orderBy('category')->orderBy('order')->orderBy('name')->get();
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
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
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

        $validated['is_active'] = $request->boolean('is_active');
        $oldName = $amenity->name;
        $amenity->update($validated);

        if ($oldName !== $amenity->name) {
            RoomType::all()->each(function (RoomType $roomType) use ($oldName, $amenity) {
                $amenities = collect($roomType->amenities ?? [])
                    ->map(fn ($item) => $item === $oldName ? $amenity->name : $item)
                    ->unique()
                    ->values()
                    ->all();
                if ($amenities !== ($roomType->amenities ?? [])) {
                    $roomType->update(['amenities' => $amenities]);
                }
            });
        }

        return back()->with('success', 'Amenity updated successfully');
    }

    public function deleteAmenity(Amenity $amenity)
    {
        $oldName = $amenity->name;
        $amenity->delete();

        RoomType::all()->each(function (RoomType $roomType) use ($oldName) {
            $amenities = collect($roomType->amenities ?? [])
                ->reject(fn ($item) => $item === $oldName)
                ->values()
                ->all();
            if ($amenities !== ($roomType->amenities ?? [])) {
                $roomType->update(['amenities' => $amenities]);
            }
        });

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

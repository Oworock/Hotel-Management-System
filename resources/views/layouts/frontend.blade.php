<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel') }}</title>
    <meta name="color-scheme" content="dark light">
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const initialTheme = storedTheme === 'light' || storedTheme === 'dark' ? storedTheme : 'dark';
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>
    @if(View::hasSection('meta_title'))
        <meta name="title" content="@yield('meta_title')">
    @else
        <meta name="title" content="{{ \App\Models\Setting::getValue('meta_title', 'StayFlow - Premium Hotel Management System') }}">
    @endif
    @if(View::hasSection('meta_description'))
        <meta name="description" content="@yield('meta_description')">
    @else
        <meta name="description" content="{{ \App\Models\Setting::getValue('meta_description', 'Experience premier accommodation and elite booking facilities.') }}">
    @endif
    @if(View::hasSection('meta_keywords'))
        <meta name="keywords" content="@yield('meta_keywords')">
    @else
        <meta name="keywords" content="{{ \App\Models\Setting::getValue('meta_keywords', 'hotel, booking, resort, luxury suite') }}">
    @endif
    <link rel="stylesheet" href="/css/style.css">
    @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteManifest = file_exists($viteManifestPath) ? json_decode(file_get_contents($viteManifestPath), true) : [];
        $viteCss = $viteManifest['resources/css/app.css']['file'] ?? null;
        $viteJs = $viteManifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if($viteCss)
        <link rel="stylesheet" href="/build/{{ $viteCss }}">
    @endif
    @if($viteJs)
        <script type="module" src="/build/{{ $viteJs }}"></script>
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @php
            $activeTheme = \App\Helpers\ThemeHelper::getActiveTheme();
            $themeColors = $activeTheme?->colors ?? [];
            $primary = $themeColors['primary'] ?? \App\Models\Setting::getValue('primary_color', '#6e44ff');
            $secondary = $themeColors['secondary'] ?? \App\Models\Setting::getValue('secondary_color', '#f44496');
            $accent = $themeColors['accent'] ?? '#10B981';
            $background = $themeColors['background'] ?? '#F9FAFB';
            $text = $themeColors['text'] ?? '#111827';
            $themeHeaderView = $activeTheme ? "themes.{$activeTheme->slug}.partials.header" : null;
            $themeFooterView = $activeTheme ? "themes.{$activeTheme->slug}.partials.footer" : null;
        @endphp
        :root {
            --primary: {{ $primary }};
            --primary-light: color-mix(in srgb, {{ $primary }} 75%, white);
            --primary-glow: color-mix(in srgb, {{ $primary }} 15%, transparent);
            --secondary: {{ $secondary }};
            --secondary-light: color-mix(in srgb, {{ $secondary }} 75%, white);
            --secondary-glow: color-mix(in srgb, {{ $secondary }} 15%, transparent);
            --accent: {{ $accent }};
            --theme-background: {{ $background }};
            --theme-text: {{ $text }};
        }
        [data-theme="dark"] {
            --primary: {{ $primary }};
            --primary-light: color-mix(in srgb, {{ $primary }} 85%, white);
            --primary-glow: color-mix(in srgb, {{ $primary }} 25%, transparent);
            --secondary: {{ $secondary }};
            --secondary-light: color-mix(in srgb, {{ $secondary }} 85%, white);
            --secondary-glow: color-mix(in srgb, {{ $secondary }} 25%, transparent);
            --accent: {{ $accent }};
        }

        /* Nav Layout */
        .public-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2rem;
            background: var(--surface-glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 999;
            transition: all var(--transition-fast);
        }

        .logo-link {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .logo-img {
            max-height: 40px;
            object-fit: contain;
            display: block;
        }

        .public-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .public-menu a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color var(--transition-fast);
        }

        .public-menu a:hover {
            color: var(--primary);
        }
        
        .public-menu a.active {
            color: var(--primary);
            font-weight: 700;
        }

        /* Mobile Nav Toggle */
        .nav-toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-primary);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .public-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--surface);
                border-bottom: 1px solid var(--border-color);
                padding: 1.5rem;
                gap: 1.25rem;
                box-shadow: var(--shadow-md);
            }
            .public-menu.active {
                display: flex;
            }
            .nav-toggle-btn {
                display: block;
            }
        }

        .public-footer {
            background: var(--surface);
            border-top: 1px solid var(--border-color);
            padding: 4rem 2rem 2rem 2rem;
            margin-top: auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto 3rem auto;
            text-align: left;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        .footer-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
            color: var(--text-primary);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .footer-links a:hover {
            color: var(--primary);
        }
    </style>
    @yield('styles')
</head>
<body>
    @if(session()->has('impersonated_by'))
        <div class="impersonation-banner" style="background: rgba(239, 68, 68, 0.9); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1.5rem; font-size: 0.9rem; font-weight: 600; position: sticky; top: 0; z-index: 9999; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-user-secret"></i>
                <span>Impersonation Mode: Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})</span>
            </div>
            <form action="{{ route('impersonate.leave') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-sm" style="background: #fff; color: #dc2626; border: none; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-weight: 700; cursor: pointer; font-size: 0.8rem;">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Return to Admin
                </button>
            </form>
        </div>
    @endif

    <!-- Announcement / Global Header -->
    @if($announcement = \App\Models\Setting::getValue('global_header'))
        <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; text-align: center; padding: 0.6rem 1rem; font-size: 0.875rem; font-weight: 500; position: relative; z-index: 1000;">
            {!! \App\Support\HtmlSanitizer::clean($announcement) !!}
        </div>
    @endif

    @if($themeHeaderView && view()->exists($themeHeaderView))
        @include($themeHeaderView)
    @else
    <!-- Navigation Header -->
    <nav class="public-nav">
        <a href="/" class="logo-link">
            @php
                $logoType = \App\Models\Setting::getValue('logo_type', 'text');
                $logoImage = \App\Models\Setting::getValue('logo_image');
            @endphp
            @if($logoType === 'image' && !empty($logoImage))
                <img src="{{ $logoImage }}" alt="{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria') }}" class="logo-img">
            @else
                {!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria')) !!}
            @endif
        </a>

        <ul class="public-menu" id="nav-menu">
            @include('themes.partials.main-menu-links')
            @php
                $tuckShopEnabled = \App\Models\Setting::getValue('ecommerce_tuck_shop_enabled', '1') === '1';
                $restaurantEnabled = \App\Models\Setting::getValue('ecommerce_restaurant_enabled', '1') === '1';
            @endphp
            @if($tuckShopEnabled && Route::has('shop.index'))
                <li><a href="{{ route('shop.index') }}" class="{{ Request::is('shop') ? 'active' : '' }}">{{ \App\Models\Setting::getValue('ecommerce_tuck_shop_name', 'Tuck Shop') }}</a></li>
            @endif
            @if($restaurantEnabled && Route::has('restaurant.index'))
                <li><a href="{{ route('restaurant.index') }}" class="{{ Request::is('restaurant') ? 'active' : '' }}">{{ \App\Models\Setting::getValue('ecommerce_restaurant_name', 'Restaurant') }}</a></li>
            @endif
            @if(view()->exists('multi_hotel.public-selector'))
                <li>@include('multi_hotel.public-selector')</li>
            @endif
        </ul>

        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <!-- Light/Dark Toggle -->
            <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme" style="border: none; background: transparent; cursor: pointer; color: var(--text-primary); font-size: 1.15rem; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; hover: background: var(--border-color);">
                <i class="fa-solid fa-moon" id="theme-icon"></i>
            </button>
            
            @if(Auth::check())
                @php
                    $dashboardRoute = route('customer.dashboard');
                    if (auth()->user()->isSuperAdmin()) $dashboardRoute = route('super_admin.dashboard');
                    elseif (auth()->user()->isAdmin()) $dashboardRoute = route('admin.dashboard');
                    elseif (auth()->user()->isStaff()) $dashboardRoute = route('staff.dashboard');
                @endphp
                <a href="{{ $dashboardRoute }}" class="btn btn-primary" style="text-decoration: none; padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline" style="text-decoration: none; padding: 0.5rem 1.25rem; font-size: 0.9rem;">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary" style="text-decoration: none; padding: 0.5rem 1.25rem; font-size: 0.9rem;">Book Online</a>
            @endif

            <button class="nav-toggle-btn" id="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>
    @endif

    <!-- Main Content Area -->
    <main>
        @yield('content')
    </main>

    @if($themeFooterView && view()->exists($themeFooterView))
        @include($themeFooterView)
    @else
    <!-- Global Footer -->
    <footer class="public-footer">
        <div class="footer-grid">
            <div>
                <a href="/" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.35rem; color: var(--primary); margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    @if($logoType === 'image' && !empty($logoImage))
                        <img src="{{ $logoImage }}" alt="Aetheria" class="logo-img" style="max-height: 32px;">
                    @else
                        {!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('logo_text', 'Aetheria')) !!}
                    @endif
                </a>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; max-width: 280px;">
                    {{ \App\Models\Setting::getValue('welcome_description', 'A luxury sanctuary where contemporary design meets pristine nature.') }}
                </p>
            </div>
            <div>
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rooms">Accommodations</a></li>
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/contact">Get In Touch</a></li>
                    @foreach($navPages->where('show_in_nav', true) as $p)
                        <li><a href="/pages/{{ $p->slug }}">{{ $p->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">For Customers</h4>
                <ul class="footer-links">
                    <li><a href="/login">Sign In</a></li>
                    <li><a href="/register">Create Account</a></li>
                    <li><a href="/#rooms">Check Availability</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Contact Details</h4>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-phone" style="color: var(--primary); margin-right: 0.5rem;"></i> {{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }}
                </p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-envelope" style="color: var(--primary); margin-right: 0.5rem;"></i> {{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}
                </p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                    <i class="fa-solid fa-location-dot" style="color: var(--primary); margin-right: 0.5rem;"></i> {{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach, Suite A') }}
                </p>
            </div>
        </div>
        <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; text-align: center;">
            <p style="font-size: 0.85rem; color: var(--text-muted);">
                {!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('global_footer', '© 2026 Aetheria Grand Hotel. All rights reserved.')) !!}
            </p>
        </div>
    </footer>
    @endif

    @php
        $promoEnabled = \App\Models\Setting::getValue('promo_popup_enabled', '0') === '1';
        $promoTitle = \App\Models\Setting::getValue('promo_popup_title', 'Special Offer!');
        $promoContent = \App\Models\Setting::getValue('promo_popup_content', 'Get an exclusive discount today.');
        $promoImage = \App\Models\Setting::getValue('promo_popup_image', '');
        $promoCoupon = \App\Models\Setting::getValue('promo_popup_coupon', '');
    @endphp
    @if($promoEnabled)
        <div class="promo-popup-backdrop" id="promoPopup" aria-hidden="true">
            <div class="promo-popup-panel" role="dialog" aria-modal="true" aria-labelledby="promoPopupTitle">
                <button type="button" class="promo-popup-close" onclick="closePromoPopup()" aria-label="Close promo"><i class="fa-solid fa-xmark"></i></button>
                @if($promoImage)
                    <img src="{{ $promoImage }}" alt="" class="promo-popup-image">
                @endif
                <div class="promo-popup-body">
                    <span class="promo-popup-kicker">Limited offer</span>
                    <h2 id="promoPopupTitle">{{ $promoTitle }}</h2>
                    <p>{{ $promoContent }}</p>
                    @if($promoCoupon)
                        <div class="promo-popup-coupon">{{ $promoCoupon }}</div>
                    @endif
                    <a href="{{ route('rooms') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;text-decoration:none;">
                        <i class="fa-solid fa-calendar-check"></i> Book a Room
                    </a>
                </div>
            </div>
        </div>
        <style>
            .promo-popup-backdrop { position: fixed; inset: 0; z-index: 3000; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(2, 6, 23, 0.72); backdrop-filter: blur(8px); }
            .promo-popup-backdrop.active { display: flex; }
            .promo-popup-panel { position: relative; width: min(92vw, 760px); display: grid; grid-template-columns: 0.9fr 1.1fr; overflow: hidden; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface); color: var(--text-primary); box-shadow: var(--shadow-xl, 0 24px 70px rgba(0,0,0,0.35)); }
            .promo-popup-image { width: 100%; height: 100%; min-height: 360px; object-fit: cover; }
            .promo-popup-body { padding: 2rem; display: flex; flex-direction: column; justify-content: center; gap: 1rem; }
            .promo-popup-kicker { color: var(--primary); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0; }
            .promo-popup-body h2 { margin: 0; font-size: clamp(1.6rem, 4vw, 2.4rem); line-height: 1.05; color: var(--text-primary); }
            .promo-popup-body p { margin: 0; color: var(--text-secondary); line-height: 1.65; }
            .promo-popup-coupon { width: fit-content; border: 1px dashed var(--primary); color: var(--primary); background: var(--primary-glow); padding: 0.6rem 0.9rem; border-radius: 6px; font-weight: 800; letter-spacing: 0; }
            .promo-popup-close { position: absolute; top: 0.75rem; right: 0.75rem; z-index: 2; width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--border-color); background: var(--surface); color: var(--text-primary); cursor: pointer; }
            @media (max-width: 720px) { .promo-popup-panel { grid-template-columns: 1fr; } .promo-popup-image { min-height: 210px; } .promo-popup-body { padding: 1.5rem; } }
        </style>
    @endif

    <script>
        // Light/Dark Theme Switching
        const html = document.documentElement;
        const themeIcon = document.getElementById('theme-icon');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        setTheme(savedTheme);

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (themeIcon) {
                themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        function toggleTheme() {
            const currentTheme = html.getAttribute('data-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        }

        // Mobile Menu toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('active');
        }

        const promoPopup = document.getElementById('promoPopup');
        const promoKey = 'promo-popup-{{ md5($promoTitle . '|' . $promoCoupon) }}';
        if (promoPopup && !sessionStorage.getItem(promoKey)) {
            window.setTimeout(() => {
                promoPopup.classList.add('active');
                promoPopup.setAttribute('aria-hidden', 'false');
                sessionStorage.setItem(promoKey, 'shown');
            }, 900);
        }

        function closePromoPopup() {
            if (!promoPopup) return;
            promoPopup.classList.remove('active');
            promoPopup.setAttribute('aria-hidden', 'true');
        }
    </script>
    @yield('scripts')
</body>
</html>

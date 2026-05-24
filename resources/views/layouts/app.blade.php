<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aetheria HMS') - Hotel Management System</title>
    <meta name="color-scheme" content="dark light">
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            const initialTheme = storedTheme === 'light' || storedTheme === 'dark' ? storedTheme : 'dark';
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>
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
            $background = $themeColors['background'] ?? null;
            $text = $themeColors['text'] ?? null;
        @endphp
        :root {
            --primary: {{ $primary }};
            --primary-light: color-mix(in srgb, {{ $primary }} 75%, white);
            --primary-glow: color-mix(in srgb, {{ $primary }} 15%, transparent);
            --secondary: {{ $secondary }};
            --secondary-light: color-mix(in srgb, {{ $secondary }} 75%, white);
            --secondary-glow: color-mix(in srgb, {{ $secondary }} 15%, transparent);
            --accent: {{ $accent }};
            @if($background)
                --theme-background: {{ $background }};
            @endif
            @if($text)
                --theme-text: {{ $text }};
            @endif
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
    </style>
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

    @if($announcement = \App\Models\Setting::getValue('global_header'))
        <div class="announcement-bar" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; text-align: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; position: relative; z-index: 1000; box-shadow: var(--shadow-sm);">
            <span>{!! $announcement !!}</span>
        </div>
    @endif

    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-brand" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.5rem;">
                    @if(\App\Models\Setting::getValue('logo_type', 'text') === 'image' && \App\Models\Setting::getValue('logo_image'))
                        <img src="{{ \App\Models\Setting::getValue('logo_image') }}" alt="Logo" style="max-height: 40px; width: auto; object-fit: contain;">
                    @else
                        {!! \App\Models\Setting::getValue('logo_text', \App\Models\Setting::getValue('platform_logo', '<i class="fa-solid fa-hotel"></i> Aetheria')) !!}
                    @endif
                </a>
                <button class="theme-toggle" style="display: none;" id="sidebar-close" onclick="toggleSidebar()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <ul class="sidebar-menu">
                @if(auth()->user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <li class="sidebar-menu-item {{ Route::is('super_admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.dashboard') }}">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.settings') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.settings') }}">
                            <i class="fa-solid fa-gears"></i> System Config
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.users') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.users') }}">
                            <i class="fa-solid fa-users-gear"></i> User Management
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.plugins') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.plugins') }}">
                            <i class="fa-solid fa-puzzle-piece"></i> Plugins
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.themes') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.themes') }}">
                            <i class="fa-solid fa-palette"></i> Themes
                        </a>
                    </li>
                    <li class="sidebar-menu-header">Website Content</li>
                    <li class="sidebar-menu-item {{ Route::is('admin.faqs') ? 'active' : '' }}">
                        <a href="{{ route('admin.faqs') }}"><i class="fa-solid fa-circle-question"></i> FAQs</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.gallery') ? 'active' : '' }}">
                        <a href="{{ route('admin.gallery') }}"><i class="fa-solid fa-images"></i> Gallery</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.testimonials') ? 'active' : '' }}">
                        <a href="{{ route('admin.testimonials') }}"><i class="fa-solid fa-comments"></i> Testimonials</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.blog') ? 'active' : '' }}">
                        <a href="{{ route('admin.blog') }}"><i class="fa-solid fa-newspaper"></i> Blog</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.frontend_content') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.frontend_content') }}"><i class="fa-solid fa-pen-nib"></i> Frontend Editor</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.languages') || Route::is('super_admin.currencies') || Route::is('super_admin.amenities') || Route::is('super_admin.offers') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.languages') }}"><i class="fa-solid fa-globe"></i> Localization</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('super_admin.developer') ? 'active' : '' }}">
                        <a href="{{ route('super_admin.developer') }}">
                            <i class="fa-solid fa-code"></i> Developer Portal
                        </a>
                    </li>
                    @if(Route::has('super_admin.hotels'))
                        <li class="sidebar-menu-item {{ Route::is('super_admin.hotels') ? 'active' : '' }}">
                            <a href="{{ route('super_admin.hotels') }}">
                                <i class="fa-solid fa-hotel"></i> Hotels Manager
                            </a>
                        </li>
                    @endif
                    @if(Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    @if(Route::has('admin.channel_manager.index'))
                        <li class="sidebar-menu-header">Channel Manager</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.channel_manager.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.channel_manager.index') }}">
                                <i class="fa-solid fa-cloud-arrow-up"></i> OTA Sync
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @elseif(auth()->user()->isAdmin())
                    <!-- Admin Menu: Strictly 8 Menus -->
                    <li class="sidebar-menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.rooms') ? 'active' : '' }}">
                        <a href="{{ route('admin.rooms') }}">
                            <i class="fa-solid fa-door-open"></i> Rooms
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.bookings') ? 'active' : '' }}">
                        <a href="{{ route('admin.bookings') }}">
                            <i class="fa-solid fa-calendar-check"></i> Bookings
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.guests') ? 'active' : '' }}">
                        <a href="{{ route('admin.guests') }}">
                            <i class="fa-solid fa-user-group"></i> Guests
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.payments') ? 'active' : '' }}">
                        <a href="{{ route('admin.payments') }}">
                            <i class="fa-solid fa-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.reports') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports') }}">
                            <i class="fa-solid fa-chart-column"></i> Reports
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.users') ? 'active' : '' }}">
                        <a href="{{ route('admin.users') }}">
                            <i class="fa-solid fa-users"></i> Users
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}">
                            <i class="fa-solid fa-gears"></i> Settings
                        </a>
                    </li>
                    <li class="sidebar-menu-header">Website Content</li>
                    <li class="sidebar-menu-item {{ Route::is('admin.faqs') ? 'active' : '' }}">
                        <a href="{{ route('admin.faqs') }}"><i class="fa-solid fa-circle-question"></i> FAQs</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.gallery') ? 'active' : '' }}">
                        <a href="{{ route('admin.gallery') }}"><i class="fa-solid fa-images"></i> Gallery</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.testimonials') ? 'active' : '' }}">
                        <a href="{{ route('admin.testimonials') }}"><i class="fa-solid fa-comments"></i> Testimonials</a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('admin.blog') ? 'active' : '' }}">
                        <a href="{{ route('admin.blog') }}"><i class="fa-solid fa-newspaper"></i> Blog</a>
                    </li>
                    @if(Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    @if(Route::has('admin.channel_manager.index'))
                        <li class="sidebar-menu-header">Channel Manager</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.channel_manager.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.channel_manager.index') }}">
                                <i class="fa-solid fa-cloud-arrow-up"></i> OTA Sync
                            </a>
                        </li>
                    @endif
                @elseif(auth()->user()->isStaff())
                    <!-- Staff Menu -->
                    <li class="sidebar-menu-item {{ Route::is('staff.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('staff.dashboard') }}">
                            <i class="fa-solid fa-clipboard-list"></i> Shift & Rooms
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('staff.bookings') ? 'active' : '' }}">
                        <a href="{{ route('staff.bookings') }}">
                            <i class="fa-solid fa-calendar-check"></i> Guest Bookings
                        </a>
                    </li>
                    @if((auth()->user()->hasFunction('manage_tuck_shop') || auth()->user()->hasFunction('manage_restaurant')) && Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @elseif(auth()->user()->isReceptionist())
                    <!-- Receptionist Menu -->
                    <li class="sidebar-menu-item {{ Route::is('receptionist.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('receptionist.dashboard') }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('receptionist.bookings') ? 'active' : '' }}">
                        <a href="{{ route('receptionist.bookings') }}">
                            <i class="fa-solid fa-calendar-check"></i> Bookings
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('receptionist.guests') ? 'active' : '' }}">
                        <a href="{{ route('receptionist.guests') }}">
                            <i class="fa-solid fa-user-group"></i> Guests
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('receptionist.rooms') ? 'active' : '' }}">
                        <a href="{{ route('receptionist.rooms') }}">
                            <i class="fa-solid fa-door-open"></i> Rooms
                        </a>
                    </li>
                    @if((auth()->user()->hasFunction('manage_tuck_shop') || auth()->user()->hasFunction('manage_restaurant')) && Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @elseif(auth()->user()->isKitchenManager())
                    <!-- Kitchen Manager Menu -->
                    <li class="sidebar-menu-item {{ Route::is('kitchen.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('kitchen.dashboard') }}">
                            <i class="fa-solid fa-fire-burner"></i> Kitchen Orders
                        </a>
                    </li>
                    @if((auth()->user()->hasFunction('manage_tuck_shop') || auth()->user()->hasFunction('manage_restaurant')) && Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @elseif(auth()->user()->isCustomer())
                    <!-- Customer Menu -->
                    <li class="sidebar-menu-item {{ Route::is('customer.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('customer.dashboard') }}">
                            <i class="fa-solid fa-bed"></i> Search & Book
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('customer.bookings') ? 'active' : '' }}">
                        <a href="{{ route('customer.bookings') }}">
                            <i class="fa-solid fa-suitcase"></i> My Bookings
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @else
                    <!-- Custom / Dynamic Role Menu -->
                    @if(auth()->user()->hasFunction('manage_rooms'))
                        <li class="sidebar-menu-item {{ Route::is('admin.rooms') ? 'active' : '' }}">
                            <a href="{{ route('admin.rooms') }}">
                                <i class="fa-solid fa-door-open"></i> Rooms
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_bookings'))
                        <li class="sidebar-menu-item {{ Route::is('admin.bookings') ? 'active' : '' }}">
                            <a href="{{ route('admin.bookings') }}">
                                <i class="fa-solid fa-calendar-check"></i> Bookings
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_guests'))
                        <li class="sidebar-menu-item {{ Route::is('admin.guests') ? 'active' : '' }}">
                            <a href="{{ route('admin.guests') }}">
                                <i class="fa-solid fa-user-group"></i> Guests
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_payments'))
                        <li class="sidebar-menu-item {{ Route::is('admin.payments') ? 'active' : '' }}">
                            <a href="{{ route('admin.payments') }}">
                                <i class="fa-solid fa-credit-card"></i> Payments
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_reports'))
                        <li class="sidebar-menu-item {{ Route::is('admin.reports') ? 'active' : '' }}">
                            <a href="{{ route('admin.reports') }}">
                                <i class="fa-solid fa-chart-column"></i> Reports
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_users'))
                        <li class="sidebar-menu-item {{ Route::is('admin.users') ? 'active' : '' }}">
                            <a href="{{ route('admin.users') }}">
                                <i class="fa-solid fa-users"></i> Users
                            </a>
                        </li>
                    @endif
                    @if((auth()->user()->hasFunction('manage_tuck_shop') || auth()->user()->hasFunction('manage_restaurant')) && Route::has('admin.ecommerce.dashboard'))
                        <li class="sidebar-menu-header">E-commerce</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.dashboard') }}">
                                <i class="fa-solid fa-gauge-high"></i> Shop Dashboard
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.products') ? 'active' : '' }}">
                            <a href="{{ route('admin.products') }}">
                                <i class="fa-solid fa-boxes-stacked"></i> Shop Catalog
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.orders') ? 'active' : '' }}">
                            <a href="{{ route('admin.orders') }}">
                                <i class="fa-solid fa-receipt"></i> Shop Orders
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.analytics') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.analytics') }}">
                                <i class="fa-solid fa-chart-simple"></i> Shop Analytics
                            </a>
                        </li>
                        <li class="sidebar-menu-item {{ Route::is('admin.ecommerce.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.ecommerce.settings') }}">
                                <i class="fa-solid fa-sliders"></i> Shop Settings
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('kitchen_dashboard') && Route::has('kitchen.dashboard'))
                        <li class="sidebar-menu-item {{ Route::is('kitchen.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('kitchen.dashboard') }}">
                                <i class="fa-solid fa-fire-burner"></i> Kitchen Orders
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_settings'))
                        <li class="sidebar-menu-item {{ Route::is('admin.settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings') }}">
                                <i class="fa-solid fa-gears"></i> Settings
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->hasFunction('manage_channel_manager') && Route::has('admin.channel_manager.index'))
                        <li class="sidebar-menu-header">Channel Manager</li>
                        <li class="sidebar-menu-item {{ Route::is('admin.channel_manager.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.channel_manager.index') }}">
                                <i class="fa-solid fa-cloud-arrow-up"></i> OTA Sync
                            </a>
                        </li>
                    @endif
                    <li class="sidebar-menu-item {{ Route::is('profile.edit') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user-gear"></i> My Profile
                        </a>
                    </li>
                @endif
            </ul>
            
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-block" style="border-color: var(--danger); color: var(--danger);">
                        <i class="fa-solid fa-right-from-bracket"></i> Log Out
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Dashboard View -->
        <div class="main-content">
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="theme-toggle" id="sidebar-toggle" onclick="toggleSidebar()" style="display: none;">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <button class="theme-toggle" id="sidebar-collapse-toggle" onclick="toggleSidebarCollapse()" aria-label="Collapse Sidebar">
                        <i class="fa-solid fa-table-columns"></i>
                    </button>
                    <h1 class="topbar-title">@yield('title')</h1>
                </div>
                
                <div class="topbar-actions">
                    @if(Schema::hasTable('hotels') && Route::has('super_admin.hotels.select') && auth()->user()->isSuperAdmin())
                        @php
                            $activeHotelId = session('active_hotel_id');
                            $hotelsList = \Plugins\MultiHotel\Models\Hotel::where('is_active', true)->get();
                        @endphp
                        <form action="{{ route('super_admin.hotels.select') }}" method="POST" id="topbar-hotel-select-form" style="margin: 0; display: inline-flex; align-items: center; margin-right: 0.5rem;">
                            @csrf
                            <div style="position: relative; display: inline-block;">
                                <select name="hotel_id" onchange="document.getElementById('topbar-hotel-select-form').submit()" class="input-field" style="padding: 0.35rem 2rem 0.35rem 0.75rem; font-size: 0.8rem; height: auto; width: 185px; border-radius: var(--radius-sm); background: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); cursor: pointer; appearance: none; -webkit-appearance: none;">
                                    <option value="">All Hotels Workspace</option>
                                    @foreach($hotelsList as $h)
                                        <option value="{{ $h->id }}" {{ $activeHotelId == $h->id ? 'selected' : '' }}>
                                            {{ $h->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.7rem; color: var(--text-secondary); pointer-events: none;"></i>
                            </div>
                        </form>
                    @elseif(Schema::hasTable('hotels') && Route::has('super_admin.hotels.select') && auth()->user()->hotel_id)
                        <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); background: var(--primary-glow); padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); display: inline-flex; align-items: center; gap: 0.25rem; margin-right: 0.5rem;">
                            <i class="fa-solid fa-hotel" style="color: var(--primary); font-size: 0.8rem;"></i> {{ auth()->user()->hotel->name ?? 'Default Property' }}
                        </div>
                    @endif

                    <!-- Light/Dark Mode Switcher -->
                    <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme">
                        <i class="fa-solid fa-moon" id="theme-icon"></i>
                    </button>
                    
                    <!-- User Widget -->
                    <div class="user-profile" id="user-profile-widget" style="position: relative; cursor: pointer;">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name" style="display: flex; align-items: center; gap: 0.25rem;">
                                {{ auth()->user()->name }}
                                <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; color: var(--text-secondary);"></i>
                            </span>
                            <span class="user-role-badge">
                                @switch(auth()->user()->role)
                                    @case('super_admin') Super Admin @break
                                    @case('admin') Admin @break
                                    @case('receptionist') Receptionist @break
                                    @case('kitchen_manager') Kitchen Chef @break
                                    @case('tuck_shop_manager') Tuck Shop Manager @break
                                    @case('restaurant_manager') Restaurant Manager @break
                                    @case('staff') Staff @break
                                    @case('customer') Guest @break
                                    @default {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                @endswitch
                            </span>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div class="profile-dropdown-menu" id="profile-dropdown-menu" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; background: var(--surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); min-width: 180px; z-index: 1000; padding: 0.5rem 0; overflow: hidden; backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); animation: fadeIn 0.2s ease-in-out;">
                            <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: var(--text-primary); text-decoration: none; font-size: 0.9rem; transition: background-color 0.2s;">
                                <i class="fa-solid fa-user-gear" style="color: var(--primary); width: 16px;"></i> Edit Profile
                            </a>
                            @php
                                    $dashRoute = '#';
                                    if(auth()->user()->isSuperAdmin()) $dashRoute = route('super_admin.dashboard');
                                    elseif(auth()->user()->isAdmin()) $dashRoute = route('admin.dashboard');
                                    elseif(auth()->user()->isReceptionist()) $dashRoute = route('receptionist.dashboard');
                                    elseif(auth()->user()->isKitchenManager()) $dashRoute = Route::has('kitchen.dashboard') ? route('kitchen.dashboard') : route('home');
                                    elseif(auth()->user()->isTuckShopManager() || auth()->user()->isRestaurantManager()) $dashRoute = Route::has('admin.products') ? route('admin.products') : route('home');
                                    elseif(auth()->user()->isStaff()) $dashRoute = route('staff.dashboard');
                                    elseif(auth()->user()->isCustomer()) $dashRoute = route('customer.dashboard');
                                    else {
                                        if (auth()->user()->hasFunction('kitchen_dashboard') && Route::has('kitchen.dashboard')) $dashRoute = route('kitchen.dashboard');
                                        elseif (auth()->user()->hasFunction('manage_bookings')) $dashRoute = route('receptionist.dashboard');
                                        elseif ((auth()->user()->hasFunction('manage_tuck_shop') || auth()->user()->hasFunction('manage_restaurant')) && Route::has('admin.products')) $dashRoute = route('admin.products');
                                        elseif (auth()->user()->hasFunction('manage_rooms')) $dashRoute = route('admin.rooms');
                                        elseif (auth()->user()->hasFunction('manage_guests')) $dashRoute = route('admin.guests');
                                        elseif (auth()->user()->hasFunction('manage_payments')) $dashRoute = route('admin.payments');
                                        elseif (auth()->user()->hasFunction('manage_reports')) $dashRoute = route('admin.reports');
                                        elseif (auth()->user()->hasFunction('manage_users')) $dashRoute = route('admin.users');
                                        elseif (auth()->user()->hasFunction('manage_settings')) $dashRoute = route('admin.settings');
                                        else $dashRoute = route('customer.dashboard');
                                    }
                            @endphp
                            <a href="{{ $dashRoute }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: var(--text-primary); text-decoration: none; font-size: 0.9rem; transition: background-color 0.2s;">
                                <i class="fa-solid fa-gauge" style="color: var(--primary); width: 16px;"></i> Dashboard
                            </a>
                            <div style="border-top: 1px solid var(--border-color); margin: 0.5rem 0;"></div>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; color: var(--danger); background: none; border: none; width: 100%; text-align: left; font-size: 0.9rem; cursor: pointer; font-family: inherit; transition: background-color 0.2s;">
                                    <i class="fa-solid fa-right-from-bracket" style="width: 16px;"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="content-body">
                @if(session('success'))
                    <div class="alert alert-success animate-fade-in">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger animate-fade-in">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger animate-fade-in">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div style="display: flex; flex-direction: column;">
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
            @if($footer = \App\Models\Setting::getValue('global_footer'))
                <footer style="margin-top: auto; padding: 1.5rem; text-align: center; border-top: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary);">
                    {!! $footer !!}
                </footer>
            @endif
        </div>
    </div>

    <!-- Script for responsive sidebar and theme toggling -->
    <script>
        // Theme Management
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

        // Sidebar Responsive Toggle
        const dashboardContainer = document.querySelector('.dashboard-container');
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarCollapseToggle = document.getElementById('sidebar-collapse-toggle');
        const sidebarClose = document.getElementById('sidebar-close');

        if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 992) {
            dashboardContainer.classList.add('sidebar-collapsed');
        }

        function checkViewport() {
            if (window.innerWidth <= 992) {
                sidebarToggle.style.display = 'flex';
                sidebarClose.style.display = 'flex';
                sidebarCollapseToggle.style.display = 'none';
                dashboardContainer.classList.remove('sidebar-collapsed');
            } else {
                sidebarToggle.style.display = 'none';
                sidebarClose.style.display = 'none';
                sidebarCollapseToggle.style.display = 'flex';
                sidebar.classList.remove('active');
                if (localStorage.getItem('sidebarCollapsed') === 'true') {
                    dashboardContainer.classList.add('sidebar-collapsed');
                }
            }
        }

        function toggleSidebar() {
            sidebar.classList.toggle('active');
        }

        function toggleSidebarCollapse() {
            dashboardContainer.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', dashboardContainer.classList.contains('sidebar-collapsed'));
        }

        window.addEventListener('resize', checkViewport);
        window.addEventListener('DOMContentLoaded', checkViewport);

        // Profile Dropdown Toggle
        const userProfileWidget = document.getElementById('user-profile-widget');
        const profileDropdownMenu = document.getElementById('profile-dropdown-menu');

        if (userProfileWidget && profileDropdownMenu) {
            userProfileWidget.addEventListener('click', function(e) {
                e.stopPropagation();
                const isDisplayed = profileDropdownMenu.style.display === 'block';
                profileDropdownMenu.style.display = isDisplayed ? 'none' : 'block';
            });

            document.addEventListener('click', function(e) {
                if (!userProfileWidget.contains(e.target)) {
                    profileDropdownMenu.style.display = 'none';
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>

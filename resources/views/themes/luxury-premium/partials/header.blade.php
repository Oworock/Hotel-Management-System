@php
    $hotelName = \App\Models\Setting::getValue('hotel_name', 'Aetheria Grand Hotel');
    $dashboardRoute = auth()->check() && auth()->user()->isSuperAdmin() ? route('super_admin.dashboard') : (auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard'));
@endphp
<header class="luxury-site-header">
    <div class="luxury-header-top">
        <span>{{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }}</span>
        <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
    </div>
    <div class="luxury-header-main">
        <nav class="luxury-site-nav" id="nav-menu">
            @include('themes.partials.main-menu-links', ['menuLimit' => 4, 'includePluginLinks' => false])
        </nav>
        <a href="/" class="luxury-site-brand">{{ $hotelName }}</a>
        <nav class="luxury-site-nav luxury-site-nav-right">
            @include('themes.partials.main-menu-links', ['menuOffset' => 4, 'includePluginLinks' => true])
            @auth
                <a href="{{ $dashboardRoute }}">Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="luxury-reserve">{{ \App\Helpers\ThemeHelper::getThemeContent('nav_cta_label', 'Reserve') }}</a>
            @endauth
        </nav>
        <button class="nav-toggle-btn" id="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu"><i class="fa-solid fa-bars"></i></button>
    </div>
</header>

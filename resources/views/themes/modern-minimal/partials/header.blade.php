@php
    $logoType = \App\Models\Setting::getValue('logo_type', 'text');
    $logoImage = \App\Models\Setting::getValue('logo_image');
    $hotelName = \App\Models\Setting::getValue('hotel_name', 'Aetheria');
    $dashboardRoute = auth()->check() && auth()->user()->isSuperAdmin() ? route('super_admin.dashboard') : (auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard'));
@endphp
<header class="modern-site-header">
    <a href="/" class="modern-site-brand">
        @if($logoType === 'image' && $logoImage)
            <img src="{{ $logoImage }}" alt="{{ $hotelName }}">
        @else
            {!! \App\Support\HtmlSanitizer::clean(\App\Models\Setting::getValue('logo_text', '<i class="fa-solid fa-hotel"></i> Aetheria')) !!}
        @endif
    </a>
    <nav class="modern-site-nav" id="nav-menu">
        @include('themes.partials.main-menu-links')
    </nav>
    <div class="modern-site-actions">
        <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
        @auth
            <a href="{{ $dashboardRoute }}" class="modern-site-cta">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="modern-site-link">Login</a>
            <a href="{{ route('register') }}" class="modern-site-cta">{{ \App\Helpers\ThemeHelper::getThemeContent('nav_cta_label', 'Book') }}</a>
        @endauth
        <button class="nav-toggle-btn" id="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu"><i class="fa-solid fa-bars"></i></button>
    </div>
</header>

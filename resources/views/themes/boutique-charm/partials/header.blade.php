@php
    $dashboardRoute = auth()->check() && auth()->user()->isSuperAdmin() ? route('super_admin.dashboard') : (auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard'));
@endphp
<header class="boutique-site-header">
    <div class="boutique-ribbon">{{ \App\Models\Setting::getValue('global_header', 'Direct bookings include flexible guest support.') }}</div>
    <div class="boutique-header-main">
        <a href="/" class="boutique-site-brand">{{ \App\Models\Setting::getValue('hotel_name', 'Aetheria') }}</a>
        <nav class="boutique-site-nav" id="nav-menu">
            @include('themes.partials.main-menu-links')
        </nav>
        <div class="boutique-site-actions">
            <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
            @auth
                <a href="{{ $dashboardRoute }}" class="boutique-pill">Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="boutique-pill">{{ \App\Helpers\ThemeHelper::getThemeContent('nav_cta_label', 'Book') }}</a>
            @endauth
            <button class="nav-toggle-btn" id="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
</header>

@php
    $dashboardRoute = auth()->check() && auth()->user()->isSuperAdmin() ? route('super_admin.dashboard') : (auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard'));
@endphp
<header class="resort-site-header">
    <a href="/" class="resort-site-brand"><i class="fa-solid fa-sun"></i> {{ \App\Models\Setting::getValue('hotel_name', 'Aetheria') }}</a>
    <nav class="resort-site-nav" id="nav-menu">
        @include('themes.partials.main-menu-links')
    </nav>
    <div class="resort-site-actions">
        <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Toggle Theme"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
        @auth
            <a href="{{ $dashboardRoute }}" class="resort-bubble">Dashboard</a>
        @else
            <a href="{{ route('register') }}" class="resort-bubble">{{ \App\Helpers\ThemeHelper::getThemeContent('nav_cta_label', 'Book Now') }}</a>
        @endauth
        <button class="nav-toggle-btn" id="menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle Navigation Menu"><i class="fa-solid fa-bars"></i></button>
    </div>
</header>

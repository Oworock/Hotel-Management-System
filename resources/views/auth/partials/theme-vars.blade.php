@php
    $activeTheme = \App\Helpers\ThemeHelper::getActiveTheme();
    $themeColors = $activeTheme?->colors ?? [];
    $primary = $themeColors['primary'] ?? \App\Models\Setting::getValue('primary_color', '#6e44ff');
    $secondary = $themeColors['secondary'] ?? \App\Models\Setting::getValue('secondary_color', '#f44496');
    $accent = $themeColors['accent'] ?? '#10B981';
    $authBackground = \App\Models\Setting::getValue('auth_background_image', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1800');
@endphp
<meta name="color-scheme" content="dark light">
<script>
    (function() {
        const storedTheme = localStorage.getItem('theme');
        const initialTheme = storedTheme === 'light' || storedTheme === 'dark' ? storedTheme : 'dark';
        document.documentElement.setAttribute('data-theme', initialTheme);
    })();
</script>
<style>
    :root {
        --primary: {{ $primary }};
        --primary-light: color-mix(in srgb, {{ $primary }} 85%, white);
        --primary-glow: color-mix(in srgb, {{ $primary }} 25%, transparent);
        --secondary: {{ $secondary }};
        --secondary-light: color-mix(in srgb, {{ $secondary }} 85%, white);
        --secondary-glow: color-mix(in srgb, {{ $secondary }} 25%, transparent);
        --accent: {{ $accent }};
        --auth-bg-image: url('{{ $authBackground }}');
    }
</style>

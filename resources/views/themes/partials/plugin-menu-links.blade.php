@php
    $showPluginLinks = \App\Helpers\ThemeHelper::getThemeVariable('content.show_plugin_links');
    $showPluginLinks = $showPluginLinks === null ? true : filter_var($showPluginLinks, FILTER_VALIDATE_BOOLEAN);
    $tuckShopEnabled = \App\Models\Setting::getValue('ecommerce_tuck_shop_enabled', '1') === '1';
    $restaurantEnabled = \App\Models\Setting::getValue('ecommerce_restaurant_enabled', '1') === '1';
@endphp
@if($showPluginLinks && $tuckShopEnabled && Route::has('shop.index'))
    <a href="{{ route('shop.index') }}" class="{{ Request::is('shop*') ? 'active' : '' }}">{{ \App\Models\Setting::getValue('ecommerce_tuck_shop_name', 'Tuck Shop') }}</a>
@endif
@if($showPluginLinks && $restaurantEnabled && Route::has('restaurant.index'))
    <a href="{{ route('restaurant.index') }}" class="{{ Request::is('restaurant*') ? 'active' : '' }}">{{ \App\Models\Setting::getValue('ecommerce_restaurant_name', 'Restaurant') }}</a>
@endif
@if($showPluginLinks && view()->exists('multi_hotel.public-selector'))
    @include('multi_hotel.public-selector')
@endif

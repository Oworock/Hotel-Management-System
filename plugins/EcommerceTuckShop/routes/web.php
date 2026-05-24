<?php

use Illuminate\Support\Facades\Route;
use Plugins\EcommerceTuckShop\Http\Controllers\ShopController;
use Plugins\EcommerceTuckShop\Http\Controllers\KitchenController;
use Plugins\EcommerceTuckShop\Http\Controllers\AdminProductOrderController;
use Plugins\EcommerceTuckShop\Http\Controllers\EcommerceAdminController;

Route::middleware('web')->group(function () {
    // E-commerce Shop & Restaurant Routes (Public)
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/restaurant', [ShopController::class, 'restaurantIndex'])->name('restaurant.index');
    Route::post('/cart/add', [ShopController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [ShopController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [ShopController::class, 'checkoutForm'])->name('checkout');
    Route::post('/checkout', [ShopController::class, 'checkout'])->name('checkout.process');

    Route::middleware('auth')->group(function () {
        // Admin prefix group
        Route::prefix('admin')->group(function () {
            // Tuck Shop & Restaurant Catalog & Orders Management
            Route::middleware('role:admin,super_admin,tuck_shop_manager,restaurant_manager,manage_tuck_shop,manage_restaurant')->group(function () {
                Route::get('/ecommerce/dashboard', [EcommerceAdminController::class, 'dashboard'])->name('admin.ecommerce.dashboard');
                Route::get('/ecommerce/analytics', [EcommerceAdminController::class, 'analytics'])->name('admin.ecommerce.analytics');
                Route::get('/ecommerce/settings', [EcommerceAdminController::class, 'settingsForm'])->name('admin.ecommerce.settings');
                Route::post('/ecommerce/settings', [EcommerceAdminController::class, 'updateSettings'])->name('admin.ecommerce.settings.update');

                Route::get('/products', [AdminProductOrderController::class, 'products'])->name('admin.products');
                Route::post('/products', [AdminProductOrderController::class, 'storeProduct'])->name('admin.products.store');
                Route::post('/products/{product}/update', [AdminProductOrderController::class, 'updateProduct'])->name('admin.products.update');
                Route::post('/products/{product}/delete', [AdminProductOrderController::class, 'deleteProduct'])->name('admin.products.delete');
                Route::get('/orders', [AdminProductOrderController::class, 'orders'])->name('admin.orders');
                Route::get('/orders/{order}/document', [AdminProductOrderController::class, 'orderDocument'])->name('admin.orders.document');
                Route::post('/orders/{order}/status', [AdminProductOrderController::class, 'updateOrderStatus'])->name('admin.orders.status');
            });
        });

        // Kitchen Dashboard Routes
        Route::middleware('role:kitchen_manager,super_admin,admin')->prefix('kitchen')->group(function () {
            Route::get('/dashboard', [KitchenController::class, 'dashboard'])->name('kitchen.dashboard');
            Route::post('/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.orders.status');
        });
    });
});

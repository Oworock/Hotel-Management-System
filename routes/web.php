<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;

// Installer & Licensing Routes
Route::get('/install', [InstallController::class, 'showInstall'])->name('install');
Route::post('/install', [InstallController::class, 'processInstall']);
Route::post('/install/test-db', [InstallController::class, 'testConnection']);
Route::get('/pirated-copy', function () {
    return view('errors.pirated');
})->name('pirated.copy');

// Landing welcome page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/rooms', [HomeController::class, 'rooms'])->name('rooms');
Route::get('/pages/{slug}', [HomeController::class, 'showPage'])->name('frontend.page');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Super Admin Dashboard Routes
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('super_admin.dashboard');
        Route::get('/settings', [SuperAdminController::class, 'settingsForm'])->name('super_admin.settings');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('super_admin.settings.update');
        Route::post('/settings/sms-test', [SuperAdminController::class, 'testSms'])->name('super_admin.settings.sms_test');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('super_admin.users');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('super_admin.users.store');
        Route::post('/users/{user}/toggle', [SuperAdminController::class, 'toggleUserStatus'])->name('super_admin.users.toggle');
        Route::post('/users/{user}/update', [SuperAdminController::class, 'updateUser'])->name('super_admin.users.update');
        
        // Slider CRUD
        Route::post('/slider', [SuperAdminController::class, 'sliderStore'])->name('super_admin.slider.store');
        Route::post('/slider/{slide}/update', [SuperAdminController::class, 'sliderUpdate'])->name('super_admin.slider.update');
        Route::post('/slider/{slide}/delete', [SuperAdminController::class, 'sliderDelete'])->name('super_admin.slider.delete');

        // Pages CRUD
        Route::post('/pages', [SuperAdminController::class, 'storePage'])->name('super_admin.pages.store');
        Route::post('/pages/{page}/update', [SuperAdminController::class, 'updatePage'])->name('super_admin.pages.update');
        Route::post('/pages/{page}/delete', [SuperAdminController::class, 'deletePage'])->name('super_admin.pages.delete');

        // Coupons CRUD
        Route::post('/coupons', [SuperAdminController::class, 'storeCoupon'])->name('super_admin.coupons.store');
        Route::post('/coupons/{coupon}/update', [SuperAdminController::class, 'updateCoupon'])->name('super_admin.coupons.update');
        Route::post('/coupons/{coupon}/delete', [SuperAdminController::class, 'deleteCoupon'])->name('super_admin.coupons.delete');

        // Impersonation
        Route::post('/users/{user}/impersonate', [SuperAdminController::class, 'impersonate'])->name('super_admin.users.impersonate');

        // Plugins Management
        Route::get('/plugins', [App\Http\Controllers\PluginController::class, 'index'])->name('super_admin.plugins');
        Route::post('/plugins/upload', [App\Http\Controllers\PluginController::class, 'upload'])->name('super_admin.plugins.upload');
        Route::post('/plugins/{plugin}/toggle', [App\Http\Controllers\PluginController::class, 'toggle'])->name('super_admin.plugins.toggle');
        Route::post('/plugins/{plugin}/delete', [App\Http\Controllers\PluginController::class, 'delete'])->name('super_admin.plugins.delete');
        Route::post('/plugins/{plugin}/qa', [App\Http\Controllers\PluginController::class, 'runQA'])->name('super_admin.plugins.qa');
        Route::get('/plugins/{name}/logo', [App\Http\Controllers\PluginController::class, 'logo'])->name('super_admin.plugins.logo');

        // Developer Portal
        Route::get('/developer', [App\Http\Controllers\DeveloperController::class, 'index'])->name('super_admin.developer');
        Route::post('/developer/keys', [App\Http\Controllers\DeveloperController::class, 'storeKey'])->name('super_admin.developer.keys.store');
        Route::delete('/developer/keys/{id}', [App\Http\Controllers\DeveloperController::class, 'destroyKey'])->name('super_admin.developer.keys.destroy');
        Route::post('/developer/webhooks', [App\Http\Controllers\DeveloperController::class, 'storeWebhook'])->name('super_admin.developer.webhooks.store');
        Route::post('/developer/webhooks/{id}/toggle', [App\Http\Controllers\DeveloperController::class, 'toggleWebhook'])->name('super_admin.developer.webhooks.toggle');
        Route::delete('/developer/webhooks/{id}', [App\Http\Controllers\DeveloperController::class, 'destroyWebhook'])->name('super_admin.developer.webhooks.destroy');
        Route::post('/developer/logs/{id}/retry', [App\Http\Controllers\DeveloperController::class, 'retryLog'])->name('super_admin.developer.logs.retry');
    });

    Route::post('/impersonate/leave', [SuperAdminController::class, 'leaveImpersonate'])->name('impersonate.leave');

    // Admin Dashboard Routes
    Route::middleware('role:admin,super_admin,receptionist,tuck_shop_manager,restaurant_manager,manage_rooms,manage_bookings,manage_guests,manage_payments,manage_reports,manage_users,manage_settings,manage_tuck_shop,manage_restaurant,manage_channel_manager')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard')->middleware('role:admin,super_admin');

        // Rooms & Types Management
        Route::middleware('role:admin,super_admin,receptionist,manage_rooms')->group(function () {
            Route::get('/rooms', [AdminController::class, 'rooms'])->name('admin.rooms');
            Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('admin.rooms.store');
            Route::post('/rooms/{room}/update', [AdminController::class, 'updateRoom'])->name('admin.rooms.update');
            Route::post('/rooms/{room}/status', [AdminController::class, 'updateRoomStatus'])->name('admin.rooms.status');
            Route::post('/rooms/{room}/delete', [AdminController::class, 'deleteRoom'])->name('admin.rooms.delete');
            Route::post('/room-types', [AdminController::class, 'storeRoomType'])->name('admin.room_types.store');
            Route::post('/room-types/{roomType}/update', [AdminController::class, 'updateRoomType'])->name('admin.room_types.update');
            Route::post('/room-types/{roomType}/delete', [AdminController::class, 'deleteRoomType'])->name('admin.room_types.delete');
        });

        // Bookings Management
        Route::middleware('role:admin,super_admin,receptionist,manage_bookings')->group(function () {
            Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
            Route::post('/bookings/walkin', [AdminController::class, 'storeWalkInBooking'])->name('admin.bookings.walkin');
            Route::post('/bookings/{booking}/check-in', [AdminController::class, 'checkInGuest'])->name('admin.bookings.check_in');
            Route::post('/bookings/{booking}/check-out', [AdminController::class, 'checkOutGuest'])->name('admin.bookings.check_out');
            Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('admin.bookings.cancel');
        });

        // Guests Management
        Route::middleware('role:admin,super_admin,receptionist,manage_guests')->group(function () {
            Route::get('/guests', [AdminController::class, 'guests'])->name('admin.guests');
            Route::post('/guests/{guest}/blacklist', [AdminController::class, 'toggleGuestBlacklist'])->name('admin.guests.blacklist');
        });

        // Payments Management
        Route::middleware('role:admin,super_admin,manage_payments')->group(function () {
            Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
            Route::post('/payments/{payment}/refund', [AdminController::class, 'refundPayment'])->name('admin.payments.refund');
        });

        // Reports Management
        Route::middleware('role:admin,super_admin,manage_reports')->group(function () {
            Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
            Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('admin.reports.export');
        });

        // Users & Shifts Management
        Route::middleware('role:admin,super_admin,manage_users')->group(function () {
            Route::get('/shifts', [AdminController::class, 'shifts'])->name('admin.shifts');
            Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
            Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
            Route::post('/users/{user}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
        });

        // Settings & Frontend Content
        Route::middleware('role:admin,super_admin,manage_settings')->group(function () {
            Route::get('/settings', [AdminController::class, 'settingsForm'])->name('admin.settings');
            Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
            Route::post('/frontend/minor-content', [AdminController::class, 'updateMinorContent'])->name('admin.frontend.minor_content');
            Route::post('/frontend/slider/{slide}', [AdminController::class, 'updateSlideMinor'])->name('admin.frontend.slider.minor');
            Route::post('/slider', [AdminController::class, 'sliderStore'])->name('admin.slider.store');
            Route::post('/slider/{slide}/update', [AdminController::class, 'sliderUpdate'])->name('admin.slider.update');
            Route::post('/slider/{slide}/delete', [AdminController::class, 'sliderDelete'])->name('admin.slider.delete');
        });
    });

    // Receptionist Dashboard Routes
    Route::middleware('role:receptionist')->prefix('receptionist')->group(function () {
        Route::get('/dashboard', [ReceptionistController::class, 'index'])->name('receptionist.dashboard');
        Route::get('/bookings', [ReceptionistController::class, 'bookings'])->name('receptionist.bookings');
        Route::post('/bookings/walkin', [ReceptionistController::class, 'storeWalkInBooking'])->name('receptionist.bookings.walkin');
        Route::post('/bookings/{booking}/check-in', [ReceptionistController::class, 'checkInGuest'])->name('receptionist.bookings.check_in');
        Route::post('/bookings/{booking}/check-out', [ReceptionistController::class, 'checkOutGuest'])->name('receptionist.bookings.check_out');
        Route::post('/bookings/{booking}/cancel', [ReceptionistController::class, 'cancelBooking'])->name('receptionist.bookings.cancel');
        Route::post('/bookings/{booking}/pay', [ReceptionistController::class, 'recordPayment'])->name('receptionist.bookings.pay');
        Route::get('/guests', [ReceptionistController::class, 'guests'])->name('receptionist.guests');
        Route::get('/rooms', [ReceptionistController::class, 'rooms'])->name('receptionist.rooms');
        Route::post('/rooms/{room}/status', [ReceptionistController::class, 'updateRoomStatus'])->name('receptionist.rooms.status');
    });

    // Staff Dashboard Routes
    Route::middleware('role:staff')->prefix('staff')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'index'])->name('staff.dashboard');
        Route::post('/clock-in', [StaffController::class, 'clockIn'])->name('staff.clock_in');
        Route::post('/clock-out', [StaffController::class, 'clockOut'])->name('staff.clock_out');
        Route::post('/rooms/{room}/housekeeping', [StaffController::class, 'updateHousekeeping'])->name('staff.rooms.housekeeping');
        Route::get('/bookings', [StaffController::class, 'bookings'])->name('staff.bookings');
        Route::post('/bookings/{booking}/check-in', [StaffController::class, 'checkInGuest'])->name('staff.bookings.check_in');
        Route::post('/bookings/{booking}/check-out', [StaffController::class, 'checkOutGuest'])->name('staff.bookings.check_out');
    });

    // Customer Dashboard Routes
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'index'])->name('customer.dashboard');
        Route::get('/book/{roomType}', [CustomerController::class, 'showBookingForm'])->name('customer.book');
        Route::post('/book', [CustomerController::class, 'processBooking'])->name('customer.book.process');
        Route::post('/coupons/verify', [CustomerController::class, 'verifyCoupon'])->name('customer.coupons.verify');
        Route::get('/payment/{booking}', [CustomerController::class, 'showPaymentForm'])->name('customer.payment');
        Route::post('/payment/{booking}', [CustomerController::class, 'processPayment'])->name('customer.payment.process');
        Route::get('/bookings', [CustomerController::class, 'bookings'])->name('customer.bookings');
        Route::post('/bookings/{booking}/check-in', [CustomerController::class, 'selfCheckIn'])->name('customer.bookings.check_in');
        Route::post('/bookings/{booking}/check-out', [CustomerController::class, 'selfCheckOut'])->name('customer.bookings.check_out');
    });
});

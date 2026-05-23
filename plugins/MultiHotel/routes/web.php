<?php

use Illuminate\Support\Facades\Route;
use Plugins\MultiHotel\Http\Controllers\MultiHotelController;

Route::middleware(['web', 'auth', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/hotels', [MultiHotelController::class, 'index'])->name('super_admin.hotels');
    Route::post('/hotels', [MultiHotelController::class, 'store'])->name('super_admin.hotels.store');
    Route::post('/hotels/{hotel}/update', [MultiHotelController::class, 'update'])->name('super_admin.hotels.update');
    Route::post('/hotels/select', [MultiHotelController::class, 'select'])->name('super_admin.hotels.select');
});

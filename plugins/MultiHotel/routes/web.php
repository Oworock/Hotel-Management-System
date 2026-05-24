<?php

use Illuminate\Support\Facades\Route;
use Plugins\MultiHotel\Http\Controllers\MultiHotelController;

Route::middleware('web')->group(function () {
    Route::post('/hotels/select', [MultiHotelController::class, 'selectPublic'])->name('hotels.select_public');
});

Route::middleware(['web', 'auth', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/hotels', [MultiHotelController::class, 'index'])->name('super_admin.hotels');
    Route::post('/hotels', [MultiHotelController::class, 'store'])->name('super_admin.hotels.store');
    Route::post('/hotels/{hotel}/update', [MultiHotelController::class, 'update'])->name('super_admin.hotels.update');
    Route::post('/hotels/select', [MultiHotelController::class, 'select'])->name('super_admin.hotels.select');
    Route::get('/hotels/{hotel}/manage', [MultiHotelController::class, 'manage'])->name('super_admin.hotels.manage');
    Route::post('/hotels/{hotel}/room-types', [MultiHotelController::class, 'storeRoomType'])->name('super_admin.hotels.room_types.store');
    Route::post('/hotels/{hotel}/rooms', [MultiHotelController::class, 'storeRoom'])->name('super_admin.hotels.rooms.store');
    Route::post('/hotels/{hotel}/rooms/{room}/status', [MultiHotelController::class, 'updateRoomStatus'])->name('super_admin.hotels.rooms.status');
    Route::delete('/hotels/{hotel}/rooms/{room}', [MultiHotelController::class, 'deleteRoom'])->name('super_admin.hotels.rooms.delete');
    Route::post('/hotels/{hotel}/staff', [MultiHotelController::class, 'assignStaff'])->name('super_admin.hotels.staff.assign');
    Route::delete('/hotels/{hotel}/staff/{user}', [MultiHotelController::class, 'removeStaff'])->name('super_admin.hotels.staff.remove');
});

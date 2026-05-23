<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\PaymentApiController;

Route::middleware('api.auth')->prefix('v1')->group(function () {
    // Rooms API
    Route::get('/rooms', [RoomApiController::class, 'index']);
    Route::get('/rooms/{id}', [RoomApiController::class, 'show']);
    Route::put('/rooms/{id}/status', [RoomApiController::class, 'updateStatus']);

    // Bookings API
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::post('/bookings', [BookingApiController::class, 'store']);
    Route::put('/bookings/{id}/check-in', [BookingApiController::class, 'checkIn']);
    Route::put('/bookings/{id}/check-out', [BookingApiController::class, 'checkOut']);

    // Payments API
    Route::get('/payments', [PaymentApiController::class, 'index']);
    Route::post('/payments', [PaymentApiController::class, 'store']);
});

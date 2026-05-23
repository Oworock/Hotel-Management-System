<?php

use Illuminate\Support\Facades\Route;
use Plugins\ChannelManager\Http\Controllers\ChannelManagerController;

Route::middleware('web')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::middleware('role:admin,super_admin,manage_channel_manager')->group(function () {
                Route::get('/channel-manager', [ChannelManagerController::class, 'index'])->name('admin.channel_manager.index');
                Route::post('/channel-manager/settings', [ChannelManagerController::class, 'saveSettings'])->name('admin.channel_manager.settings');
                Route::post('/channel-manager/mappings', [ChannelManagerController::class, 'addMapping'])->name('admin.channel_manager.mappings.store');
                Route::post('/channel-manager/mappings/{mapping}/delete', [ChannelManagerController::class, 'deleteMapping'])->name('admin.channel_manager.mappings.delete');
                Route::post('/channel-manager/sync', [ChannelManagerController::class, 'manualSync'])->name('admin.channel_manager.sync');
            });
        });
    });
});

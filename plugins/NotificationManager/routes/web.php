<?php

use Illuminate\Support\Facades\Route;
use Plugins\NotificationManager\Http\Controllers\NotificationManagerController;

Route::middleware(['web', 'auth', 'role:super_admin,admin'])->prefix('admin/notifications')->name('notification_manager.')->group(function () {
    Route::get('/', [NotificationManagerController::class, 'index'])->name('index');
    Route::post('/settings', [NotificationManagerController::class, 'updateSettings'])->name('settings.update');
    Route::post('/test', [NotificationManagerController::class, 'sendTest'])->name('test');
    Route::post('/templates/{template}', [NotificationManagerController::class, 'updateTemplate'])->name('templates.update');
    Route::post('/recipients', [NotificationManagerController::class, 'storeRecipient'])->name('recipients.store');
    Route::post('/recipients/{recipient}', [NotificationManagerController::class, 'updateRecipient'])->name('recipients.update');
    Route::post('/recipients/{recipient}/delete', [NotificationManagerController::class, 'deleteRecipient'])->name('recipients.delete');
});

Route::middleware(['web', 'auth'])->prefix('dashboard/notifications')->name('notification_manager.')->group(function () {
    Route::get('/inbox', [NotificationManagerController::class, 'inbox'])->name('inbox');
    Route::get('/inbox/{notification}/open', [NotificationManagerController::class, 'openInboxItem'])->name('inbox.open');
    Route::post('/read', [NotificationManagerController::class, 'markInboxRead'])->name('inbox.read');
});

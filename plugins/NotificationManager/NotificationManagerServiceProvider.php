<?php

namespace Plugins\NotificationManager;

use App\Models\Booking;
use App\Models\StaffShift;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Plugins\NotificationManager\Observers\BookingObserver;
use Plugins\NotificationManager\Observers\StaffShiftObserver;
use Plugins\NotificationManager\Observers\UserObserver;
use Plugins\NotificationManager\Services\NotificationManagerService;

class NotificationManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotificationManagerService::class, function () {
            return new NotificationManagerService();
        });
    }

    public function boot(): void
    {
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        if (is_dir(__DIR__ . '/resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'notification-manager');
            $this->app['view']->addLocation(__DIR__ . '/resources/views');
        }

        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }

        if (!app()->runningInConsole() || app()->environment('testing')) {
            $this->registerObservers();
        }

        try {
            if (Schema::hasTable('notification_manager_templates')) {
                app(NotificationManagerService::class)->ensureDefaultConfiguration();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function registerObservers(): void
    {
        if (class_exists(Booking::class)) {
            Booking::observe(BookingObserver::class);
        }

        if (class_exists(StaffShift::class)) {
            StaffShift::observe(StaffShiftObserver::class);
        }

        if (class_exists(User::class)) {
            User::observe(UserObserver::class);
        }
    }
}

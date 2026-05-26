<?php

namespace Plugins\HRManagement;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class HRManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
    }

    protected function registerRoutes()
    {
        // Admin/Super Admin routes
        Route::middleware(['web', 'auth', 'role:admin|super_admin'])
            ->prefix('admin/hr')
            ->name('admin.hr.')
            ->group($this->basePath('routes/web.php'));

        // Staff routes - these are registered under admin.hr prefix but with staff middleware
        Route::middleware(['web', 'auth', 'role:staff'])
            ->prefix('admin/hr')
            ->name('admin.hr.')
            ->group(function () {
                require $this->basePath('routes/staff.php');
            });
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(
            $this->basePath('resources/views'),
            'hrmanagement'
        );
    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom($this->basePath('database/migrations'));
    }

    protected function basePath($path = '')
    {
        return __DIR__ . ($path ? '/' . $path : '');
    }
}

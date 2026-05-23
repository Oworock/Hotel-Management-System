<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Plugin;
use Illuminate\Support\Facades\Schema;

class PluginServiceProvider extends ServiceProvider
{
    public static $testDisabled = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Dynamic PSR-4 Autoloading for Plugins
        $loader = null;
        foreach (spl_autoload_functions() as $autoloader) {
            if (is_array($autoloader) && isset($autoloader[0]) && $autoloader[0] instanceof \Composer\Autoload\ClassLoader) {
                $loader = $autoloader[0];
                break;
            }
        }

        if (!$loader) {
            $loader = require base_path('vendor/autoload.php');
        }

        // Map Plugins\ namespace to the plugins/ folder in root
        $loader->addPsr4('Plugins\\', base_path('plugins'));

        // 2. Fetch and register enabled plugins
        try {
            $testing = app()->environment('testing');
            $testDisabled = self::$testDisabled || config('plugins.test_disabled');

            if ($testing) {
                if (!$testDisabled) {
                    $providerClass = 'Plugins\\EcommerceTuckShop\\EcommerceTuckShopServiceProvider';
                    if (class_exists($providerClass)) {
                        $this->app->register($providerClass);
                    }
                    $channelManagerProvider = 'Plugins\\ChannelManager\\ChannelManagerServiceProvider';
                    if (class_exists($channelManagerProvider)) {
                        $this->app->register($channelManagerProvider);
                    }
                    $multiHotelProvider = 'Plugins\\MultiHotel\\MultiHotelServiceProvider';
                    if (class_exists($multiHotelProvider)) {
                        $this->app->register($multiHotelProvider);
                    }
                }
            } else {
                $manifestPath = base_path('bootstrap/cache/enabled_plugins.json');
                if (file_exists($manifestPath)) {
                    $activePlugins = json_decode(file_get_contents($manifestPath), true);
                    if (is_array($activePlugins)) {
                        foreach ($activePlugins as $plugin) {
                            $provider = $plugin['service_provider'] ?? null;
                            if ($provider && class_exists($provider)) {
                                $this->app->register($provider);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence exceptions during migration or database setup
            \Illuminate\Support\Facades\Log::error('PluginServiceProvider register error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    /**
     * Writes enabled plugins to a JSON manifest cache file.
     */
    public static function writeManifest(): void
    {
        try {
            $filename = app()->environment('testing') ? 'enabled_plugins_test.json' : 'enabled_plugins.json';
            $manifestPath = base_path('bootstrap/cache/' . $filename);
            $activePlugins = [];

            if (Schema::hasTable('plugins')) {
                $activePlugins = Plugin::where('is_enabled', true)
                    ->get(['name', 'service_provider'])
                    ->toArray();
            }

            $dir = dirname($manifestPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($manifestPath, json_encode($activePlugins, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PluginServiceProvider writeManifest error: ' . $e->getMessage());
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $manifestPath = base_path('bootstrap/cache/enabled_plugins.json');
        if (!app()->environment('testing') && !file_exists($manifestPath)) {
            self::writeManifest();
            if (file_exists($manifestPath)) {
                $activePlugins = json_decode(file_get_contents($manifestPath), true);
                if (is_array($activePlugins)) {
                    foreach ($activePlugins as $plugin) {
                        $provider = $plugin['service_provider'] ?? null;
                        if ($provider && class_exists($provider)) {
                            $this->app->register($provider);
                        }
                    }
                }
            }
        }
    }
}

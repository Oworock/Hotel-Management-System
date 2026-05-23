<?php

namespace Plugins\ChannelManager;

use Illuminate\Support\ServiceProvider;

class ChannelManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load routes
        if (file_exists(__DIR__ . '/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        }

        // 2. Register view path
        if (is_dir(__DIR__ . '/resources/views')) {
            $this->app['view']->addLocation(__DIR__ . '/resources/views');
        }

        // 3. Register migrations
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }

        // 4. Register API & Webhook documentation
        if (class_exists(\App\Services\DeveloperDocsRegistry::class)) {
            \App\Services\DeveloperDocsRegistry::registerApi('Channel Manager', [
                'method' => 'POST',
                'uri' => '/api/v1/channel-manager/sync',
                'description' => 'Trigger a synchronization of rates, inventory, and bookings with all connected OTA channels.',
                'headers' => [
                    'Authorization' => 'Bearer <api_token>'
                ],
                'parameters' => [
                    'channels' => ['type' => 'array', 'required' => false, 'description' => 'List of channels to sync (e.g. ["booking.com", "expedia"]). Defaults to all.'],
                ],
                'response' => [
                    'status' => 'success',
                    'message' => 'Channel synchronization initiated.',
                    'job_id' => 'sync_job_998a12c',
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerWebhook('Channel Manager Events', [
                'event' => 'channel.sync.completed',
                'description' => 'Fires when a channel inventory/booking sync operation completes successfully.',
                'payload' => [
                    'event' => 'channel.sync.completed',
                    'timestamp' => '2026-05-23T00:00:00Z',
                    'data' => [
                        'job_id' => 'sync_job_998a12c',
                        'channels_synced' => ['booking.com', 'expedia'],
                        'bookings_imported' => 3,
                        'inventory_updated_rooms' => 12,
                    ]
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerWebhook('Channel Manager Events', [
                'event' => 'channel.rate.updated',
                'description' => 'Fires when room rates or availability overrides are pushed to channels.',
                'payload' => [
                    'event' => 'channel.rate.updated',
                    'timestamp' => '2026-05-23T00:02:00Z',
                    'data' => [
                        'room_type_id' => 2,
                        'base_price' => 150.00,
                        'channels' => ['booking.com', 'expedia'],
                    ]
                ]
            ]);
        }
    }
}

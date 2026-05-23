<?php

namespace Plugins\MultiHotel;

use Illuminate\Support\ServiceProvider;
use Plugins\MultiHotel\Scopes\HotelScope;
use Plugins\MultiHotel\Models\Hotel;

class MultiHotelServiceProvider extends ServiceProvider
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

        // 4. Dynamic relations, scopes, and observers for core models
        $scopedModels = [
            \App\Models\User::class,
            \App\Models\RoomType::class,
            \App\Models\Room::class,
            \App\Models\Booking::class,
            \App\Models\Payment::class,
            \App\Models\Coupon::class,
            \App\Models\StaffShift::class,
        ];

        foreach ($scopedModels as $modelClass) {
            if (class_exists($modelClass)) {
                // Add 'hotel' relation dynamically
                $modelClass::resolveRelationUsing('hotel', function ($model) {
                    return $model->belongsTo(Hotel::class, 'hotel_id');
                });

                // Add global HotelScope dynamically
                $modelClass::addGlobalScope(new HotelScope());

                // Set hotel_id automatically on creation
                $modelClass::creating(function ($model) {
                    if (empty($model->hotel_id)) {
                        $activeHotelId = null;
                        if (request()->hasHeader('X-Hotel-ID')) {
                            $activeHotelId = request()->header('X-Hotel-ID');
                        } elseif (request()->hasSession() && session()->has('active_hotel_id')) {
                            $activeHotelId = session('active_hotel_id');
                        }

                        if (auth()->check()) {
                            $user = auth()->user();
                            if ($user->role === 'super_admin') {
                                if ($activeHotelId !== null && $activeHotelId !== '') {
                                    $model->hotel_id = $activeHotelId;
                                }
                            } else {
                                $model->hotel_id = $user->hotel_id;
                            }
                        } elseif ($activeHotelId !== null && $activeHotelId !== '') {
                            $model->hotel_id = $activeHotelId;
                        }
                    }
                });
            }
        }

        // 5. Register API & Webhook documentation
        if (class_exists(\App\Services\DeveloperDocsRegistry::class)) {
            \App\Services\DeveloperDocsRegistry::registerApi('Multi-Hotel Management', [
                'method' => 'GET',
                'uri' => '/api/v1/hotels',
                'description' => 'Retrieve list of hotel properties managed under this platform (Super Admin only).',
                'headers' => [
                    'Authorization' => 'Bearer <api_token>'
                ],
                'parameters' => [],
                'response' => [
                    'status' => 'success',
                    'data' => [
                        [
                            'id' => 1,
                            'name' => 'Aetheria Grand Hotel',
                            'address' => '123 Paradise Boulevard',
                            'phone' => '+1234567890',
                        ],
                        [
                            'id' => 2,
                            'name' => 'Aetheria Beach Resort',
                            'address' => '456 Coastline Road',
                            'phone' => '+1987654321',
                        ]
                    ]
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerWebhook('Multi-Hotel Events', [
                'event' => 'hotel.created',
                'description' => 'Fires when a new hotel property is registered under the platform.',
                'payload' => [
                    'event' => 'hotel.created',
                    'timestamp' => '2026-05-23T00:00:00Z',
                    'data' => [
                        'hotel_id' => 3,
                        'name' => 'Aetheria Mountain Lodge',
                        'address' => '789 Peak Highway',
                        'phone' => '+1122334455',
                    ]
                ]
            ]);
        }
    }
}

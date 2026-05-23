<?php

namespace Plugins\EcommerceTuckShop;

use Illuminate\Support\ServiceProvider;

class EcommerceTuckShopServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register controllers or model bindings if any
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

        // 2. Register view path (adds to fallback lookup locations)
        if (is_dir(__DIR__ . '/resources/views')) {
            $this->app['view']->addLocation(__DIR__ . '/resources/views');
        }

        // 3. Register migrations
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }

        // 4. Register API & Webhook documentation
        if (class_exists(\App\Services\DeveloperDocsRegistry::class)) {
            \App\Services\DeveloperDocsRegistry::registerApi('Ecommerce & Tuck Shop', [
                'method' => 'GET',
                'uri' => '/api/v1/tuckshop/products',
                'description' => 'Retrieve list of products available in the hotel shop/restaurant.',
                'headers' => [
                    'Authorization' => 'Bearer <api_token>'
                ],
                'parameters' => [
                    'category' => ['type' => 'string', 'required' => false, 'description' => 'Filter by category (tuck_shop, restaurant)'],
                ],
                'response' => [
                    'status' => 'success',
                    'data' => [
                        [
                            'id' => 1,
                            'name' => 'Coca Cola 330ml',
                            'category' => 'tuck_shop',
                            'price' => 2.50,
                            'stock' => 50,
                        ]
                    ]
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerApi('Ecommerce & Tuck Shop', [
                'method' => 'POST',
                'uri' => '/api/v1/tuckshop/orders',
                'description' => 'Place an order for products from the shop/restaurant.',
                'headers' => [
                    'Authorization' => 'Bearer <api_token>'
                ],
                'parameters' => [
                    'items' => ['type' => 'array', 'required' => true, 'description' => 'List of items to order. Each item must have product_id and quantity.'],
                    'room_id' => ['type' => 'integer', 'required' => false, 'description' => 'Optionally bill to a specific room.'],
                ],
                'response' => [
                    'status' => 'success',
                    'message' => 'Order placed successfully.',
                    'data' => [
                        'id' => 45,
                        'total' => 12.50,
                        'status' => 'pending',
                    ]
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerWebhook('Ecommerce & Tuck Shop Events', [
                'event' => 'tuckshop.order.placed',
                'description' => 'Fires immediately when an order is placed from the shop or restaurant.',
                'payload' => [
                    'event' => 'tuckshop.order.placed',
                    'timestamp' => '2026-05-23T00:00:00Z',
                    'data' => [
                        'order_id' => 45,
                        'total' => 12.50,
                        'status' => 'pending',
                        'items' => [
                            ['product_id' => 1, 'name' => 'Coca Cola 330ml', 'qty' => 1, 'price' => 2.50]
                        ]
                    ]
                ]
            ]);

            \App\Services\DeveloperDocsRegistry::registerWebhook('Ecommerce & Tuck Shop Events', [
                'event' => 'tuckshop.order.completed',
                'description' => 'Fires when an order is marked as completed/fulfilled by the tuck shop staff.',
                'payload' => [
                    'event' => 'tuckshop.order.completed',
                    'timestamp' => '2026-05-23T00:05:00Z',
                    'data' => [
                        'order_id' => 45,
                        'status' => 'completed',
                    ]
                ]
            ]);
        }
    }
}

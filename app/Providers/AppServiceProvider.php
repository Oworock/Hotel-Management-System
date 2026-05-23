<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\DeveloperDocsRegistry;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!app()->runningUnitTests() && !file_exists(base_path('.env'))) {
            config([
                'app.key' => 'base64:yk+tCcZ2xJh7L0yA5JvWn7L8yA5JvWn7L8yA5JvWn7I=',
                'session.driver' => 'file',
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => database_path('database.sqlite'),
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                \Illuminate\Support\Facades\View::share('navPages', \App\Models\Page::where('is_active', true)->get());
            } else {
                \Illuminate\Support\Facades\View::share('navPages', collect());
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\View::share('navPages', collect());
        }

        $this->registerCoreDocs();
    }

    protected function registerCoreDocs(): void
    {
        // Rooms API
        DeveloperDocsRegistry::registerApi('Rooms', [
            'method' => 'GET',
            'uri' => '/api/v1/rooms',
            'description' => 'Retrieve a list of rooms in the hotel. Supports filtering by status and room type.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>',
                'X-Hotel-ID' => '(Optional) Scopes the query to a specific hotel when Multi-Hotel plugin is active.'
            ],
            'parameters' => [
                'status' => ['type' => 'string', 'required' => false, 'description' => 'Filter by status (e.g. available, occupied, dirty, maintenance)'],
                'room_type_id' => ['type' => 'integer', 'required' => false, 'description' => 'Filter by room type ID'],
            ],
            'response' => [
                'status' => 'success',
                'data' => [
                    [
                        'id' => 1,
                        'room_number' => '101',
                        'room_type_id' => 2,
                        'status' => 'available',
                        'housekeeping_status' => 'clean',
                        'created_at' => '2026-05-23T00:00:00Z',
                    ]
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Rooms', [
            'method' => 'GET',
            'uri' => '/api/v1/rooms/{id}',
            'description' => 'Retrieve details of a specific room.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>'
            ],
            'parameters' => [],
            'response' => [
                'status' => 'success',
                'data' => [
                    'id' => 1,
                    'room_number' => '101',
                    'room_type_id' => 2,
                    'status' => 'available',
                    'housekeeping_status' => 'clean',
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Rooms', [
            'method' => 'PUT',
            'uri' => '/api/v1/rooms/{id}/status',
            'description' => 'Update the status of a specific room (e.g., set to maintenance or dirty).',
            'headers' => [
                'Authorization' => 'Bearer <api_token>'
            ],
            'parameters' => [
                'status' => ['type' => 'string', 'required' => true, 'description' => 'New status (available, occupied, dirty, maintenance)'],
            ],
            'response' => [
                'status' => 'success',
                'message' => 'Room status updated successfully.',
                'data' => [
                    'id' => 1,
                    'room_number' => '101',
                    'status' => 'maintenance',
                ]
            ]
        ]);

        // Bookings API
        DeveloperDocsRegistry::registerApi('Bookings', [
            'method' => 'GET',
            'uri' => '/api/v1/bookings',
            'description' => 'Retrieve list of guest bookings. Supports filtering by status.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>',
                'X-Hotel-ID' => '(Optional) Scopes the query to a specific hotel when Multi-Hotel plugin is active.'
            ],
            'parameters' => [
                'status' => ['type' => 'string', 'required' => false, 'description' => 'Filter by status (pending, confirmed, checked_in, checked_out, cancelled)'],
            ],
            'response' => [
                'status' => 'success',
                'data' => [
                    [
                        'id' => 12,
                        'customer_id' => 4,
                        'room_id' => 3,
                        'check_in' => '2026-05-25',
                        'check_out' => '2026-05-28',
                        'total_price' => 350.00,
                        'status' => 'confirmed',
                    ]
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Bookings', [
            'method' => 'POST',
            'uri' => '/api/v1/bookings',
            'description' => 'Create a new guest booking.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>',
                'X-Hotel-ID' => '(Optional) Asserts the booking belongs to this hotel when Multi-Hotel plugin is active.'
            ],
            'parameters' => [
                'customer_id' => ['type' => 'integer', 'required' => true, 'description' => 'Associated customer/user ID'],
                'room_type_id' => ['type' => 'integer', 'required' => true, 'description' => 'Target room type ID'],
                'check_in' => ['type' => 'string (date)', 'required' => true, 'description' => 'Check in date (YYYY-MM-DD)'],
                'check_out' => ['type' => 'string (date)', 'required' => true, 'description' => 'Check out date (YYYY-MM-DD)'],
                'coupon_code' => ['type' => 'string', 'required' => false, 'description' => 'Optional coupon discount code'],
            ],
            'response' => [
                'status' => 'success',
                'message' => 'Booking created successfully.',
                'data' => [
                    'id' => 13,
                    'customer_id' => 4,
                    'room_type_id' => 2,
                    'check_in' => '2026-05-25',
                    'check_out' => '2026-05-28',
                    'total_price' => 315.00, // Coupon applied
                    'status' => 'pending',
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Bookings', [
            'method' => 'PUT',
            'uri' => '/api/v1/bookings/{id}/check-in',
            'description' => 'Perform check-in action for a booking.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>'
            ],
            'parameters' => [
                'room_id' => ['type' => 'integer', 'required' => true, 'description' => 'Assign specific room ID for check-in']
            ],
            'response' => [
                'status' => 'success',
                'message' => 'Guest checked in successfully.',
                'data' => [
                    'id' => 12,
                    'status' => 'checked_in',
                    'room_id' => 3
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Bookings', [
            'method' => 'PUT',
            'uri' => '/api/v1/bookings/{id}/check-out',
            'description' => 'Perform check-out action for an active guest stay.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>'
            ],
            'parameters' => [],
            'response' => [
                'status' => 'success',
                'message' => 'Guest checked out successfully.',
                'data' => [
                    'id' => 12,
                    'status' => 'checked_out',
                ]
            ]
        ]);

        // Payments API
        DeveloperDocsRegistry::registerApi('Payments', [
            'method' => 'GET',
            'uri' => '/api/v1/payments',
            'description' => 'Retrieve a list of payment records.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>',
                'X-Hotel-ID' => '(Optional) Scopes the query to a specific hotel when Multi-Hotel plugin is active.'
            ],
            'parameters' => [],
            'response' => [
                'status' => 'success',
                'data' => [
                    [
                        'id' => 5,
                        'booking_id' => 12,
                        'amount' => 350.00,
                        'payment_method' => 'credit_card',
                        'status' => 'completed',
                        'created_at' => '2026-05-23T00:05:00Z',
                    ]
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerApi('Payments', [
            'method' => 'POST',
            'uri' => '/api/v1/payments',
            'description' => 'Record a new payment for a booking.',
            'headers' => [
                'Authorization' => 'Bearer <api_token>'
            ],
            'parameters' => [
                'booking_id' => ['type' => 'integer', 'required' => true, 'description' => 'Associated booking ID'],
                'amount' => ['type' => 'numeric', 'required' => true, 'description' => 'Payment amount in USD'],
                'payment_method' => ['type' => 'string', 'required' => true, 'description' => 'Payment method (e.g., cash, credit_card, bank_transfer)'],
            ],
            'response' => [
                'status' => 'success',
                'message' => 'Payment recorded successfully.',
                'data' => [
                    'id' => 6,
                    'booking_id' => 12,
                    'amount' => 350.00,
                    'payment_method' => 'credit_card',
                    'status' => 'completed',
                ]
            ]
        ]);

        // Core Webhooks
        DeveloperDocsRegistry::registerWebhook('Core Events', [
            'event' => 'booking.created',
            'description' => 'Fires immediately when a new booking is created (online reservation or walk-in).',
            'payload' => [
                'event' => 'booking.created',
                'timestamp' => '2026-05-23T00:00:00Z',
                'data' => [
                    'booking_id' => 12,
                    'customer_id' => 4,
                    'room_type_id' => 2,
                    'check_in' => '2026-05-25',
                    'check_out' => '2026-05-28',
                    'total_price' => 350.00,
                    'status' => 'pending'
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerWebhook('Core Events', [
            'event' => 'booking.updated',
            'description' => 'Fires when any aspect of a booking is updated (status change, date modification, room assignment).',
            'payload' => [
                'event' => 'booking.updated',
                'timestamp' => '2026-05-23T00:01:00Z',
                'data' => [
                    'booking_id' => 12,
                    'status' => 'checked_in',
                    'room_id' => 3,
                    'total_price' => 350.00,
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerWebhook('Core Events', [
            'event' => 'payment.recorded',
            'description' => 'Fires when a guest payment is successfully recorded.',
            'payload' => [
                'event' => 'payment.recorded',
                'timestamp' => '2026-05-23T00:02:00Z',
                'data' => [
                    'payment_id' => 5,
                    'booking_id' => 12,
                    'amount' => 350.00,
                    'payment_method' => 'credit_card',
                    'status' => 'completed'
                ]
            ]
        ]);

        DeveloperDocsRegistry::registerWebhook('Core Events', [
            'event' => 'room.status_updated',
            'description' => 'Fires when a room\'s physical status or housekeeping status changes.',
            'payload' => [
                'event' => 'room.status_updated',
                'timestamp' => '2026-05-23T00:03:00Z',
                'data' => [
                    'room_id' => 3,
                    'room_number' => '101',
                    'status' => 'occupied',
                    'housekeeping_status' => 'dirty'
                ]
            ]
        ]);
    }
}

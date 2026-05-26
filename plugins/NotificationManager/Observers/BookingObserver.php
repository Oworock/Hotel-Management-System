<?php

namespace Plugins\NotificationManager\Observers;

use App\Models\Booking;
use Plugins\NotificationManager\Services\NotificationManagerService;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        app(NotificationManagerService::class)->notify(
            'booking.created',
            app(NotificationManagerService::class)->payloadForBooking($booking)
        );
    }

    public function updated(Booking $booking): void
    {
        if (!$booking->wasChanged('status')) {
            return;
        }

        $eventKey = match ($booking->status) {
            'confirmed' => 'booking.confirmed',
            'checked_in' => 'booking.checked_in',
            'checked_out' => 'booking.checked_out',
            'cancelled' => 'booking.cancelled',
            default => null,
        };

        if ($eventKey) {
            app(NotificationManagerService::class)->notify(
                $eventKey,
                app(NotificationManagerService::class)->payloadForBooking($booking)
            );
        }
    }
}

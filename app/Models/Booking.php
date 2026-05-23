<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['customer_id', 'room_id', 'check_in_date', 'check_out_date', 'total_price', 'coupon_code', 'discount_amount', 'status', 'payment_status', 'loyalty_points_redeemed', 'loyalty_points_earned', 'loyalty_discount_amount', 'addons'])]
class Booking extends Model
{
    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'total_price' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted()
    {
        static::created(function ($booking) {
            \App\Models\WebhookSubscription::dispatchEvent('booking.created', [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'room_id' => $booking->room_id,
                'check_in' => $booking->check_in_date ? $booking->check_in_date->format('Y-m-d') : null,
                'check_out' => $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : null,
                'total_price' => $booking->total_price,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
            ]);
        });

        static::updated(function ($booking) {
            \App\Models\WebhookSubscription::dispatchEvent('booking.updated', [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'room_id' => $booking->room_id,
                'check_in' => $booking->check_in_date ? $booking->check_in_date->format('Y-m-d') : null,
                'check_out' => $booking->check_out_date ? $booking->check_out_date->format('Y-m-d') : null,
                'total_price' => $booking->total_price,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
            ]);
        });
    }
}

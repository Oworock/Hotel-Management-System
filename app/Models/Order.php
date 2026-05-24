<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'type', 'status', 'delivery_mode', 'room_number', 'subtotal', 'tax', 'discount', 'total', 'payment_method', 'special_instructions', 'ordered_at', 'completed_at'];
    protected $casts = ['ordered_at' => 'datetime', 'completed_at' => 'datetime'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

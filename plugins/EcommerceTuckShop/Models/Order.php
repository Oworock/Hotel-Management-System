<?php

namespace Plugins\EcommerceTuckShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\User;
use App\Models\Booking;

#[Fillable([
    'user_id', 'customer_name', 'customer_email', 'customer_phone', 
    'delivery_type', 'delivery_details', 'total_price', 
    'payment_method', 'payment_status', 'status', 'booking_id'
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'customer_name' => 'encrypted',
            'customer_email' => 'encrypted',
            'customer_phone' => 'encrypted',
            'total_price' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['room_number', 'room_type_id', 'status'])]
class Room extends Model
{
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected static function booted()
    {
        static::updated(function ($room) {
            if ($room->isDirty('status')) {
                \App\Models\WebhookSubscription::dispatchEvent('room.status_updated', [
                    'room_id' => $room->id,
                    'room_number' => $room->room_number,
                    'status' => $room->status,
                ]);
            }
        });
    }
}

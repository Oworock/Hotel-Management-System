<?php

namespace Plugins\MultiHotel\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'address',
        'map_embed_url',
        'phone',
        'email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function roomTypes()
    {
        return $this->hasMany(\App\Models\RoomType::class, 'hotel_id');
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\Room::class, 'hotel_id');
    }

    public function staff()
    {
        return $this->hasMany(\App\Models\User::class, 'hotel_id');
    }
}

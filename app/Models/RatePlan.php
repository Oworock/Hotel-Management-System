<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatePlan extends Model
{
    protected $fillable = ['room_type_id', 'name', 'description', 'base_price', 'min_price', 'max_price', 'booking_type', 'start_date', 'end_date', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'base_price' => 'decimal:2', 'min_price' => 'decimal:2', 'max_price' => 'decimal:2'];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}

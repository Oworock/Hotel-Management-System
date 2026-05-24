<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $fillable = ['title', 'description', 'type', 'value', 'valid_from', 'valid_until', 'max_bookings', 'used_count', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'value' => 'decimal:2', 'valid_from' => 'date', 'valid_until' => 'date'];
}

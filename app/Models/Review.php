<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['booking_id', 'guest_id', 'overall_rating', 'cleanliness_rating', 'comfort_rating', 'service_rating', 'value_rating', 'comment', 'is_verified', 'is_published', 'would_recommend', 'verified_at'];
    protected $casts = ['is_verified' => 'boolean', 'is_published' => 'boolean', 'would_recommend' => 'boolean', 'verified_at' => 'datetime'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'discount_percentage', 'is_active', 'expire_at'])]
class Coupon extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expire_at' => 'date',
            'discount_percentage' => 'decimal:2',
        ];
    }

    /**
     * Check if the coupon is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expire_at && $this->expire_at->isPast()) {
            return false;
        }

        return true;
    }
}

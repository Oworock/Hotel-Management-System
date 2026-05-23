<?php

namespace Plugins\EcommerceTuckShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'price', 'image_path', 'type', 'category', 'is_available'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

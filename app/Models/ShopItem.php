<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $fillable = ['name', 'description', 'price', 'cost_price', 'image', 'category', 'sku', 'stock', 'reorder_level', 'is_available', 'is_featured', 'is_active', 'order'];
    protected $casts = ['is_available' => 'boolean', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
}

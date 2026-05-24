<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantItem extends Model
{
    protected $fillable = ['name', 'description', 'price', 'cost_price', 'image', 'category', 'calories', 'allergens', 'preparation_time', 'is_vegetarian', 'is_vegan', 'is_spicy', 'is_available', 'is_featured', 'is_active', 'order'];
    protected $casts = ['is_vegetarian' => 'boolean', 'is_vegan' => 'boolean', 'is_spicy' => 'boolean', 'is_available' => 'boolean', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
}

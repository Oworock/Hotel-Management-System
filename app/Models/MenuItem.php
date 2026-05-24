<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['type', 'name', 'description', 'price', 'image', 'category', 'calories', 'allergens', 'is_available', 'is_featured', 'preparation_time', 'is_active', 'order'];
    protected $casts = ['is_available' => 'boolean', 'is_featured' => 'boolean', 'is_active' => 'boolean'];
}

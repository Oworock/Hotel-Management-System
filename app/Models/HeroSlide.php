<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'subtitle', 'image_path', 'button_text', 'button_link', 'sort_order', 'is_active'])]
class HeroSlide extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}

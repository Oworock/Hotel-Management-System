<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'preview_image', 'colors', 'settings', 'is_active', 'is_default', 'author', 'version'];
    protected $casts = ['colors' => 'array', 'settings' => 'array', 'is_active' => 'boolean', 'is_default' => 'boolean'];
}

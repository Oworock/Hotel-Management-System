<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'description', 'image', 'order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}

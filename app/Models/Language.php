<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'code', 'flag_emoji', 'is_active', 'is_default', 'order'];
    protected $casts = ['is_active' => 'boolean', 'is_default' => 'boolean'];
}

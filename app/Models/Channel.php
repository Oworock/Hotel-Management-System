<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['name', 'type', 'api_key', 'api_secret', 'settings', 'is_active', 'sync_enabled'];
    protected $casts = ['api_key' => 'encrypted', 'api_secret' => 'encrypted', 'settings' => 'array', 'is_active' => 'boolean', 'sync_enabled' => 'boolean'];
    protected $hidden = ['api_secret'];
}

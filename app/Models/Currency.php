<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'symbol', 'exchange_rate', 'is_active', 'is_default'];
    protected $casts = ['is_active' => 'boolean', 'is_default' => 'boolean', 'exchange_rate' => 'decimal:6'];
}

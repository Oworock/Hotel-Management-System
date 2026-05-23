<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'description', 'base_price', 'capacity', 'amenities', 'images'])]
class RoomType extends Model
{
    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'amenities' => 'array',
            'images' => 'array',
        ];
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}

<?php

namespace Plugins\ChannelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'api_key', 'api_secret', 'hotel_id', 'is_connected'])]
class OtaChannel extends Model
{
    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
        ];
    }
}

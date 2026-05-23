<?php

namespace Plugins\ChannelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\RoomType;

#[Fillable(['room_type_id', 'channel_name', 'ota_room_id', 'rate_multiplier'])]
class ChannelRoomMapping extends Model
{
    protected function casts(): array
    {
        return [
            'room_type_id' => 'integer',
            'rate_multiplier' => 'decimal:2',
        ];
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}

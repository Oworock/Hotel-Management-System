<?php

namespace Plugins\ChannelManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['channel_name', 'sync_type', 'status', 'message', 'payload'])]
class ChannelSyncLog extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}

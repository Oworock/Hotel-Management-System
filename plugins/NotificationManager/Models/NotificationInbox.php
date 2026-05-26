<?php

namespace Plugins\NotificationManager\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationInbox extends Model
{
    protected $table = 'notification_manager_inbox';

    protected $fillable = [
        'user_id',
        'event_key',
        'title',
        'message',
        'action_url',
        'payload',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];
}

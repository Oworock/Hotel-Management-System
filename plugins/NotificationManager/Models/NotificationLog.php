<?php

namespace Plugins\NotificationManager\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notification_manager_logs';

    protected $fillable = [
        'event_key',
        'channel',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'status',
        'message',
        'payload',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];
}

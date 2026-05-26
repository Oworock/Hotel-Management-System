<?php

namespace Plugins\NotificationManager\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    protected $table = 'notification_manager_recipients';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'event_keys',
        'wants_email',
        'wants_sms',
        'is_active',
    ];

    protected $casts = [
        'email' => 'encrypted',
        'phone' => 'encrypted',
        'event_keys' => 'array',
        'wants_email' => 'boolean',
        'wants_sms' => 'boolean',
        'is_active' => 'boolean',
    ];
}

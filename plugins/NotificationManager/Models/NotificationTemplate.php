<?php

namespace Plugins\NotificationManager\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $table = 'notification_manager_templates';

    protected $fillable = [
        'event_key',
        'name',
        'email_subject',
        'email_body',
        'sms_body',
        'send_email',
        'send_sms',
        'is_active',
        'recipient_roles',
    ];

    protected $casts = [
        'send_email' => 'boolean',
        'send_sms' => 'boolean',
        'is_active' => 'boolean',
        'recipient_roles' => 'array',
    ];
}

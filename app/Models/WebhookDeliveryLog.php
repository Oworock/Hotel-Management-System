<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['webhook_subscription_id', 'event', 'url', 'payload', 'response_status', 'response_body', 'error', 'duration_ms', 'created_at'])]
class WebhookDeliveryLog extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(WebhookSubscription::class, 'webhook_subscription_id');
    }
}

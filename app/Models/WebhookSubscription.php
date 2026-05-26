<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'url', 'secret', 'events', 'is_active'])]
class WebhookSubscription extends Model
{
    protected function casts(): array
    {
        return [
            'secret' => 'encrypted',
            'events' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function logs()
    {
        return $this->hasMany(WebhookDeliveryLog::class);
    }

    public static function dispatchEvent(string $event, array $payload): void
    {
        try {
            $subscriptions = self::where('is_active', true)->get();
            foreach ($subscriptions as $sub) {
                if (in_array($event, $sub->events) || in_array('*', $sub->events)) {
                    \App\Jobs\SendWebhookJob::dispatch($sub->id, $event, $payload);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed dispatching webhook event {$event}: " . $e->getMessage());
        }
    }
}

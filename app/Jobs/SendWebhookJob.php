<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\WebhookDeliveryLog;
use App\Models\WebhookSubscription;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscriptionId;
    protected $event;
    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(int $subscriptionId, string $event, array $payload)
    {
        $this->subscriptionId = $subscriptionId;
        $this->event = $event;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscription = WebhookSubscription::find($this->subscriptionId);

        if (!$subscription || !$subscription->is_active) {
            return;
        }

        $url = $subscription->url;
        $secret = $subscription->secret;

        $jsonPayload = json_encode([
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
            'data' => $this->payload
        ]);

        $signature = hash_hmac('sha256', $jsonPayload, $secret);

        $startTime = microtime(true);
        $responseStatus = null;
        $responseBody = null;
        $errorMessage = null;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'User-Agent' => 'StayFlow-Webhook-Dispatcher/1.0',
                'X-Signature' => 'sha256=' . $signature,
                'X-Event' => $this->event,
            ])->timeout(10)->post($url, json_decode($jsonPayload, true));

            $responseStatus = $response->status();
            $responseBody = substr($response->body(), 0, 10000); // Truncate if extremely large
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $durationMs = (int) ((microtime(true) - $startTime) * 1000);

        WebhookDeliveryLog::create([
            'webhook_subscription_id' => $subscription->id,
            'event' => $this->event,
            'url' => $url,
            'payload' => json_decode($jsonPayload, true),
            'response_status' => $responseStatus,
            'response_body' => $responseBody,
            'error' => $errorMessage,
            'duration_ms' => $durationMs,
            'created_at' => now(),
        ]);
    }
}

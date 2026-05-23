<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\WebhookSubscription;
use App\Models\WebhookDeliveryLog;
use App\Models\User;
use App\Services\DeveloperDocsRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    public function index()
    {
        $apiTokens = ApiToken::with('user')->get();
        $webhooks = WebhookSubscription::all();
        $logs = WebhookDeliveryLog::with('subscription')->orderBy('created_at', 'desc')->take(100)->get();
        
        // List users suitable for API token assignment
        $users = User::whereIn('role', ['super_admin', 'admin', 'receptionist', 'staff'])->get();

        $registeredApis = DeveloperDocsRegistry::getApis();
        $registeredWebhooks = DeveloperDocsRegistry::getWebhooks();

        return view('super_admin.developer', compact(
            'apiTokens',
            'webhooks',
            'logs',
            'users',
            'registeredApis',
            'registeredWebhooks'
        ));
    }

    public function storeKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $plainToken = 'sf_live_' . Str::random(40);

        ApiToken::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
            'token' => $plainToken,
        ]);

        return redirect()->back()->with('new_api_key', [
            'name' => $request->name,
            'token' => $plainToken,
        ])->with('success', 'API Key generated successfully.');
    }

    public function destroyKey($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->delete();

        return redirect()->back()->with('success', 'API Key revoked successfully.');
    }

    public function storeWebhook(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array',
            'events.*' => 'string',
        ]);

        $secret = 'whsec_' . Str::random(24);

        WebhookSubscription::create([
            'name' => $request->name,
            'url' => $request->url,
            'secret' => $secret,
            'events' => $request->events,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Webhook subscription created successfully.');
    }

    public function toggleWebhook($id)
    {
        $webhook = WebhookSubscription::findOrFail($id);
        $webhook->is_active = !$webhook->is_active;
        $webhook->save();

        $status = $webhook->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Webhook subscription {$status} successfully.");
    }

    public function destroyWebhook($id)
    {
        $webhook = WebhookSubscription::findOrFail($id);
        $webhook->delete();

        return redirect()->back()->with('success', 'Webhook subscription deleted successfully.');
    }

    public function retryLog($id)
    {
        $log = WebhookDeliveryLog::findOrFail($id);

        if (!$log->webhook_subscription_id) {
            return redirect()->back()->with('error', 'Cannot retry this webhook log: subscription no longer exists.');
        }

        \App\Jobs\SendWebhookJob::dispatch(
            $log->webhook_subscription_id,
            $log->event,
            $log->payload['data'] ?? []
        );

        return redirect()->back()->with('success', 'Webhook dispatch retried successfully.');
    }
}
